<?php
/**
 * 주문 완료 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$od_no = $_GET['od_no'] ?? null;

if (!$od_no) {
    header('Location: shop.php?lang=' . $lang);
    exit;
}

// 주문 정보 조회
$order = g5_fetch("SELECT * FROM g5_shop_order WHERE od_no = ?", [$od_no]);
$order_items = g5_fetch_all("SELECT * FROM g5_shop_order_item WHERE od_no = ?", [$od_no]);

if (!$order) {
    header('Location: shop.php?lang=' . $lang);
    exit;
}

$text = [
    'ko' => [
        'title' => '주문 완료',
        'thank_you' => '주문해주셔서 감사합니다.',
        'order_no' => '주문번호',
        'order_info' => '주문 정보',
        'product' => '상품',
        'price' => '가격',
        'qty' => '수량',
        'total' => '총액',
        'delivery_info' => '배송 정보',
        'name' => '이름',
        'email' => '이메일',
        'phone' => '전화번호',
        'address' => '주소',
        'payment_method' => '결제 방법',
        'payment_status' => '결제 상태',
        'paid' => '결제완료',
        'pending' => '결제대기',
        'back_to_shop' => '쇼핑몰로 돌아가기',
        'view_order' => '주문 내역 보기',
        'currency' => '원'
    ],
    'en' => [
        'title' => 'Order Complete',
        'thank_you' => 'Thank you for your order.',
        'order_no' => 'Order Number',
        'order_info' => 'Order Information',
        'product' => 'Product',
        'price' => 'Price',
        'qty' => 'Quantity',
        'total' => 'Total',
        'delivery_info' => 'Delivery Information',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'payment_method' => 'Payment Method',
        'payment_status' => 'Payment Status',
        'paid' => 'Paid',
        'pending' => 'Pending',
        'back_to_shop' => 'Back to Shop',
        'view_order' => 'View Order',
        'currency' => 'KRW'
    ]
];

$t = $text[$lang] ?? $text['ko'];
$t = array_merge($text['ko'], $t);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title']); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .complete-container {
            padding: 60px 0;
        }
        .complete-box {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success-icon {
            font-size: 64px;
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }
        .order-summary {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .order-section {
            margin-bottom: 30px;
        }
        .order-section h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .order-item {
            display: flex;
            gap: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .order-item-info {
            flex: 1;
        }
        .order-actions {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
        }
        .btn-primary {
            background: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="complete-container">
        <div class="container">
            <div class="complete-box">
                <div class="success-icon">✓</div>
                <h1 style="text-align: center; margin-bottom: 10px;"><?php echo htmlspecialchars($t['title']); ?></h1>
                <p style="text-align: center; color: #666; margin-bottom: 30px;"><?php echo htmlspecialchars($t['thank_you']); ?></p>
                
                <div class="order-summary">
                    <div class="order-section">
                        <h3><?php echo htmlspecialchars($t['order_no']); ?></h3>
                        <p style="font-size: 20px; font-weight: bold; color: #007bff;"><?php echo htmlspecialchars($order['od_no']); ?></p>
                    </div>
                    
                    <div class="order-section">
                        <h3><?php echo htmlspecialchars($t['order_info']); ?></h3>
                        <?php foreach ($order_items as $item): ?>
                            <div class="order-item">
                                <?php if ($item['it_img1']): ?>
                                    <img src="<?php echo htmlspecialchars($item['it_img1']); ?>" alt="<?php echo htmlspecialchars($item['it_name']); ?>">
                                <?php endif; ?>
                                <div class="order-item-info">
                                    <div style="font-weight: bold; margin-bottom: 5px;"><?php echo htmlspecialchars($item['it_name']); ?></div>
                                    <div><?php echo htmlspecialchars($t['price']); ?>: <?php echo number_format($item['it_price']); ?><?php echo htmlspecialchars($t['currency']); ?></div>
                                    <div><?php echo htmlspecialchars($t['qty']); ?>: <?php echo $item['ct_qty']; ?></div>
                                    <div style="font-weight: bold; margin-top: 5px;"><?php echo htmlspecialchars($t['total']); ?>: <?php echo number_format($item['ct_price']); ?><?php echo htmlspecialchars($t['currency']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: right; margin-top: 20px; font-size: 18px; font-weight: bold;">
                            <?php echo htmlspecialchars($t['total']); ?>: <?php echo number_format($order['od_cart_price'] + $order['od_send_cost']); ?><?php echo htmlspecialchars($t['currency']); ?>
                        </div>
                    </div>
                    
                    <div class="order-section">
                        <h3><?php echo htmlspecialchars($t['delivery_info']); ?></h3>
                        <div class="info-row">
                            <div class="info-label"><?php echo htmlspecialchars($t['name']); ?>:</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['od_name']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><?php echo htmlspecialchars($t['email']); ?>:</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['od_email']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><?php echo htmlspecialchars($t['phone']); ?>:</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['od_tel']); ?></div>
                        </div>
                        <?php if ($order['od_addr1']): ?>
                            <div class="info-row">
                                <div class="info-label"><?php echo htmlspecialchars($t['address']); ?>:</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($order['od_zip'] ?? ''); ?><br>
                                    <?php echo htmlspecialchars($order['od_addr1']); ?><br>
                                    <?php echo htmlspecialchars($order['od_addr2'] ?? ''); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="order-section">
                        <h3><?php echo htmlspecialchars($t['payment_method']); ?></h3>
                        <div class="info-row">
                            <div class="info-label"><?php echo htmlspecialchars($t['payment_method']); ?>:</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['od_settle_case']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><?php echo htmlspecialchars($t['payment_status']); ?>:</div>
                            <div class="info-value">
                                <?php if ($order['od_status'] === '결제완료'): ?>
                                    <span style="color: #28a745; font-weight: bold;"><?php echo htmlspecialchars($t['paid']); ?></span>
                                <?php else: ?>
                                    <span style="color: #ffc107; font-weight: bold;"><?php echo htmlspecialchars($t['pending']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="order-actions">
                    <a href="shop.php?lang=<?php echo $lang; ?>" class="btn btn-primary">
                        <?php echo htmlspecialchars($t['back_to_shop']); ?>
                    </a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>
