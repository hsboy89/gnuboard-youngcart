<?php
/**
 * Sports Tech 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$text = [
    'ko' => [
        'title' => 'Sports Tech',
        'subtitle' => '스포츠 웨어러블 기술',
        'hero_title' => '스포츠 성능을 한 단계 끌어올리다',
        'hero_desc' => '최첨단 센서 기술과 데이터 분석을 통해 운동 성능을 최적화합니다.',
        'section1_title' => 'DIVA for swim',
        'section1_desc' => '수영 분석용 스포츠 웨어러블 디바이스로 수영자의 자세와 성능을 실시간으로 분석합니다.',
        'section2_title' => 'Smart ICT Cycling Glove',
        'section2_desc' => '사이클 장갑에 통합된 스마트 기술로 라이딩 데이터를 수집하고 분석합니다.',
        'features_title' => '주요 기능',
        'feature1' => '실시간 성능 모니터링',
        'feature1_desc' => '운동 중 실시간으로 데이터를 수집하고 분석합니다.',
        'feature2' => '정확한 센서 기술',
        'feature2_desc' => '고정밀 센서로 정확한 운동 데이터를 측정합니다.',
        'feature3' => '데이터 분석 플랫폼',
        'feature3_desc' => '수집된 데이터를 분석하여 성능 개선 방안을 제시합니다.',
        'cta_title' => '스포츠 기술로 성능을 향상시키세요',
        'cta_button' => '제품 보기'
    ],
    'en' => [
        'title' => 'Sports Tech',
        'subtitle' => 'Sports Wearable Technology',
        'hero_title' => 'Elevate Your Sports Performance',
        'hero_desc' => 'Optimize athletic performance through cutting-edge sensor technology and data analysis.',
        'section1_title' => 'DIVA for swim',
        'section1_desc' => 'A sports wearable device for swimming analysis that analyzes swimmer posture and performance in real-time.',
        'section2_title' => 'Smart ICT Cycling Glove',
        'section2_desc' => 'Smart technology integrated into cycling gloves to collect and analyze riding data.',
        'features_title' => 'Key Features',
        'feature1' => 'Real-time Performance Monitoring',
        'feature1_desc' => 'Collect and analyze data in real-time during exercise.',
        'feature2' => 'Precise Sensor Technology',
        'feature2_desc' => 'Measure accurate exercise data with high-precision sensors.',
        'feature3' => 'Data Analysis Platform',
        'feature3_desc' => 'Analyze collected data to suggest performance improvement strategies.',
        'cta_title' => 'Enhance Performance with Sports Technology',
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
    <title><?php echo htmlspecialchars($t['title'] ?? 'Sports Tech'); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .page-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .section-item {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
            align-items: center;
        }
        .section-item:nth-child(even) {
            direction: rtl;
        }
        .section-item:nth-child(even) > * {
            direction: ltr;
        }
        .section-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #667eea;
        }
        .section-content h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }
        .section-content p {
            font-size: 18px;
            line-height: 1.8;
            color: #666;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 60px;
        }
        .feature-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
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
            .section-item {
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
            <h1><?php echo htmlspecialchars($t['hero_title'] ?? '스포츠 성능을 한 단계 끌어올리다'); ?></h1>
            <p><?php echo htmlspecialchars($t['hero_desc'] ?? '최첨단 센서 기술과 데이터 분석을 통해 운동 성능을 최적화합니다.'); ?></p>
        </div>
    </section>
    
    <main class="content-section">
        <div class="container">
            <div class="section-item">
                <div class="section-image">🏊</div>
                <div class="section-content">
                    <h2><?php echo htmlspecialchars($t['section1_title'] ?? 'DIVA for swim'); ?></h2>
                    <p><?php echo htmlspecialchars($t['section1_desc'] ?? '수영 분석용 스포츠 웨어러블 디바이스로 수영자의 자세와 성능을 실시간으로 분석합니다.'); ?></p>
                    <a href="product.php?type=diva&lang=<?php echo $lang; ?>" style="display: inline-block; margin-top: 20px; color: #667eea; text-decoration: none; font-weight: bold;">자세히 보기 →</a>
                </div>
            </div>
            
            <div class="section-item">
                <div class="section-image">🚴</div>
                <div class="section-content">
                    <h2><?php echo htmlspecialchars($t['section2_title'] ?? 'Smart ICT Cycling Glove'); ?></h2>
                    <p><?php echo htmlspecialchars($t['section2_desc'] ?? '사이클 장갑에 통합된 스마트 기술로 라이딩 데이터를 수집하고 분석합니다.'); ?></p>
                </div>
            </div>
            
            <div style="margin-top: 100px;">
                <h2 style="text-align: center; font-size: 36px; margin-bottom: 40px;"><?php echo htmlspecialchars($t['features_title'] ?? '주요 기능'); ?></h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h3><?php echo htmlspecialchars($t['feature1'] ?? '실시간 성능 모니터링'); ?></h3>
                        <p><?php echo htmlspecialchars($t['feature1_desc'] ?? '운동 중 실시간으로 데이터를 수집하고 분석합니다.'); ?></p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <h3><?php echo htmlspecialchars($t['feature2'] ?? '정확한 센서 기술'); ?></h3>
                        <p><?php echo htmlspecialchars($t['feature2_desc'] ?? '고정밀 센서로 정확한 운동 데이터를 측정합니다.'); ?></p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💡</div>
                        <h3><?php echo htmlspecialchars($t['feature3'] ?? '데이터 분석 플랫폼'); ?></h3>
                        <p><?php echo htmlspecialchars($t['feature3_desc'] ?? '수집된 데이터를 분석하여 성능 개선 방안을 제시합니다.'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <section class="cta-section">
        <div class="container">
            <h2><?php echo htmlspecialchars($t['cta_title'] ?? '스포츠 기술로 성능을 향상시키세요'); ?></h2>
            <a href="shop.php?lang=<?php echo $lang; ?>" class="cta-button"><?php echo htmlspecialchars($t['cta_button'] ?? '제품 보기'); ?></a>
        </div>
    </section>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

