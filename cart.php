<?php
/**
 * 장바구니 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$text = [
    'ko' => [
        'title' => '장바구니',
        'empty_cart' => '장바구니가 비어있습니다.',
        'product' => '상품',
        'price' => '가격',
        'quantity' => '수량',
        'total' => '합계',
        'subtotal' => '상품금액',
        'shipping' => '배송비',
        'final_total' => '최종 결제금액',
        'remove' => '삭제',
        'update' => '수량 변경',
        'continue_shopping' => '쇼핑 계속하기',
        'checkout' => '주문하기',
        'free_shipping' => '무료배송'
    ],
    'en' => [
        'title' => 'Shopping Cart',
        'empty_cart' => 'Your cart is empty.',
        'product' => 'Product',
        'price' => 'Price',
        'quantity' => 'Quantity',
        'total' => 'Total',
        'subtotal' => 'Subtotal',
        'shipping' => 'Shipping',
        'final_total' => 'Final Total',
        'remove' => 'Remove',
        'update' => 'Update',
        'continue_shopping' => 'Continue Shopping',
        'checkout' => 'Checkout',
        'free_shipping' => 'Free Shipping'
    ]
];
$t = $text[$lang] ?? $text['ko'];

// 장바구니 초기화
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 수량 변경
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $it_id = $_POST['it_id'];
    $quantity = intval($_POST['quantity']);
    
    if ($quantity > 0) {
        $_SESSION['cart'][$it_id] = $quantity;
    } else {
        unset($_SESSION['cart'][$it_id]);
    }
    
    header('Location: cart.php?lang=' . $lang);
    exit;
}

// 상품 삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $it_id = $_POST['it_id'];
    unset($_SESSION['cart'][$it_id]);
    
    header('Location: cart.php?lang=' . $lang);
    exit;
}

// 장바구니 상품 정보 조회
$cart_items = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $it_id => $quantity) {
    $product = g5_fetch("SELECT * FROM g5_shop_item WHERE it_id = ?", [$it_id]);
    if ($product) {
        $item_total = $product['it_price'] * $quantity;
        $subtotal += $item_total;
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'total' => $item_total
        ];
    }
}

$shipping_cost = $subtotal >= 50000 ? 0 : 3000; // 5만원 이상 무료배송
$final_total = $subtotal + $shipping_cost;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title']); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .cart-container {
            padding: 60px 0;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .cart-table th,
        .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .cart-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .cart-table img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-danger {
            background: #dc3545;
            color: #fff;
        }
        .btn-primary {
            background: #007bff;
            color: #fff;
        }
        .btn-success {
            background: #28a745;
            color: #fff;
        }
        .cart-summary {
            margin-top: 30px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-row.final {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            border-bottom: none;
            margin-top: 10px;
        }
        .cart-actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="cart-container">
        <div class="container">
            <h1><?php echo htmlspecialchars($t['title']); ?></h1>
            
            <?php if (empty($cart_items)): ?>
                <div style="text-align: center; padding: 60px 0;">
                    <p style="font-size: 18px; color: #666;"><?php echo htmlspecialchars($t['empty_cart']); ?></p>
                    <a href="shop.php?lang=<?php echo $lang; ?>" class="btn btn-primary" style="margin-top: 20px;"><?php echo htmlspecialchars($t['continue_shopping']); ?></a>
                </div>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th><?php echo htmlspecialchars($t['product']); ?></th>
                            <th><?php echo htmlspecialchars($t['price']); ?></th>
                            <th><?php echo htmlspecialchars($t['quantity']); ?></th>
                            <th><?php echo htmlspecialchars($t['total']); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <img src="<?php echo htmlspecialchars($item['product']['it_img1'] ?: 'https://via.placeholder.com/80x80?text=No+Image'); ?>" alt="<?php echo htmlspecialchars($item['product']['it_name']); ?>">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product']['it_name']); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo number_format($item['product']['it_price']); ?>원</td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="it_id" value="<?php echo $item['product']['it_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['it_stock_qty']; ?>" class="quantity-input" required>
                                        <button type="submit" name="update_quantity" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px; margin-left: 5px;"><?php echo htmlspecialchars($t['update']); ?></button>
                                    </form>
                                </td>
                                <td><strong><?php echo number_format($item['total']); ?>원</strong></td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('<?php echo $lang === 'ko' ? '정말 삭제하시겠습니까?' : 'Are you sure you want to remove this item?'; ?>');">
                                        <input type="hidden" name="it_id" value="<?php echo $item['product']['it_id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;"><?php echo htmlspecialchars($t['remove']); ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-summary">
                    <h2><?php echo $lang === 'ko' ? '주문 요약' : 'Order Summary'; ?></h2>
                    <div class="summary-row">
                        <span><?php echo htmlspecialchars($t['subtotal']); ?></span>
                        <span><?php echo number_format($subtotal); ?>원</span>
                    </div>
                    <div class="summary-row">
                        <span><?php echo htmlspecialchars($t['shipping']); ?> <?php echo $shipping_cost == 0 ? '(' . htmlspecialchars($t['free_shipping']) . ')' : ''; ?></span>
                        <span><?php echo $shipping_cost == 0 ? htmlspecialchars($t['free_shipping']) : number_format($shipping_cost) . '원'; ?></span>
                    </div>
                    <div class="summary-row final">
                        <span><?php echo htmlspecialchars($t['final_total']); ?></span>
                        <span><?php echo number_format($final_total); ?>원</span>
                    </div>
                </div>
                
                <div class="cart-actions">
                    <a href="shop.php?lang=<?php echo $lang; ?>" class="btn btn-primary"><?php echo htmlspecialchars($t['continue_shopping']); ?></a>
                    <a href="checkout.php?lang=<?php echo $lang; ?>" class="btn btn-success" style="padding: 15px 40px; font-size: 18px;"><?php echo htmlspecialchars($t['checkout']); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

