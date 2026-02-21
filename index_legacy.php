<?php
session_start();
define('DATA_DIR', __DIR__);

function readJson($file) {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return $data ?: [];
}

function writeJson($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getNextId($data) {
    if (empty($data)) return 1;
    return max(array_column($data, 'id')) + 1;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/' || $path === '/index.php') {
    echo renderTemplate('home', []);
} elseif ($path === '/catalog') {
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'courier') { header('Location: /courier'); exit; }
    $products = readJson(DATA_DIR . '/data/products.json');
    $categories = readJson(DATA_DIR . '/data/categories.json');
    
    // Enrich products with category names
    $categoryMap = [];
    foreach ($categories as $cat) {
        $categoryMap[$cat['id']] = $cat['name'];
    }
    foreach ($products as &$product) {
        if (!empty($product['category_id']) && isset($categoryMap[$product['category_id']])) {
            $product['category_name'] = $categoryMap[$product['category_id']];
        }
    }
    
    echo renderTemplate('catalog', ['products' => $products, 'categories' => $categories]);
} elseif ($path === '/cart') {
    // Enrich cart items with product data
    $cart = $_SESSION['cart'] ?? [];
    if (!empty($cart)) {
        $products = readJson(DATA_DIR . '/data/products.json');
        $productMap = [];
        foreach ($products as $product) {
            $productMap[$product['id']] = $product;
        }
        
        foreach ($cart as &$item) {
            if (isset($productMap[$item['id']])) {
                $product = $productMap[$item['id']];
                $item['name'] = $product['name'];
                $item['price'] = $product['price'];
                $item['image_url'] = $product['image_url'] ?? '';
                $item['is_weighted'] = $product['is_weighted'] ?? 0;
                $item['weight_unit'] = $product['weight_unit'] ?? '';
                $item['product_id'] = $product['id'];
            }
        }
    }
    echo renderTemplate('cart', ['cart' => $cart]);
} elseif ($path === '/order') {
    if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
    // Get cart from session
    $cart = $_SESSION['cart'] ?? [];
    if (!empty($cart)) {
        $products = readJson(DATA_DIR . '/data/products.json');
        $productMap = [];
        foreach ($products as $product) {
            $productMap[$product['id']] = $product;
        }
        
        foreach ($cart as &$item) {
            if (isset($productMap[$item['id']])) {
                $product = $productMap[$item['id']];
                $item['name'] = $product['name'];
                $item['price'] = $product['price'];
                $item['image_url'] = $product['image_url'] ?? '';
                $item['is_weighted'] = $product['is_weighted'] ?? 0;
                $item['weight_unit'] = $product['weight_unit'] ?? '';
                $item['product_id'] = $product['id'];
            }
        }
    }
    echo renderTemplate('order', ['cart' => $cart]);
} elseif ($path === '/login') {
    echo renderTemplate('login', []);
} elseif ($path === '/register') {
    echo renderTemplate('register', []);
} elseif ($path === '/profile') {
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'courier') { header('Location: /courier'); exit; }
    if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
    echo renderTemplate('profile', []);
} elseif ($path === '/orders') {
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'courier') { header('Location: /courier'); exit; }
    if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
    echo renderTemplate('orders', []);
} elseif ($path === '/chat') {
    if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'courier') { header('Location: /courier'); exit; }
    echo renderTemplate('chat', []);
} elseif ($path === '/courier') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') { header('Location: /login'); exit; }
    echo renderTemplate('courier', []);
} elseif ($path === '/admin') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { header('Location: /login'); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    foreach ($orders as &$o) { $o['items'] = json_decode($o['items'], true); }
    
    $products = readJson(DATA_DIR . '/data/products.json');
    $categories = readJson(DATA_DIR . '/data/categories.json');
    
    // Enrich products with category names for admin
    $categoryMap = [];
    foreach ($categories as $cat) {
        $categoryMap[$cat['id']] = $cat['name'];
    }
    foreach ($products as &$product) {
        if (!empty($product['category_id']) && isset($categoryMap[$product['category_id']])) {
            $product['category_name'] = $categoryMap[$product['category_id']];
        }
    }
    
    echo renderTemplate('admin', ['orders' => $orders, 'products' => $products, 'categories' => $categories, 'users' => readJson(DATA_DIR . '/data/users.json')]);
} elseif ($path === '/api/auth/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data = $data ?? [];
    
    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        exit;
    }
    
    $users = readJson(DATA_DIR . '/data/users.json');
    $user = null;
    foreach ($users as $u) {
        if ((!empty($u['email']) && $u['email'] == $data['email'] || !empty($u['phone']) && $u['phone'] == $data['email']) && !empty($u['password']) && password_verify($data['password'], $u['password'])) {
            $user = $u; break;
        }
    }
    if ($user) { $_SESSION['user'] = $user; echo json_encode(['success' => true, 'user' => $user, 'redirect' => '/profile']); }
    else { http_response_code(401); echo json_encode(['error' => 'Invalid credentials']); }
} elseif ($path === '/api/auth/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data = $data ?? [];
    
    if (empty($data['name']) || empty($data['phone']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name, phone and password are required']);
        exit;
    }
    
    $users = readJson(DATA_DIR . '/data/users.json');
    if (!empty(array_filter($users, fn($u) => $u['phone'] == $data['phone']))) { http_response_code(400); echo json_encode(['error' => 'Phone exists']); exit; }
    $users[] = ['id' => getNextId($users), 'name' => $data['name'], 'phone' => $data['phone'], 'password' => password_hash($data['password'], PASSWORD_DEFAULT), 'role' => 'user', 'created_at' => date('c')];
    writeJson(DATA_DIR . '/data/users.json', $users);
    echo json_encode(['success' => true]);
} elseif ($path === '/api/products' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $products = readJson(DATA_DIR . '/data/products.json');
    $categories = readJson(DATA_DIR . '/data/categories.json');
    
    // Create category map
    $categoryMap = [];
    foreach ($categories as $cat) {
        $categoryMap[$cat['id']] = $cat['name'];
    }
    
    // Enrich products with category names
    foreach ($products as &$product) {
        if (!empty($product['category_id']) && isset($categoryMap[$product['category_id']])) {
            $product['category_name'] = $categoryMap[$product['category_id']];
        }
    }
    
    if ($cat = $_GET['category'] ?? null) { $products = array_filter($products, fn($p) => $p['category_id'] == $cat); }
    echo json_encode(array_values($products));
} elseif ($path === '/api/cart/add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error' => 'Auth required']); exit; }
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    $productId = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    
    // Validation
    if ($productId === null || !is_numeric($productId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid product_id']);
        exit;
    }
    
    if (!is_numeric($quantity) || $quantity <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid quantity']);
        exit;
    }
    
    $quantity = intval($quantity);
    $productId = intval($productId);
    
    // Check if product already exists in cart and update quantity
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if (intval($item['id']) === $productId) {
            $item['quantity'] = intval($item['quantity']) + $quantity;
            $found = true;
            break;
        }
    }
    
    // If not found, add new item
    if (!$found) {
        $_SESSION['cart'][] = ['id' => $productId, 'quantity' => $quantity];
    }
    
    echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
} elseif ($path === '/api/cart' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error' => 'Auth required']); exit; }
    echo json_encode($_SESSION['cart'] ?? []);
} elseif ($path === '/api/cart/update' && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error' => 'Auth required']); exit; }
    
    $cart = $_SESSION['cart'] ?? [];
    $productId = $data['product_id'] ?? null;
    $newQuantity = $data['quantity'] ?? null;
    
    // Validation
    if ($productId === null || !is_numeric($productId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid product_id']);
        exit;
    }
    
    if ($newQuantity === null || !is_numeric($newQuantity)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid quantity']);
        exit;
    }
    
    if ($newQuantity < 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Quantity cannot be negative']);
        exit;
    }
    
    $productId = intval($productId);
    $newQuantity = intval($newQuantity);
    
    // Find and update the item in cart
    $found = false;
    foreach ($cart as $key => &$item) {
        if (intval($item['id']) === $productId) {
            if ($newQuantity == 0) {
                // Remove item if quantity is 0
                unset($cart[$key]);
            } else {
                $item['quantity'] = $newQuantity;
            }
            $found = true;
            break;
        }
    }
    
    if ($found) {
        $_SESSION['cart'] = array_values($cart); // Re-index array
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found in cart']);
    }
} elseif (preg_match('/^\/api\/cart\/remove\/(\d+)$/', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $productId = intval($matches[1]);
    if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error' => 'Auth required']); exit; }
    
    $cart = $_SESSION['cart'] ?? [];
    
    // Find and remove the product
    foreach ($cart as $key => $item) {
        if (intval($item['id']) === $productId) {
            unset($cart[$key]);
            $_SESSION['cart'] = array_values($cart); // Re-index array
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
            exit;
        }
    }
    
    http_response_code(404);
    echo json_encode(['error' => 'Product not found in cart']);
} elseif ($path === '/api/cart/clear' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error' => 'Auth required']); exit; }
    unset($_SESSION['cart']);
    echo json_encode(['success' => true]);
} elseif ($path === '/api/orders' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error' => 'Auth required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $total = 0;
    foreach ($data['items'] ?? [] as $item) { $total += $item['price'] * $item['quantity']; }
    $delivery = $data['delivery_included'] ? 500 : 0;
    // New status system: CREATED
    $orders[] = [
        'id' => getNextId($orders),
        'user_id' => $_SESSION['user']['id'],
        'items' => json_encode($data['items']),
        'address' => $data['address'],
        'delivery_included' => $data['delivery_included'],
        'delivery_price' => $delivery,
        'total_price' => $total + $delivery,
        'status' => 'СОЗДАН',
        'created_at' => date('c')
    ];
    writeJson(DATA_DIR . '/data/orders.json', $orders);
    unset($_SESSION['cart']);
    $lastOrder = end($orders);
    echo json_encode(['success' => true, 'order_id' => $lastOrder['id']]);
} elseif ($path === '/api/orders' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error' => 'Auth required']); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $userOrders = array_filter($orders, fn($o) => $o['user_id'] == $_SESSION['user']['id']);
    foreach ($userOrders as &$o) { $o['items'] = json_decode($o['items'], true); }
    echo json_encode(array_values($userOrders));
} elseif ($path === '/api/admin/orders' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    foreach ($orders as &$o) { $o['items'] = json_decode($o['items'], true); }
    echo json_encode($orders);
} elseif ($path === '/api/admin/categories' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    echo json_encode(readJson(DATA_DIR . '/data/categories.json'));
} elseif ($path === '/api/admin/categories' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['name'])) { http_response_code(400); echo json_encode(['error' => 'Name required']); exit; }
    $categories = readJson(DATA_DIR . '/data/categories.json');
    $categories[] = ['id' => getNextId($categories), 'name' => $data['name'], 'created_at' => date('c')];
    writeJson(DATA_DIR . '/data/categories.json', $categories);
    echo json_encode(['success' => true]);
} elseif (preg_match('#^/api/admin/categories/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['name'])) { http_response_code(400); echo json_encode(['error' => 'Name required']); exit; }
    $categories = readJson(DATA_DIR . '/data/categories.json');
    foreach ($categories as &$c) { if ($c['id'] == $m[1]) { $c['name'] = $data['name']; break; } }
    writeJson(DATA_DIR . '/data/categories.json', $categories);
    echo json_encode(['success' => true]);
} elseif (preg_match('#^/api/admin/categories/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $categories = readJson(DATA_DIR . '/data/categories.json');
    $categories = array_filter($categories, fn($c) => $c['id'] != $m[1]);
    writeJson(DATA_DIR . '/data/categories.json', array_values($categories));
    echo json_encode(['success' => true]);
} elseif ($path === '/api/admin/products' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    echo json_encode(readJson(DATA_DIR . '/data/products.json'));
} elseif ($path === '/api/admin/products' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['name']) || empty($data['price']) || empty($data['category_id'])) { 
        http_response_code(400); echo json_encode(['error' => 'Name, price, and category_id required']); exit; 
    }
    $products = readJson(DATA_DIR . '/data/products.json');
    $products[] = [
        'id' => getNextId($products), 
        'name' => $data['name'], 
        'description' => $data['description'] ?? '', 
        'price' => floatval($data['price']), 
        'category_id' => intval($data['category_id']), 
        'image_url' => $data['image_url'] ?? '', 
        'is_weighted' => intval($data['is_weighted'] ?? 0), 
        'weight_unit' => $data['weight_unit'] ?? '', 
        'created_at' => date('c')
    ];
    writeJson(DATA_DIR . '/data/products.json', $products);
    echo json_encode(['success' => true]);
} elseif (preg_match('#^/api/admin/products/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    $products = readJson(DATA_DIR . '/data/products.json');
    foreach ($products as &$p) { 
        if ($p['id'] == $m[1]) { 
            $p['name'] = $data['name'] ?? $p['name'];
            $p['price'] = floatval($data['price'] ?? $p['price']);
            $p['description'] = $data['description'] ?? $p['description'];
            $p['category_id'] = intval($data['category_id'] ?? $p['category_id']);
            $p['image_url'] = $data['image_url'] ?? $p['image_url'];
            $p['is_weighted'] = intval($data['is_weighted'] ?? $p['is_weighted']);
            $p['weight_unit'] = $data['weight_unit'] ?? $p['weight_unit'];
            break; 
        } 
    }
    writeJson(DATA_DIR . '/data/products.json', $products);
    echo json_encode(['success' => true]);
} elseif (preg_match('#^/api/admin/products/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $products = readJson(DATA_DIR . '/data/products.json');
    $products = array_filter($products, fn($p) => $p['id'] != $m[1]);
    writeJson(DATA_DIR . '/data/products.json', array_values($products));
    echo json_encode(['success' => true]);
} elseif ($path === '/api/admin/users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $users = readJson(DATA_DIR . '/data/users.json');
    foreach ($users as &$u) { unset($u['password']); }
    echo json_encode($users);
} elseif (preg_match('#^/api/admin/users/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    $users = readJson(DATA_DIR . '/data/users.json');
    foreach ($users as &$u) { 
        if ($u['id'] == $m[1]) { 
            $u['role'] = $data['role'] ?? $u['role'];
            if (!empty($data['name'])) $u['name'] = $data['name'];
            break; 
        } 
    }
    writeJson(DATA_DIR . '/data/users.json', $users);
    echo json_encode(['success' => true]);
} elseif (preg_match('#^/api/admin/users/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $users = readJson(DATA_DIR . '/data/users.json');
    $users = array_filter($users, fn($u) => $u['id'] != $m[1]);
    writeJson(DATA_DIR . '/data/users.json', array_values($users));
    echo json_encode(['success' => true]);
} elseif ($path === '/api/admin/stats' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $users = readJson(DATA_DIR . '/data/users.json');
    $products = readJson(DATA_DIR . '/data/products.json');
    $stats = [
        'total_users' => count($users),
        'total_products' => count($products),
        'total_orders' => count($orders),
        'total_revenue' => array_sum(array_column($orders, 'total_price')),
        'orders_by_status' => array_count_values(array_column($orders, 'status'))
    ];
    echo json_encode($stats);
} elseif (preg_match('#^/api/admin/orders/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    $orders = readJson(DATA_DIR . '/data/orders.json');
    foreach ($orders as &$o) { 
        if ($o['id'] == $m[1]) {
            // Only allow valid status transitions
            $validStatuses = ['СОЗДАН', 'СБОРКА', 'ОЖИДАНИЕ_КУРЬЕРА', 'В_ПУТИ', 'ДОСТАВЛЕН', 'ОТМЕНЕН'];
            if (isset($data['status']) && in_array($data['status'], $validStatuses)) {
                $o['status'] = $data['status'];
                // If rejecting back to WAITING_COURIER, also clear courier_id
                if ($data['status'] === 'ОЖИДАНИЕ_КУРЬЕРА') {
                    $o['courier_id'] = null;
                }
            }
            break;
        }
    }
    writeJson(DATA_DIR . '/data/orders.json', $orders);
    echo json_encode(['success' => true]);
} elseif (preg_match('#^/api/admin/orders/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $orderToArchive = null;
    foreach ($orders as $o) { if ($o['id'] == $m[1]) { $orderToArchive = $o; break; } }
    if ($orderToArchive) {
        $archive = readJson(DATA_DIR . '/data/archive.json');
        $orderToArchive['archived_at'] = date('c');
        $archive[] = $orderToArchive;
        writeJson(DATA_DIR . '/data/archive.json', $archive);
    }
    $orders = array_filter($orders, fn($o) => $o['id'] != $m[1]);
    writeJson(DATA_DIR . '/data/orders.json', array_values($orders));
    echo json_encode(['success' => true]);
} elseif ($path === '/api/admin/archive' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $archive = readJson(DATA_DIR . '/data/archive.json');
    foreach ($archive as &$o) { $o['items'] = json_decode($o['items'], true); }
    echo json_encode($archive);
} elseif (preg_match('#^/api/admin/archive/(\d+)/restore$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $archive = readJson(DATA_DIR . '/data/archive.json');
    $orderToRestore = null;
    foreach ($archive as $o) { if ($o['id'] == $m[1]) { $orderToRestore = $o; break; } }
    if ($orderToRestore) {
        unset($orderToRestore['archived_at']);
        $orders = readJson(DATA_DIR . '/data/orders.json');
        $orders[] = $orderToRestore;
        writeJson(DATA_DIR . '/data/orders.json', $orders);
    }
    $archive = array_filter($archive, fn($o) => $o['id'] != $m[1]);
    writeJson(DATA_DIR . '/data/archive.json', array_values($archive));
    echo json_encode(['success' => true]);
} elseif ($path === '/api/courier/orders' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') { http_response_code(403); echo json_encode(['error' => 'Courier required']); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $courierId = $_SESSION['user']['id'];
    $available = array_filter($orders, fn($o) => (!isset($o['courier_id']) || !$o['courier_id']) && $o['status'] === 'ОЖИДАНИЕ_КУРЬЕРА');
    $current = array_filter($orders, fn($o) => isset($o['courier_id']) && $o['courier_id'] == $courierId && in_array($o['status'], ['В_ПУТИ','ДОСТАВЛЕН','ОТМЕНЕН']));
    foreach ($available as &$o) { $o['items'] = json_decode($o['items'], true); }
    foreach ($current as &$o) { $o['items'] = json_decode($o['items'], true); }
    echo json_encode(['available' => array_values($available), 'current' => array_values($current)]);
} elseif ($path === '/api/auth/logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy(); echo json_encode(['success' => true]);
} elseif ($path === '/api/admin/couriers' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $couriers = array_values(array_filter(readJson(DATA_DIR . '/data/users.json'), fn($u) => ($u['role'] ?? '') === 'courier'));
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $courierLocations = readJson(DATA_DIR . '/data/courier.json');
    
    foreach ($couriers as &$courier) {
        $courierId = $courier['id'];
        // Get current order for this courier
        $currentOrder = null;
        foreach ($orders as $o) {
            if (isset($o['courier_id']) && $o['courier_id'] == $courierId && in_array($o['status'], ['ОЖИДАНИЕ_ПОДТВЕРЖДЕНИЯ', 'В_ПУТИ'])) {
                $o['items'] = json_decode($o['items'], true);
                $currentOrder = $o;
                break;
            }
        }
        $courier['current_order'] = $currentOrder;
        
        // Get location
        $courier['location'] = null;
        foreach ($courierLocations as $loc) {
            if ($loc['courier_id'] == $courierId) {
                $courier['location'] = $loc;
                break;
            }
        }
    }
    echo json_encode($couriers);
} elseif ($path === '/api/admin/requests' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $users = readJson(DATA_DIR . '/data/users.json');
    $userMap = [];
    foreach ($users as $u) { $userMap[$u['id']] = $u; }
    
    // Find orders where courier has requested (status = WAITING_ADMIN_CONFIRM)
    $requests = [];
    foreach ($orders as $o) {
        if ($o['status'] === 'ОЖИДАНИЕ_ПОДТВЕРЖДЕНИЯ' && isset($o['courier_id'])) {
            $courier = $userMap[$o['courier_id']] ?? null;
            $requests[] = [
                'order_id' => $o['id'],
                'courier_id' => $o['courier_id'],
                'courier_name' => $courier['name'] ?? 'Неизвестный',
                'order_address' => $o['address'],
                'created_at' => $o['created_at']
            ];
        }
    }
    echo json_encode($requests);
} elseif (preg_match('#^/api/orders/(\d+)/confirm$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo json_encode(['error' => 'Admin required']); exit; }
    $orderId = intval($m[1]);
    $data = json_decode(file_get_contents('php://input'), true);
    $courierId = $data['courier_id'] ?? null;
    
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $found = false;
    foreach ($orders as &$o) {
        if ($o['id'] == $orderId) {
            if ($o['status'] === 'ОЖИДАНИЕ_ПОДТВЕРЖДЕНИЯ') {
                $o['status'] = 'В_ПУТИ';
                $found = true;
            }
            break;
        }
    }
    
    if ($found) {
        writeJson(DATA_DIR . '/data/orders.json', $orders);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Order not available for confirmation']);
    }
} elseif ($path === '/api/courier/orders/my' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') { http_response_code(403); echo json_encode(['error' => 'Courier required']); exit; }
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $courierId = $_SESSION['user']['id'];
    // Only show orders that are confirmed (not pending admin confirmation)
    $myOrders = array_filter($orders, fn($o) => isset($o['courier_id']) && $o['courier_id'] == $courierId && $o['status'] !== 'ОЖИДАНИЕ_ПОДТВЕРЖДЕНИЯ');
    foreach ($myOrders as &$o) { $o['items'] = json_decode($o['items'], true); }
    echo json_encode(array_values($myOrders));
} elseif (preg_match('#^/api/orders/(\d+)/status$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') { http_response_code(403); echo json_encode(['error' => 'Courier required']); exit; }
    $orderId = intval($m[1]);
    $data = json_decode(file_get_contents('php://input'), true);
    $status = $data['status'] ?? '';
    
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $found = false;
    foreach ($orders as &$o) {
        if ($o['id'] == $orderId && $o['courier_id'] == $_SESSION['user']['id']) {
            $validStatuses = ['СБОРКА', 'ОЖИДАНИЕ_КУРЬЕРА', 'В_ПУТИ', 'ДОСТАВЛЕН', 'ОТМЕНЕН'];
            if (in_array($status, $validStatuses)) {
                $o['status'] = $status;
                $found = true;
            }
            break;
        }
    }
    
    if ($found) {
        writeJson(DATA_DIR . '/data/orders.json', $orders);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Order not found or access denied']);
    }
} elseif (preg_match('#^/api/orders/(\d+)/cancel$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') { http_response_code(403); echo json_encode(['error' => 'Courier required']); exit; }
    $orderId = intval($m[1]);
    
    $orders = readJson(DATA_DIR . '/data/orders.json');
    $found = false;
    foreach ($orders as &$o) {
        if ($o['id'] == $orderId && $o['courier_id'] == $_SESSION['user']['id']) {
            $o['status'] = 'ОТМЕНЕН';
            $o['courier_id'] = null;
            $found = true;
            break;
        }
    }
    
    if ($found) {
        writeJson(DATA_DIR . '/data/orders.json', $orders);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Order not found or access denied']);
    }
} elseif ($path === '/api/courier/location' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') { http_response_code(403); echo json_encode(['error' => 'Courier required']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    $lat = floatval($data['lat'] ?? 0);
    $lng = floatval($data['lng'] ?? 0);
    
    if ($lat === 0 || $lng === 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid coordinates']);
        exit;
    }
    
    $courierLocations = readJson(DATA_DIR . '/data/courier.json');
    $found = false;
    foreach ($courierLocations as &$loc) {
        if ($loc['courier_id'] == $_SESSION['user']['id']) {
            $loc['lat'] = $lat;
            $loc['lng'] = $lng;
            $loc['updated_at'] = date('c');
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $courierLocations[] = [
            'courier_id' => $_SESSION['user']['id'],
            'lat' => $lat,
            'lng' => $lng,
            'updated_at' => date('c')
        ];
    }
    
    writeJson(DATA_DIR . '/data/courier.json', $courierLocations);
    echo json_encode(['success' => true]);
} else {

// Chat API - Get contacts (couriers and admins)
if ($path === "/api/chat/contacts" && $_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_SESSION["user"])) { http_response_code(401); echo json_encode(["error" => "Auth required"]); exit; }
    $users = readJson(DATA_DIR . "/data/users.json");
    $contacts = array_filter($users, fn($u) => in_array($u["role"] ?? "user", ["courier", "admin"]));
    foreach ($contacts as &$c) { unset($c["password"]); }
    echo json_encode(array_values($contacts));
}
// Chat API - Get messages with specific user
elseif (preg_match("#^/api/chat/messages/(\d+)$#", $path, $m) && $_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_SESSION["user"])) { http_response_code(401); echo json_encode(["error" => "Auth required"]); exit; }
    $contactId = intval($m[1]);
    $chat = readJson(DATA_DIR . "/data/chat.json");
    $messages = array_filter($chat, fn($msg) => 
        ($msg["sender_id"] == $_SESSION["user"]["id"] && $msg["receiver_id"] == $contactId) ||
        ($msg["sender_id"] == $contactId && $msg["receiver_id"] == $_SESSION["user"]["id"])
    );
    echo json_encode(array_values($messages));
}
// Chat API - Send message to specific user
elseif (preg_match("#^/api/chat/messages/(\d+)$#", $path, $m) && $_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION["user"])) { http_response_code(401); echo json_encode(["error" => "Auth required"]); exit; }
    $contactId = intval($m[1]);
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data["message"])) { http_response_code(400); echo json_encode(["error" => "Message required"]); exit; }
    $chat = readJson(DATA_DIR . "/data/chat.json");
    $chat[] = [
        "id" => getNextId($chat),
        "sender_id" => $_SESSION["user"]["id"],
        "sender_name" => $_SESSION["user"]["name"],
        "receiver_id" => $contactId,
        "message" => htmlspecialchars($data["message"]),
        "created_at" => date("c")
    ];
    writeJson(DATA_DIR . "/data/chat.json", $chat);
    echo json_encode(["success" => true]);
}
// Chat API - Get all messages (general chat)
elseif ($path === "/api/chat/messages" && $_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_SESSION["user"])) { http_response_code(401); echo json_encode(["error" => "Auth required"]); exit; }
    $chat = readJson(DATA_DIR . "/data/chat.json");
    echo json_encode($chat);
}
// Chat API - Send message to general chat
elseif ($path === "/api/chat/messages" && $_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION["user"])) { http_response_code(401); echo json_encode(["error" => "Auth required"]); exit; }
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data["message"])) { http_response_code(400); echo json_encode(["error" => "Message required"]); exit; }
    $chat = readJson(DATA_DIR . "/data/chat.json");
    $chat[] = [
        "id" => getNextId($chat),
        "sender_id" => $_SESSION["user"]["id"],
        "sender_name" => $_SESSION["user"]["name"],
        "receiver_id" => 0,
        "message" => htmlspecialchars($data["message"]),
        "created_at" => date("c")
    ];
    writeJson(DATA_DIR . "/data/chat.json", $chat);
    echo json_encode(["success" => true]);
}
// Admin Chat API - Get users with chat messages
elseif ($path === "/api/admin/chat/users" && $_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") { http_response_code(403); echo json_encode(["error" => "Admin required"]); exit; }
    $chat = readJson(DATA_DIR . "/data/chat.json");
    $users = readJson(DATA_DIR . "/data/users.json");
    
    // Get users who have sent or received messages
    $userIds = [];
    foreach ($chat as $msg) {
        $userIds[$msg["sender_id"]] = true;
        if (isset($msg["receiver_id"]) && $msg["receiver_id"] > 0) {
            $userIds[$msg["receiver_id"]] = true;
        }
    }
    
    $result = [];
    foreach ($userIds as $uid => $_) {
        $user = array_values(array_filter($users, fn($u) => $u["id"] == $uid));
        if (!empty($user) && $uid != $_SESSION["user"]["id"]) {
            $user = $user[0];
            $userMessages = array_filter($chat, fn($m) => $m["sender_id"] == $uid || (isset($m["receiver_id"]) && $m["receiver_id"] == $uid));
            $userMessages = array_values($userMessages);
            $lastMessage = end($userMessages);
            
            // Count unread messages (messages from user to admin)
            $unread = count(array_filter($chat, fn($m) => $m["sender_id"] == $uid && isset($m["receiver_id"]) && $m["receiver_id"] == $_SESSION["user"]["id"]));
            
            $result[] = [
                "id" => $user["id"],
                "name" => $user["name"],
                "phone" => $user["phone"],
                "last_message" => $lastMessage["message"] ?? null,
                "unread_count" => $unread
            ];
        }
    }
    
    // Sort by last message
    usort($result, function($a, $b) {
        return 0; // Keep original order
    });
    
    echo json_encode($result);
}
// Admin Chat API - Get messages with specific user
elseif (preg_match("#^/api/admin/chat/messages/(\d+)$#", $path, $m) && $_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") { http_response_code(403); echo json_encode(["error" => "Admin required"]); exit; }
    $userId = intval($m[1]);
    $chat = readJson(DATA_DIR . "/data/chat.json");
    
    // Get messages between admin and user
    $messages = array_filter($chat, fn($msg) =>
        ($msg["sender_id"] == $_SESSION["user"]["id"] && (isset($msg["receiver_id"]) && $msg["receiver_id"] == $userId)) ||
        ($msg["sender_id"] == $userId && (isset($msg["receiver_id"]) && $msg["receiver_id"] == $_SESSION["user"]["id"]))
    );
    
    echo json_encode(array_values($messages));
}
// Admin Chat API - Send message to user
elseif ($path === "/api/admin/chat/messages" && $_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") { http_response_code(403); echo json_encode(["error" => "Admin required"]); exit; }
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data["message"]) || empty($data["user_id"])) { http_response_code(400); echo json_encode(["error" => "Message and user_id required"]); exit; }
    
    $chat = readJson(DATA_DIR . "/data/chat.json");
    $chat[] = [
        "id" => getNextId($chat),
        "sender_id" => $_SESSION["user"]["id"],
        "sender_name" => $_SESSION["user"]["name"],
        "sender_role" => "admin",
        "receiver_id" => intval($data["user_id"]),
        "message" => htmlspecialchars($data["message"]),
        "created_at" => date("c")
    ];
    writeJson(DATA_DIR . "/data/chat.json", $chat);
    echo json_encode(["success" => true]);
}
    http_response_code(404); echo json_encode(['error' => 'Not found']);
}

function renderTemplate($template, $data) {
    extract($data);
    $isLoggedIn = isset($_SESSION['user']);
    $user = $_SESSION['user'] ?? null;
    ob_start();
    include __DIR__ . "/templates/$template.php";
    return ob_get_clean();
}
