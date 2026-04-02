<?php

namespace App\Core;

use App\Models\UserModel;

/**
 * WhatsApp Notification Service via Twilio
 * 
 * Официальная интеграция через Twilio WhatsApp Business API
 * Документация: https://www.twilio.com/docs/whatsapp
 * 
 * Поддерживает отправку:
 * - На номер по умолчанию из .env
 * - Всем подписчикам (пользователям с whatsapp_notifications = 1)
 */
class WhatsApp
{
    private ?string $accountSid;
    private ?string $authToken;
    private string $fromNumber;
    private string $toNumber;
    private bool $enabled;
    private ?UserModel $userModel;

    public function __construct(?UserModel $userModel = null)
    {
        $this->accountSid = $_ENV['TWILIO_ACCOUNT_SID'] ?? null;
        $this->authToken = $_ENV['TWILIO_AUTH_TOKEN'] ?? null;
        $this->fromNumber = $_ENV['TWILIO_WHATSAPP_FROM'] ?? ''; // Ваш Twilio WhatsApp номер
        $this->toNumber = $_ENV['TWILIO_WHATSAPP_TO'] ?? '';     // WhatsApp номер по умолчанию
        $this->userModel = $userModel;
        $this->enabled = !empty($this->accountSid) && !empty($this->authToken) && !empty($this->fromNumber);
    }

    /**
     * Отправить сообщение в WhatsApp на конкретный номер
     */
    public function sendToNumber(string $to, string $message): bool
    {
        if (!$this->enabled) {
            error_log('WhatsApp notifications disabled - check Twilio configuration');
            return false;
        }

        // Нормализуем номер
        $to = $this->normalizePhone($to);

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";
        
        $data = [
            'From' => "whatsapp:{$this->fromNumber}",
            'To' => "whatsapp:{$to}",
            'Body' => $message
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "{$this->accountSid}:{$this->authToken}",
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("WhatsApp cURL error: {$error}");
            return false;
        }

        if ($httpCode !== 201) {
            error_log("WhatsApp Twilio error (HTTP {$httpCode}): {$response}");
            return false;
        }

        return true;
    }

    /**
     * Отправить сообщение в WhatsApp (на номер по умолчанию или подписчикам)
     */
    public function sendMessage(string $message): bool
    {
        // Сначала отправляем подписчикам
        $subscribersResult = $this->sendToSubscribers($message);
        
        // Если есть номер по умолчанию - отправляем и туда
        if (!empty($this->toNumber)) {
            $this->sendToNumber($this->toNumber, $message);
        }
        
        return $subscribersResult;
    }

    /**
     * Отправить сообщение всем подписчикам
     */
    public function sendToSubscribers(string $message): bool
    {
        if (!$this->userModel) {
            $this->userModel = new UserModel();
        }

        $subscribers = $this->userModel->getWhatsAppSubscribers();
        
        if (empty($subscribers)) {
            error_log('No WhatsApp subscribers found');
            return true; // Не ошибка, просто некому отправлять
        }

        $success = true;
        foreach ($subscribers as $subscriber) {
            $phone = $subscriber['whatsapp_phone'] ?? $subscriber['phone'] ?? null;
            
            if (!$phone) {
                continue;
            }

            $result = $this->sendToNumber($phone, $message);
            if (!$result) {
                $success = false;
                error_log("Failed to send WhatsApp to subscriber {$subscriber['id']}");
            }
            
            // Небольшая пауза между отправками
            usleep(200000); // 200ms
        }

        return $success;
    }

    /**
     * Нормализовать номер телефона для WhatsApp
     */
    private function normalizePhone(string $phone): string
    {
        // Убираем всё кроме цифр и плюса
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Если нет плюса в начале - добавляем
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

    /**
     * Отправить уведомление о новом заказе
     */
    public function notifyNewOrder(array $order): bool
    {
        // items может быть уже массивом или JSON строкой
        $items = $order['items'] ?? [];
        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }
        $itemsList = '';
        $total = 0;
        
        foreach ($items as $item) {
            $subtotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            $total += $subtotal;
            $itemsList .= "• {$item['name']} x{$item['quantity']} = " . number_format($subtotal, 0, '', ' ') . " ₸\n";
        }

        $delivery = !empty($order['delivery_included']) ? 500 : 0;
        $grandTotal = $total + $delivery;

        $message = "🛒 *НОВЫЙ ЗАКАЗ #{$order['id']}*\n\n";
        $message .= "📍 *Адрес:* {$order['address']}\n";
        
        if (!empty($order['phone'])) {
            $message .= "📞 *Телефон:* {$order['phone']}\n";
        }
        
        $message .= "\n📦 *Товары:*\n{$itemsList}\n";
        
        if ($delivery > 0) {
            $message .= "🚗 *Доставка:* " . number_format($delivery, 0, '', ' ') . " ₸\n";
        }
        
        $message .= "💰 *Итого:* " . number_format($grandTotal, 0, '', ' ') . " ₸\n\n";
        $message .= "⏰ " . date('d.m.Y H:i');

        return $this->sendMessage($message);
    }

    /**
     * Отправить уведомление об изменении статуса заказа
     */
    public function notifyOrderStatus(int $orderId, string $status, ?string $address = null): bool
    {
        $statusEmoji = [
            'СОЗДАН' => '🆕',
            'СБОРКА' => '📦',
            'ОЖИДАНИЕ_КУРЬЕРА' => '🚶',
            'В_ПУТИ' => '🚗',
            'ДОСТАВЛЕН' => '✅',
            'ОТМЕНЕН' => '❌'
        ];

        $statusNames = [
            'СОЗДАН' => 'Создан',
            'СБОРКА' => 'На сборке',
            'ОЖИДАНИЕ_КУРЬЕРА' => 'Ожидает курьера',
            'В_ПУТИ' => 'В пути',
            'ДОСТАВЛЕН' => 'Доставлен',
            'ОТМЕНЕН' => 'Отменён'
        ];

        $emoji = $statusEmoji[$status] ?? '📋';
        $statusName = $statusNames[$status] ?? $status;

        $message = "{$emoji} *Заказ #{$orderId}*\n";
        $message .= "Статус: *{$statusName}*\n";
        
        if ($address) {
            $message .= "📍 Адрес: {$address}\n";
        }
        
        $message .= "⏰ " . date('d.m.Y H:i');

        return $this->sendMessage($message);
    }

    /**
     * Отправить уведомление о новом курьере
     */
    public function notifyNewCourier(string $name, string $phone): bool
    {
        $message = "🏃 *НОВЫЙ КУРЬЕР*\n\n";
        $message .= "👤 Имя: {$name}\n";
        $message .= "📞 Телефон: {$phone}\n";
        $message .= "⏰ " . date('d.m.Y H:i');

        return $this->sendMessage($message);
    }

    /**
     * Отправить кастомное уведомление
     */
    public function notify(string $title, string $body): bool
    {
        $message = "📢 *{$title}*\n\n{$body}\n\n⏰ " . date('d.m.Y H:i');
        return $this->sendMessage($message);
    }

    /**
     * Проверить доступность сервиса
     */
    public function isAvailable(): bool
    {
        return $this->enabled;
    }

    /**
     * Получить информацию о конфигурации (для отладки)
     */
    public function getConfigStatus(): array
    {
        return [
            'enabled' => $this->enabled,
            'has_account_sid' => !empty($this->accountSid),
            'has_auth_token' => !empty($this->authToken),
            'has_from_number' => !empty($this->fromNumber),
            'has_to_number' => !empty($this->toNumber)
        ];
    }
}