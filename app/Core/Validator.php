<?php

namespace App\Core;

/**
 * Класс валидации данных
 */
class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }
    
    /**
     * Статический метод создания валидатора
     */
    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }
    
    /**
     * Выполнить валидацию
     */
    public function validate(): bool
    {
        $this->errors = [];
        
        foreach ($this->rules as $field => $fieldRules) {
            $rulesArray = is_array($fieldRules) ? $fieldRules : explode(',', $fieldRules);
            $value = $this->data[$field] ?? null;
            
            foreach ($rulesArray as $rule) {
                $rule = trim($rule);
                $params = [];
                
                // Проверяем, есть ли параметры в правиле (например, min:6)
                if (strpos($rule, ':') !== false) {
                    $parts = explode(':', $rule, 2);
                    $rule = $parts[0];
                    $params = [$parts[1]];
                }
                
                $errorMessage = $this->applyRule($field, $value, $rule, $params);
                if ($errorMessage !== null) {
                    $this->errors[$field][] = $errorMessage;
                    break; // Показываем только первую ошибку для поля
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Применить правило валидации
     */
    private function applyRule(string $field, mixed $value, string $rule, array $params): ?string
    {
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    return "Поле обязательно для заполнения";
                }
                break;
                
            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "Поле должно быть корректным email";
                }
                break;
                
            case 'phone':
                if ($value !== null && $value !== '') {
                    $phone = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($phone) < 10 || strlen($phone) > 15) {
                        return "Поле должно быть корректным номером телефона";
                    }
                }
                break;
                
            case 'min':
                $minLength = (int)($params[0] ?? 1);
                if ($value !== null && strlen((string)$value) < $minLength) {
                    return "Поле должно содержать минимум {$minLength} символов";
                }
                break;
                
            case 'max':
                $maxLength = (int)($params[0] ?? 255);
                if ($value !== null && strlen((string)$value) > $maxLength) {
                    return "Поле должно содержать максимум {$maxLength} символов";
                }
                break;
                
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    return "Поле должно быть числом";
                }
                break;
                
            case 'integer':
                if ($value !== null && $value !== '') {
                    // Проверяем, что значение является целым числом (включая 0)
                    if (!is_numeric($value) || floor(abs(floatval($value))) != abs(floatval($value))) {
                        return "Поле должно быть целым числом";
                    }
                }
                break;
                
            case 'positive':
                if ($value !== null && (float)$value <= 0) {
                    return "Поле должно быть положительным числом";
                }
                break;
                
            case 'min_value':
                $minVal = (float)($params[0] ?? 0);
                if ($value !== null && $value !== '' && (float)$value < $minVal) {
                    return "Значение должно быть не менее {$minVal}";
                }
                break;
                
            case 'max_value':
                $maxVal = (float)($params[0] ?? PHP_INT_MAX);
                if ($value !== null && $value !== '' && (float)$value > $maxVal) {
                    return "Значение должно быть не более {$maxVal}";
                }
                break;
        }
        
        return null;
    }
    
    /**
     * Получить все ошибки
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Получить первую ошибку
     */
    public function getFirstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return null;
    }
    
    /**
     * Получить ошибки для конкретного поля
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Проверить, есть ли ошибки
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Проверить, прошла ли валидация
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }
}