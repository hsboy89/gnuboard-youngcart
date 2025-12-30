<?php
/**
 * 주문/결제 페이지
 */

require_once 'data/dbconfig.php';
require_once 'auto_create_tables.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 주문 테이블이 없으면 자동 생성
ensure_order_tables();

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$text = [
    'ko' => [
        'title' => '주문/결제',
        'order_info' => '주문 정보',
        'product' => '상품',
        'price' => '가격',
        'qty' => '수량',
        'total' => '총액',
        'delivery_info' => '배송 정보',
        'name' => '이름',
        'email' => '이메일',
        'phone' => '전화번호',
        'mobile' => '휴대폰',
        'zipcode' => '우편번호',
        'address1' => '주소',
        'address2' => '상세주소',
        'memo' => '배송 메모',
        'payment_method' => '결제 방법',
        'bank_transfer' => '무통장입금',
        'card' => '신용카드',
        'kcp' => 'NHN KCP',
        'inicis' => 'KG이니시스',
        'toss' => '토스페이먼츠',
        'place_order' => '주문하기',
        'required' => '필수',
        'currency' => '원',
        'delivery_cost' => '배송비',
        'free_delivery' => '무료배송',
        'total_amount' => '최종 결제금액'
    ],
    'en' => [
        'title' => 'Order/Payment',
        'order_info' => 'Order Information',
        'product' => 'Product',
        'price' => 'Price',
        'qty' => 'Quantity',
        'total' => 'Total',
        'delivery_info' => 'Delivery Information',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'mobile' => 'Mobile',
        'zipcode' => 'Zipcode',
        'address1' => 'Address',
        'address2' => 'Detail Address',
        'memo' => 'Delivery Memo',
        'payment_method' => 'Payment Method',
        'bank_transfer' => 'Bank Transfer',
        'card' => 'Credit Card',
        'kcp' => 'NHN KCP',
        'inicis' => 'KG이니시스',
        'toss' => '토스페이먼츠',
        'place_order' => 'Place Order',
        'required' => 'Required',
        'currency' => 'KRW',
        'delivery_cost' => 'Delivery Cost',
        'free_delivery' => 'Free Delivery',
        'total_amount' => 'Total Amount'
    ]
];

$t = $text[$lang] ?? $text['ko'];
$t = array_merge($text['ko'], $t);

// POST 처리 - 주문 생성
$error = '';
$order_no = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    try {
        $db = g5_get_db();
        $db->beginTransaction();
        
        // 주문 번호 생성
        $order_no = 'ORD' . date('YmdHis') . rand(1000, 9999);
        
        // 상품 정보 조회
        $it_id = $_POST['it_id'] ?? null;
        $qty = intval($_POST['qty'] ?? 1);
        
        if (!$it_id) {
            throw new Exception('상품 정보가 없습니다.');
        }
        
        $product = g5_fetch("SELECT * FROM g5_shop_item WHERE it_id = ?", [$it_id]);
        if (!$product) {
            throw new Exception('상품을 찾을 수 없습니다.');
        }
        
        // 재고 체크 (재고가 없어도 주문은 가능하도록, 주문 완료 시 경고만 표시)
        if ($product['it_stock_qty'] < $qty && $product['it_stock_qty'] > 0) {
            throw new Exception('재고가 부족합니다. (재고: ' . $product['it_stock_qty'] . '개)');
        }
        
        // 주문 정보
        $cart_price = $product['it_price'] * $qty;
        $send_cost = $cart_price >= 50000 ? 0 : 3000; // 5만원 이상 무료배송
        $total_price = $cart_price + $send_cost;
        
        // 주문 저장
        $stmt = $db->prepare("INSERT INTO g5_shop_order (od_no, mb_id, od_name, od_email, od_tel, od_hp, od_zip, od_addr1, od_addr2, od_memo, od_settle_case, od_cart_price, od_send_cost, od_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $order_no,
            $_SESSION['mb_id'] ?? null,
            $_POST['od_name'] ?? '',
            $_POST['od_email'] ?? '',
            $_POST['od_tel'] ?? '',
            $_POST['od_hp'] ?? '',
            $_POST['od_zip'] ?? '',
            $_POST['od_addr1'] ?? '',
            $_POST['od_addr2'] ?? '',
            $_POST['od_memo'] ?? '',
            $_POST['od_settle_case'] ?? '무통장입금',
            $cart_price,
            $send_cost,
            '주문완료'
        ]);
        
        $od_id = $db->lastInsertId();
        
        // 주문 상세 저장
        $stmt = $db->prepare("INSERT INTO g5_shop_order_item (od_id, od_no, it_id, it_name, it_price, ct_qty, ct_price, it_img1) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $od_id,
            $order_no,
            $product['it_id'],
            $product['it_name'],
            $product['it_price'],
            $qty,
            $cart_price,
            $product['it_img1'] ?? ''
        ]);
        
        // 재고 차감 (재고가 있을 때만)
        if ($product['it_stock_qty'] > 0) {
            $stmt = $db->prepare("UPDATE g5_shop_item SET it_stock_qty = it_stock_qty - ? WHERE it_id = ?");
            $stmt->execute([$qty, $it_id]);
        }
        
        $db->commit();
        
        // 결제 방법에 따라 리다이렉트
        $settle_case = $_POST['od_settle_case'] ?? '무통장입금';
        
        if ($settle_case === '무통장입금') {
            // 무통장입금은 바로 완료 페이지로
            header('Location: order_complete.php?od_no=' . $order_no . '&lang=' . $lang);
            exit;
        } else {
            // PG사 결제로 리다이렉트
            header('Location: payment/process.php?od_no=' . $order_no . '&pg=' . $settle_case . '&lang=' . $lang);
            exit;
        }
        
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $error = $e->getMessage();
    }
}

