<?php
/**
 * NHN KCP 결제 모듈
 */

require_once 'config.php';

// process.php에서 전달된 변수 사용
$lang = $lang ?? 'ko';
$od_no = $od_no ?? '';
$order = $order ?? [];
$order_items = $order_items ?? [];
$total_amount = $total_amount ?? 0;

$text = [
    'ko' => [
        'title' => 'NHN KCP 결제',
        'redirecting' => '결제 페이지로 이동 중...'
    ],
    'en' => [
        'title' => 'NHN KCP Payment',
        'redirecting' => 'Redirecting to payment page...'
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
    <title><?php echo htmlspecialchars($t['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        .payment-container {
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php if (empty(PG_KCP_SITE_CD)): ?>
        <div class="payment-container">
            <h2><?php echo htmlspecialchars($t['title'] ?? 'NHN KCP 결제'); ?></h2>
            <div class="spinner"></div>
            <p><?php echo htmlspecialchars($t['redirecting'] ?? '결제 페이지로 이동 중...'); ?></p>
        </div>
        <!-- PG사 설정이 없어서 테스트 모드로 표시 -->
        <div style="background: #fff3cd; padding: 20px; border-radius: 4px; margin-top: 20px;">
            <h3>⚠️ PG사 설정이 필요합니다</h3>
            <p>KCP 결제를 사용하려면 <code>payment/config.php</code> 파일에 KCP 사이트 코드와 키를 설정해야 합니다.</p>
            <p><strong>테스트 모드:</strong> 현재는 실제 결제가 진행되지 않습니다.</p>
            <p><strong>주문번호:</strong> <?php echo htmlspecialchars($od_no); ?></p>
            <p><strong>결제금액:</strong> <?php echo number_format($total_amount); ?>원</p>
            <p><strong>상품명:</strong> <?php echo htmlspecialchars($order_items[0]['it_name'] ?? ''); ?></p>
            <a href="../order_complete.php?od_no=<?php echo htmlspecialchars($od_no); ?>&lang=<?php echo $lang; ?>" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px;">
                테스트 완료 (주문 완료 페이지로 이동)
            </a>
        </div>
    <?php else: ?>
        <!-- iframe으로 결제 페이지 표시 -->
        <div style="width: 100%; height: 100vh; border: none;">
            <iframe id="paymentFrame" name="paymentFrame" style="width: 100%; height: 100vh; border: none;"></iframe>
        </div>
        
        <form id="kcpForm" method="POST" action="<?php echo PG_TEST_MODE ? 'https://testpay.kcp.co.kr/pg/pay' : 'https://pay.kcp.co.kr/pg/pay'; ?>" target="paymentFrame" style="display: none;">
            <input type="hidden" name="site_cd" value="<?php echo htmlspecialchars(PG_KCP_SITE_CD); ?>">
            <input type="hidden" name="ordr_idxx" value="<?php echo htmlspecialchars($od_no); ?>">
            <input type="hidden" name="good_mny" value="<?php echo $total_amount; ?>">
            <input type="hidden" name="good_name" value="<?php echo htmlspecialchars($order_items[0]['it_name'] ?? ''); ?>">
            <input type="hidden" name="buyr_name" value="<?php echo htmlspecialchars($order['od_name'] ?? ''); ?>">
            <input type="hidden" name="buyr_tel1" value="<?php echo htmlspecialchars($order['od_tel'] ?? ''); ?>">
            <input type="hidden" name="buyr_mail" value="<?php echo htmlspecialchars($order['od_email'] ?? ''); ?>">
            <input type="hidden" name="return_url" value="<?php echo PG_SUCCESS_URL; ?>?lang=<?php echo $lang; ?>">
            <input type="hidden" name="noti_url" value="<?php echo PG_SUCCESS_URL; ?>?lang=<?php echo $lang; ?>">
        </form>
        
        <script>
            // 자동 제출
            document.getElementById('kcpForm').submit();
        </script>
    <?php endif; ?>
</body>
</html>

