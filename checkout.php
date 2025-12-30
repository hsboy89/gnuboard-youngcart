<?php
/**
 * 결제 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

// 로그인 확인
if (!isset($_SESSION['mb_id'])) {
    header('Location: login.php?lang=' . $lang . '&redirect=checkout');
    exit;
}

// 장바구니 확인
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php?lang=' . $lang);
    exit;
}

$text = [
    'ko' => [
        'title' => '주문/결제',
        'order_info' => '주문 정보',
        'delivery_info' => '배송 정보',
        'payment_method' => '결제 수단',
        'name' => '이름',
        'email' => '이메일',
        'phone' => '전화번호',
        'mobile' => '휴대폰',
        'zipcode' => '우편번호',
        'address1' => '주소',
        'address2' => '상세주소',
        'memo' => '배송 메모',
        'bank_transfer' => '무통장 입금',
        'card' => '신용카드',
        'virtual_account' => '가상계좌',
        'mobile_payment' => '휴대폰 결제',
        'kcp' => 'NHN KCP',
        'inicis' => 'KG이니시스',
        'toss' => '토스페이먼츠',
        'place_order' => '주문하기',
        'product' => '상품',
        'quantity' => '수량',
        'price' => '가격',
        'subtotal' => '상품금액',
        'shipping' => '배송비',
        'total' => '최종 결제금액'
    ],
    'en' => [
        'title' => 'Checkout',
        'order_info' => 'Order Information',
        'delivery_info' => 'Delivery Information',
        'payment_method' => 'Payment Method',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'mobile' => 'Mobile',
        'zipcode' => 'Zipcode',
        'address1' => 'Address',
        'address2' => 'Detail Address',
        'memo' => 'Delivery Memo',
        'bank_transfer' => 'Bank Transfer',
        'card' => 'Credit Card',
        'virtual_account' => 'Virtual Account',
        'mobile_payment' => 'Mobile Payment',
        'kcp' => 'NHN KCP',
        'inicis' => 'KG Inicis',
        'toss' => 'Toss Payments',
        'place_order' => 'Place Order',
        'product' => 'Product',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'subtotal' => 'Subtotal',
        'shipping' => 'Shipping',
        'total' => 'Total'
    ]
];
$t = $text[$lang] ?? $text['ko'];

// 회원 정보 가져오기
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", [$_SESSION['mb_id']]);

// 장바구니 상품 정보
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

$shipping_cost = $subtotal >= 50000 ? 0 : 3000;
$final_total = $subtotal + $shipping_cost;

// 주문 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $db = g5_get_db();
    
    try {
        $db->beginTransaction();
        
        // 주문번호 생성
        $od_no = 'ORD' . date('YmdHis') . rand(1000, 9999);
        
        // 주문 정보 저장
        $stmt = $db->prepare("INSERT INTO g5_shop_order (od_no, mb_id, od_name, od_email, od_tel, od_hp, od_zip, od_addr1, od_addr2, od_addr3, od_memo, od_status, od_settle_case, od_cart_price, od_send_cost, od_receipt_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $od_no,
            $_SESSION['mb_id'],
            $_POST['od_name'],
            $_POST['od_email'],
            $_POST['od_tel'],
            $_POST['od_hp'] ?? '',
            $_POST['od_zip'] ?? '',
            $_POST['od_addr1'] ?? '',
            $_POST['od_addr2'] ?? '',
            $_POST['od_addr3'] ?? '',
            $_POST['od_memo'] ?? '',
            '주문완료',
            $_POST['od_settle_case'],
            $subtotal,
            $shipping_cost,
            $final_total
        ]);
        
        $od_id = $db->lastInsertId();
        
        // 주문 상세 저장
        foreach ($cart_items as $item) {
            $stmt = $db->prepare("INSERT INTO g5_shop_order_item (od_id, od_no, it_id, it_name, it_price, ct_qty, ct_price, it_img1) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $od_id,
                $od_no,
                $item['product']['it_id'],
                $item['product']['it_name'],
                $item['product']['it_price'],
                $item['quantity'],
                $item['total'],
                $item['product']['it_img1'] ?? ''
            ]);
        }
        
        $db->commit();
        
        // 결제 처리로 이동
        $_SESSION['order_no'] = $od_no;
        $_SESSION['order_id'] = $od_id;
        $_SESSION['payment_method'] = $_POST['od_settle_case'];
        
        header('Location: payment.php?pg=' . $_POST['od_settle_case'] . '&lang=' . $lang);
        exit;
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = $lang === 'ko' ? '주문 처리 중 오류가 발생했습니다: ' : 'Error processing order: ';
        $error .= $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title']); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .checkout-container {
            padding: 60px 0;
        }
        .checkout-form {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        .form-section {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .order-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #333;
            font-size: 20px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        .btn-success {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-success:hover {
            background: #218838;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .checkout-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="checkout-container">
        <div class="container">
            <h1><?php echo htmlspecialchars($t['title']); ?></h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="checkout.php?lang=<?php echo $lang; ?>">
                <div class="checkout-form">
                    <div>
                        <div class="form-section">
                            <h2><?php echo htmlspecialchars($t['order_info']); ?></h2>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['name']); ?> *</label>
                                <input type="text" name="od_name" value="<?php echo htmlspecialchars($member['mb_name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['email']); ?> *</label>
                                <input type="email" name="od_email" value="<?php echo htmlspecialchars($member['mb_email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['phone']); ?> *</label>
                                <input type="tel" name="od_tel" required>
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['mobile']); ?></label>
                                <input type="tel" name="od_hp">
                            </div>
                        </div>
                        
                        <div class="form-section" style="margin-top: 20px;">
                            <h2><?php echo htmlspecialchars($t['delivery_info']); ?></h2>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['zipcode']); ?></label>
                                <input type="text" name="od_zip" placeholder="12345">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['address1']); ?></label>
                                <input type="text" name="od_addr1" placeholder="<?php echo $lang === 'ko' ? '기본 주소' : 'Address'; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['address2']); ?></label>
                                <input type="text" name="od_addr2" placeholder="<?php echo $lang === 'ko' ? '상세 주소' : 'Detail Address'; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['address2']); ?> (<?php echo $lang === 'ko' ? '참고항목' : 'Reference'; ?>)</label>
                                <input type="text" name="od_addr3">
                            </div>
                            
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($t['memo']); ?></label>
                                <textarea name="od_memo" placeholder="<?php echo $lang === 'ko' ? '배송 시 요청사항을 입력해주세요' : 'Delivery instructions'; ?>"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-section" style="margin-top: 20px;">
                            <h2><?php echo htmlspecialchars($t['payment_method']); ?></h2>
                            
                            <div class="form-group">
                                <select name="od_settle_case" required>
                                    <option value="card"><?php echo htmlspecialchars($t['card']); ?> (<?php echo htmlspecialchars($t['kcp']); ?>)</option>
                                    <option value="card_inicis"><?php echo htmlspecialchars($t['card']); ?> (<?php echo htmlspecialchars($t['inicis']); ?>)</option>
                                    <option value="card_toss"><?php echo htmlspecialchars($t['card']); ?> (<?php echo htmlspecialchars($t['toss']); ?>)</option>
                                    <option value="bank"><?php echo htmlspecialchars($t['bank_transfer']); ?></option>
                                    <option value="virtual"><?php echo htmlspecialchars($t['virtual_account']); ?></option>
                                    <option value="mobile"><?php echo htmlspecialchars($t['mobile_payment']); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-section">
                            <h2><?php echo $lang === 'ko' ? '주문 요약' : 'Order Summary'; ?></h2>
                            
                            <div class="order-summary">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="order-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product']['it_name']); ?></strong><br>
                                            <small><?php echo $item['quantity']; ?>개 × <?php echo number_format($item['product']['it_price']); ?>원</small>
                                        </div>
                                        <div><?php echo number_format($item['total']); ?>원</div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="order-item">
                                    <span><?php echo htmlspecialchars($t['subtotal']); ?></span>
                                    <span><?php echo number_format($subtotal); ?>원</span>
                                </div>
                                
                                <div class="order-item">
                                    <span><?php echo htmlspecialchars($t['shipping']); ?></span>
                                    <span><?php echo $shipping_cost == 0 ? ($lang === 'ko' ? '무료' : 'Free') : number_format($shipping_cost) . '원'; ?></span>
                                </div>
                                
                                <div class="order-total">
                                    <span><?php echo htmlspecialchars($t['total']); ?></span>
                                    <span><?php echo number_format($final_total); ?>원</span>
                                </div>
                            </div>
                            
                            <button type="submit" name="place_order" class="btn-success"><?php echo htmlspecialchars($t['place_order']); ?></button>
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

