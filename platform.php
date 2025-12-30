<?php
/**
 * Platform 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$text = [
    'ko' => [
        'title' => 'Platform',
        'hero_title' => '통합 데이터 관리 플랫폼',
        'hero_desc' => '웨어러블 디바이스에서 수집된 데이터를 통합 관리하고 분석하는 플랫폼입니다.',
        'section1_title' => 'LUHearty Platform',
        'section1_desc' => '센서 데이터 처리 및 관리 플랫폼으로, 다양한 웨어러블 디바이스의 데이터를 한 곳에서 관리하고 분석할 수 있습니다.',
        'features_title' => '플랫폼 기능',
        'feature1' => '데이터 통합',
        'feature1_desc' => '여러 디바이스의 데이터를 하나의 플랫폼에서 통합 관리합니다.',
        'feature2' => '실시간 분석',
        'feature2_desc' => '수집된 데이터를 실시간으로 분석하여 즉각적인 인사이트를 제공합니다.',
        'feature3' => '시각화 대시보드',
        'feature3_desc' => '복잡한 데이터를 직관적인 차트와 그래프로 시각화합니다.',
        'feature4' => 'API 연동',
        'feature4_desc' => '다양한 서비스와 API를 통해 데이터를 연동하고 확장할 수 있습니다.',
        'cta_title' => '플랫폼으로 데이터를 더 스마트하게',
        'cta_button' => '더 알아보기'
    ],
    'en' => [
        'title' => 'Platform',
        'hero_title' => 'Integrated Data Management Platform',
        'hero_desc' => 'A platform that integrates and analyzes data collected from wearable devices.',
        'section1_title' => 'LUHearty Platform',
        'section1_desc' => 'A sensor data processing and management platform that allows you to manage and analyze data from various wearable devices in one place.',
        'features_title' => 'Platform Features',
        'feature1' => 'Data Integration',
        'feature1_desc' => 'Integrate and manage data from multiple devices in one platform.',
        'feature2' => 'Real-time Analysis',
        'feature2_desc' => 'Analyze collected data in real-time to provide immediate insights.',
        'feature3' => 'Visualization Dashboard',
        'feature3_desc' => 'Visualize complex data with intuitive charts and graphs.',
        'feature4' => 'API Integration',
        'feature4_desc' => 'Integrate and extend data through various services and APIs.',
        'cta_title' => 'Make Your Data Smarter with Our Platform',
        'cta_button' => 'Learn More'
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
    <title><?php echo htmlspecialchars($t['title'] ?? 'Platform'); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .page-hero {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        .platform-showcase {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 80px;
        }
        .platform-image {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 120px;
        }
        .platform-content h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }
        .platform-content p {
            font-size: 18px;
            line-height: 1.8;
            color: #666;
            margin-bottom: 30px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 60px;
        }
        .feature-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-top: 4px solid #4facfe;
        }
        .feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        .cta-section {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            color: #4facfe;
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
            .platform-showcase {
                grid-template-columns: 1fr;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <section class="page-hero">
        <div class="container">
            <h1><?php echo htmlspecialchars($t['hero_title'] ?? '통합 데이터 관리 플랫폼'); ?></h1>
            <p><?php echo htmlspecialchars($t['hero_desc'] ?? '웨어러블 디바이스에서 수집된 데이터를 통합 관리하고 분석하는 플랫폼입니다.'); ?></p>
        </div>
    </section>
    
    <main class="content-section">
        <div class="container">
            <div class="platform-showcase">
                <div class="platform-image">💻</div>
                <div class="platform-content">
                    <h2><?php echo htmlspecialchars($t['section1_title'] ?? 'LUHearty Platform'); ?></h2>
                    <p><?php echo htmlspecialchars($t['section1_desc'] ?? '센서 데이터 처리 및 관리 플랫폼으로, 다양한 웨어러블 디바이스의 데이터를 한 곳에서 관리하고 분석할 수 있습니다.'); ?></p>
                    <a href="product.php?type=luhearty&lang=<?php echo $lang; ?>" style="display: inline-block; margin-top: 20px; color: #4facfe; text-decoration: none; font-weight: bold;">자세히 보기 →</a>
                </div>
            </div>
            
            <div style="margin-top: 100px;">
                <h2 style="text-align: center; font-size: 36px; margin-bottom: 40px;"><?php echo htmlspecialchars($t['features_title'] ?? '플랫폼 기능'); ?></h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🔗</div>
                        <h3><?php echo htmlspecialchars($t['feature1'] ?? '데이터 통합'); ?></h3>
                        <p><?php echo htmlspecialchars($t['feature1_desc'] ?? '여러 디바이스의 데이터를 하나의 플랫폼에서 통합 관리합니다.'); ?></p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h3><?php echo htmlspecialchars($t['feature2'] ?? '실시간 분석'); ?></h3>
                        <p><?php echo htmlspecialchars($t['feature2_desc'] ?? '수집된 데이터를 실시간으로 분석하여 즉각적인 인사이트를 제공합니다.'); ?></p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📈</div>
                        <h3><?php echo htmlspecialchars($t['feature3'] ?? '시각화 대시보드'); ?></h3>
                        <p><?php echo htmlspecialchars($t['feature3_desc'] ?? '복잡한 데이터를 직관적인 차트와 그래프로 시각화합니다.'); ?></p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔌</div>
                        <h3><?php echo htmlspecialchars($t['feature4'] ?? 'API 연동'); ?></h3>
                        <p><?php echo htmlspecialchars($t['feature4_desc'] ?? '다양한 서비스와 API를 통해 데이터를 연동하고 확장할 수 있습니다.'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <section class="cta-section">
        <div class="container">
            <h2><?php echo htmlspecialchars($t['cta_title'] ?? '플랫폼으로 데이터를 더 스마트하게'); ?></h2>
            <a href="shop.php?lang=<?php echo $lang; ?>" class="cta-button"><?php echo htmlspecialchars($t['cta_button'] ?? '더 알아보기'); ?></a>
        </div>
    </section>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

