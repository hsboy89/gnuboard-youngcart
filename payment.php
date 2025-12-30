<?php
/**
 * 결제 처리 페이지
 * NHN KCP, KG이니시스, 토스페이먼츠 연동
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

// 로그인 확인
if (!isset($_SESSION['mb_id'])) {
    header('Location: login.php?lang=' . $lang);
    exit;
}

// 주문 정보 확인
if (!isset($_SESSION['order_no']) || !isset($_SESSION['order_id'])) {
    header('Location: cart.php?lang=' . $lang);
    exit;
}

$pg = $_GET['pg'] ?? ($_SESSION['payment_method'] ?? 'card');
$order_no = $_SESSION['order_no'];
$order_id = $_SESSION['order_id'];

// 주문 정보 조회
$order = g5_fetch("SELECT * FROM g5_shop_order WHERE od_id = ?", [$order_id]);

if (!$order) {
    header('Location: cart.php?lang=' . $lang);
    exit;
}

// 결제 모듈 로드
require_once 'payment/payment_config.php';
require_once 'payment/PaymentGateway.php';

$text = [
    'ko' => [
        'title' => '결제',
        'processing' => '결제 처리 중...',
        'order_no' => '주문번호',
        'amount' => '결제금액',
        'payment_method' => '결제수단',
        'cancel' => '취소'
    ],
    'en' => [
        'title' => 'Payment',
        'processing' => 'Processing payment...',
        'order_no' => 'Order No',
        'amount' => 'Amount',
        'payment_method' => 'Payment Method',
        'cancel' => 'Cancel'
    ]
];
$t = $text[$lang] ?? $text['ko'];

// 결제 처리
$payment_gateway = new PaymentGateway($pg);
$payment_result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_data'])) {
    try {
        $payment_result = $payment_gateway->processPayment([
            'order_no' => $order_no,
            'amount' => $order['od_receipt_price'],
            'product_name' => $order_no . ' 주문',
            'buyer_name' => $order['od_name'],
            'buyer_email' => $order['od_email'],
            'buyer_tel' => $order['od_tel']
        ]);
        
        if ($payment_result['success']) {
            // 주문 상태 업데이트
            g5_query("UPDATE g5_shop_order SET od_status = '결제완료', od_receipt_time = datetime('now'), od_settle_case = ? WHERE od_id = ?", [$pg, $order_id]);
            
            // 장바구니 비우기
            $_SESSION['cart'] = [];
            unset($_SESSION['order_no']);
            unset($_SESSION['order_id']);
            
            header('Location: order_complete.php?order_no=' . $order_no . '&lang=' . $lang);
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
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
        .payment-container {
            padding: 60px 0;
            min-height: 60vh;
        }
        .payment-box {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .payment-info {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .payment-form {
            margin-top: 30px;
        }
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #007bff;
            color: #fff;
            width: 100%;
        }
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="payment-container">
        <div class="container">
            <div class="payment-box">
                <h1><?php echo htmlspecialchars($t['title']); ?></h1>
                
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="payment-info">
                    <div class="info-row">
                        <span class="info-label"><?php echo htmlspecialchars($t['order_no']); ?></span>
                        <span class="info-value"><?php echo htmlspecialchars($order_no); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo htmlspecialchars($t['amount']); ?></span>
                        <span class="info-value"><?php echo number_format($order['od_receipt_price']); ?>원</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo htmlspecialchars($t['payment_method']); ?></span>
                        <span><?php echo htmlspecialchars($payment_gateway->getPaymentMethodName($pg)); ?></span>
                    </div>
                </div>
                
                <div class="payment-form">
                    <?php echo $payment_gateway->renderPaymentForm($order_no, $order['od_receipt_price'], $order); ?>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="cart.php?lang=<?php echo $lang; ?>" class="btn btn-secondary"><?php echo htmlspecialchars($t['cancel']); ?></a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

