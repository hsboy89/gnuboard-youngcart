<?php
/**
 * Pet Tech 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$text = [
    'ko' => [
        'title' => 'Pet Tech',
        'hero_title' => '반려동물의 건강을 지키는 스마트 솔루션',
        'hero_desc' => '웨어러블 기술로 반려동물의 건강 상태를 실시간으로 모니터링하고 관리합니다.',
        'section1_title' => 'LUHearty for dogs',
        'section1_desc' => '반려견의 활동량, 심박수, 수면 패턴 등을 추적하여 건강 상태를 종합적으로 관리하는 헬스케어 웨어러블 디바이스입니다.',
        'benefits_title' => '주요 혜택',
        'benefit1' => '건강 모니터링',
        'benefit1_desc' => '24시간 건강 데이터를 수집하여 이상 징후를 조기에 발견합니다.',
        'benefit2' => '활동량 추적',
        'benefit2_desc' => '일일 활동량과 운동 패턴을 분석하여 적절한 운동량을 제안합니다.',
        'benefit3' => '수면 분석',
        'benefit3_desc' => '수면 질과 패턴을 분석하여 건강한 수면 습관을 유도합니다.',
        'benefit4' => '앱 연동',
        'benefit4_desc' => '스마트폰 앱을 통해 언제 어디서나 반려동물의 건강 정보를 확인할 수 있습니다.',
        'cta_title' => '반려동물의 건강을 지키는 첫걸음',
        'cta_button' => '제품 보기'
    ],
    'en' => [
        'title' => 'Pet Tech',
        'hero_title' => 'Smart Solutions for Pet Health',
        'hero_desc' => 'Monitor and manage your pet\'s health status in real-time with wearable technology.',
        'section1_title' => 'LUHearty for dogs',
        'section1_desc' => 'A healthcare wearable device that comprehensively manages your dog\'s health by tracking activity, heart rate, sleep patterns, and more.',
        'benefits_title' => 'Key Benefits',
        'benefit1' => 'Health Monitoring',
        'benefit1_desc' => 'Collect 24-hour health data to detect early signs of health issues.',
        'benefit2' => 'Activity Tracking',
        'benefit2_desc' => 'Analyze daily activity and exercise patterns to suggest appropriate exercise levels.',
        'benefit3' => 'Sleep Analysis',
        'benefit3_desc' => 'Analyze sleep quality and patterns to promote healthy sleep habits.',
        'benefit4' => 'App Integration',
        'benefit4_desc' => 'Check your pet\'s health information anytime, anywhere through a smartphone app.',
        'cta_title' => 'The First Step to Protecting Your Pet\'s Health',
        'cta_button' => 'View Products'
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
    <title><?php echo htmlspecialchars($t['title'] ?? 'Pet Tech'); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .page-hero {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #fff;
            padding: 100px 0;
            text-align: center;
        }
        .page-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .page-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        .content-section {
            padding: 80px 0;
        }
        .product-showcase {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 80px;
        }
        .product-image {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 120px;
        }
        .product-content h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }
        .product-content p {
            font-size: 18px;
            line-height: 1.8;
            color: #666;
            margin-bottom: 30px;
        }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 60px;
        }
        .benefit-card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #f5576c;
        }
        .benefit-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        .benefit-card p {
            color: #666;
            line-height: 1.6;
        }
        .cta-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #fff;
            padding: 80px 0;
            text-align: center;
        }
        .cta-section h2 {
            font-size: 36px;
            margin-bottom: 30px;
        }
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background: #fff;
            color: #f5576c;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
            transition: transform 0.3s;
        }
        .cta-button:hover {
            transform: translateY(-3px);
        }
        @media (max-width: 768px) {
            .product-showcase {
                grid-template-columns: 1fr;
            }
            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <section class="page-hero">
        <div class="container">
            <h1><?php echo htmlspecialchars($t['hero_title'] ?? '반려동물의 건강을 지키는 스마트 솔루션'); ?></h1>
            <p><?php echo htmlspecialchars($t['hero_desc'] ?? '웨어러블 기술로 반려동물의 건강 상태를 실시간으로 모니터링하고 관리합니다.'); ?></p>
        </div>
    </section>
    
    <main class="content-section">
        <div class="container">
            <div class="product-showcase">
                <div class="product-image">🐕</div>
                <div class="product-content">
                    <h2><?php echo htmlspecialchars($t['section1_title'] ?? 'LUHearty for dogs'); ?></h2>
                    <p><?php echo htmlspecialchars($t['section1_desc'] ?? '반려견의 활동량, 심박수, 수면 패턴 등을 추적하여 건강 상태를 종합적으로 관리하는 헬스케어 웨어러블 디바이스입니다.'); ?></p>
                    <a href="product.php?type=luhearty-dog&lang=<?php echo $lang; ?>" style="display: inline-block; margin-top: 20px; color: #f5576c; text-decoration: none; font-weight: bold;">자세히 보기 →</a>
                </div>
            </div>
            
            <div style="margin-top: 100px;">
                <h2 style="text-align: center; font-size: 36px; margin-bottom: 40px;"><?php echo htmlspecialchars($t['benefits_title'] ?? '주요 혜택'); ?></h2>
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <h3><?php echo htmlspecialchars($t['benefit1'] ?? '건강 모니터링'); ?></h3>
                        <p><?php echo htmlspecialchars($t['benefit1_desc'] ?? '24시간 건강 데이터를 수집하여 이상 징후를 조기에 발견합니다.'); ?></p>
                    </div>
                    <div class="benefit-card">
                        <h3><?php echo htmlspecialchars($t['benefit2'] ?? '활동량 추적'); ?></h3>
                        <p><?php echo htmlspecialchars($t['benefit2_desc'] ?? '일일 활동량과 운동 패턴을 분석하여 적절한 운동량을 제안합니다.'); ?></p>
                    </div>
                    <div class="benefit-card">
                        <h3><?php echo htmlspecialchars($t['benefit3'] ?? '수면 분석'); ?></h3>
                        <p><?php echo htmlspecialchars($t['benefit3_desc'] ?? '수면 질과 패턴을 분석하여 건강한 수면 습관을 유도합니다.'); ?></p>
                    </div>
                    <div class="benefit-card">
                        <h3><?php echo htmlspecialchars($t['benefit4'] ?? '앱 연동'); ?></h3>
                        <p><?php echo htmlspecialchars($t['benefit4_desc'] ?? '스마트폰 앱을 통해 언제 어디서나 반려동물의 건강 정보를 확인할 수 있습니다.'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <section class="cta-section">
        <div class="container">
            <h2><?php echo htmlspecialchars($t['cta_title'] ?? '반려동물의 건강을 지키는 첫걸음'); ?></h2>
            <a href="shop.php?lang=<?php echo $lang; ?>" class="cta-button"><?php echo htmlspecialchars($t['cta_button'] ?? '제품 보기'); ?></a>
        </div>
    </section>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