// 상품 정보 조회
$it_id = $_GET['it_id'] ?? $_POST['it_id'] ?? null;
$qty = intval($_GET['qty'] ?? $_POST['qty'] ?? 1);

if (!$it_id) {
    header('Location: shop.php?lang=' . $lang);
    exit;
}

$product = g5_fetch("SELECT * FROM g5_shop_item WHERE it_id = ? AND it_use = 1 AND it_sell_use = 1", [$it_id]);

if (!$product) {
    header('Location: shop.php?lang=' . $lang);
    exit;
}

$cart_price = $product['it_price'] * $qty;
$send_cost = $cart_price >= 50000 ? 0 : 3000;
$total_price = $cart_price + $send_cost;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title'] ?? '주문/결제'); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .order-container {
            padding: 60px 0;
        }
        .order-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        .order-section {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .order-section h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .required {
            color: #dc3545;
        }
        .product-summary {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .product-summary img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .product-summary-info {
            flex: 1;
        }
        .price-summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #007bff;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .price-row.total {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-submit:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .order-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="order-container">
        <div class="container">
            <h1><?php echo htmlspecialchars($t['title'] ?? '주문/결제'); ?></h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="order.php?lang=<?php echo $lang; ?>">
                <input type="hidden" name="it_id" value="<?php echo $product['it_id']; ?>">
                <input type="hidden" name="qty" value="<?php echo $qty; ?>">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                
                <div class="order-form">
                    <div>
                        <div class="order-section">
                            <h2><?php echo htmlspecialchars($t['order_info'] ?? '주문 정보'); ?></h2>
                            
                            <?php if ($product['it_stock_qty'] <= 0): ?>
                                <div class="error" style="background: #fff3cd; color: #856404; border: 1px solid #ffc107; margin-bottom: 20px;">
                                    ⚠️ 현재 재고가 없습니다. 주문은 가능하지만 배송이 지연될 수 있습니다.
                                </div>
                            <?php elseif ($product['it_stock_qty'] < $qty): ?>
                                <div class="error" style="background: #fff3cd; color: #856404; border: 1px solid #ffc107; margin-bottom: 20px;">
                                    ⚠️ 재고가 부족합니다. (재고: <?php echo number_format($product['it_stock_qty']); ?>개)
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-summary">
                                <?php if ($product['it_img1']): ?>
                                    <img src="<?php echo htmlspecialchars($product['it_img1']); ?>" alt="<?php echo htmlspecialchars($product['it_name']); ?>">
                                <?php endif; ?>
                                <div class="product-summary-info">
                                    <div style="font-weight: bold; margin-bottom: 10px;"><?php echo htmlspecialchars($product['it_name']); ?></div>
                                    <div><?php echo htmlspecialchars($t['price'] ?? '가격'); ?>: <?php echo number_format($product['it_price']); ?><?php echo htmlspecialchars($t['currency'] ?? '원'); ?></div>
                                    <div><?php echo htmlspecialchars($t['qty'] ?? '수량'); ?>: <?php echo $qty; ?></div>
                                    <?php if ($product['it_stock_qty'] > 0): ?>
                                        <div style="color: #28a745; margin-top: 5px;">재고: <?php echo number_format($product['it_stock_qty']); ?>개</div>
                                    <?php else: ?>
                                        <div style="color: #dc3545; margin-top: 5px;">재고 없음</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="price-summary">
                                <div class="price-row">
                                    <span><?php echo htmlspecialchars($t['product'] ?? '상품'); ?> <?php echo htmlspecialchars($t['total'] ?? '총액'); ?></span>
                                    <span><?php echo number_format($cart_price); ?><?php echo htmlspecialchars($t['currency'] ?? '원'); ?></span>
                                </div>
                                <div class="price-row">
                                    <span><?php echo htmlspecialchars($t['delivery_cost'] ?? '배송비'); ?></span>
                                    <span><?php echo $send_cost > 0 ? number_format($send_cost) . ($t['currency'] ?? '원') : ($t['free_delivery'] ?? '무료배송'); ?></span>
                                </div>
                                <div class="price-row total">
                                    <span><?php echo htmlspecialchars($t['total_amount'] ?? '최종 결제금액'); ?></span>
                                    <span><?php echo number_format($total_price); ?><?php echo htmlspecialchars($t['currency'] ?? '원'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-section">
                            <h2><?php echo htmlspecialchars($t['delivery_info'] ?? '배송 정보'); ?></h2>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['name'] ?? '이름'); ?> <span class="required">*</span></label>
                                <input type="text" name="od_name" value="<?php echo htmlspecialchars($_SESSION['mb_name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['email'] ?? '이메일'); ?> <span class="required">*</span></label>
                                <input type="email" name="od_email" value="<?php echo htmlspecialchars($_SESSION['mb_email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['phone'] ?? '전화번호'); ?> <span class="required">*</span></label>
                                <input type="tel" name="od_tel" required>
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['mobile'] ?? '휴대폰'); ?></label>
                                <input type="tel" name="od_hp">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['zipcode'] ?? '우편번호'); ?></label>
                                <input type="text" name="od_zip" placeholder="12345">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['address1'] ?? '주소'); ?></label>
                                <input type="text" name="od_addr1" placeholder="기본주소">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['address2'] ?? '상세주소'); ?></label>
                                <input type="text" name="od_addr2" placeholder="상세주소">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['memo'] ?? '배송 메모'); ?></label>
                                <textarea name="od_memo" placeholder="배송 시 요청사항"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="order-section">
                            <h2><?php echo htmlspecialchars($t['payment_method'] ?? '결제 방법'); ?></h2>
                            
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="od_settle_case" value="무통장입금" checked>
                                    <?php echo htmlspecialchars($t['bank_transfer'] ?? '무통장입금'); ?>
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="od_settle_case" value="kcp">
                                    <?php echo htmlspecialchars($t['kcp'] ?? 'NHN KCP'); ?>
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="od_settle_case" value="inicis">
                                    <?php echo htmlspecialchars($t['inicis'] ?? 'KG이니시스'); ?>
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="od_settle_case" value="toss">
                                    <?php echo htmlspecialchars($t['toss'] ?? '토스페이먼츠'); ?>
                                </label>
                            </div>
                            
                            <button type="submit" name="place_order" class="btn-submit">
                                <?php echo htmlspecialchars($t['place_order'] ?? '주문하기'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

