<?php
/**
 * 결제 실패 페이지
 */

require_once '../data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$od_no = $_GET['od_no'] ?? $_POST['ordr_idxx'] ?? $_POST['oid'] ?? null;

$text = [
    'ko' => [
        'title' => '결제 실패',
        'message' => '결제가 실패했습니다.',
        'reason' => '사유',
        'back_to_shop' => '쇼핑몰로 돌아가기'
    ],
    'en' => [
        'title' => 'Payment Failed',
        'message' => 'Payment has failed.',
        'reason' => 'Reason',
        'back_to_shop' => 'Back to Shop'
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
    <link rel="stylesheet" href="../theme/pumae/css/style.css">
    <style>
        .error-container {
            padding: 100px 0;
            text-align: center;
        }
        .error-box {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include '../theme/pumae/header.php'; ?>
    
    <main class="error-container">
        <div class="container">
            <div class="error-box">
                <div class="error-icon">✕</div>
                <h1><?php echo htmlspecialchars($t['title']); ?></h1>
                <p><?php echo htmlspecialchars($t['message']); ?></p>
                <?php if (isset($_GET['message'])): ?>
                    <p style="color: #666; margin-top: 20px;">
                        <?php echo htmlspecialchars($t['reason']); ?>: <?php echo htmlspecialchars($_GET['message']); ?>
                    </p>
                <?php endif; ?>
                <a href="../shop.php?lang=<?php echo $lang; ?>" class="btn btn-primary" style="margin-top: 30px; display: inline-block;">
                    <?php echo htmlspecialchars($t['back_to_shop']); ?>
                </a>
            </div>
        </div>
    </main>
    
    <?php include '../theme/pumae/footer.php'; ?>
</body>
</html>

