<?php
/**
 * Gnuboard 메인 페이지
 */

// 설정 파일 로드
require_once 'data/dbconfig.php';

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 현재 언어 설정 (기본값: ko)
$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

// 다국어 텍스트
$text = [
    'ko' => [
        'title' => '메인',
        'hero_title' => '"미래를 입는다"',
        'hero_subtitle' => '웨어러블 디바이스 개발 전문 기업',
        'hero_desc1' => '웨어러블 디바이스 개발',
        'hero_desc2' => '센서데이터 처리 및 관리 플랫폼',
        'hero_desc3' => '전문 연구개발 기업',
        'company_story' => 'Company Story',
        'innovation' => '혁신',
        'innovation_desc' => '항상 새로운 기술과 디자인을 결합하여 한 발 앞서가는 혁신적인 제품을 개발합니다.',
        'joy' => '즐거움',
        'joy_desc' => '사용자의 삶에 새로운 즐거움을 주는 제품을 통해 일상의 만족도를 높입니다.',
        'health' => '건강',
        'health_desc' => '반려동물과 사람 모두의 건강과 웰빙을 최우선으로 생각하는 헬스케어 솔루션을 제공합니다.',
        'achievements_title' => 'Major Achievements',
        'achievements_subtitle' => '펫, 스포츠 웨어러블 디바이스 자체 개발과 중견기업의 대규모 프로젝트에 협업하여<br>웨어러블 기술을 지속적으로 다져 나갑니다.',
        'portfolio01_title' => 'PORTFOLIO 01',
        'portfolio01_desc' => '다무상회 반려견 헬스케어 웨어러블 디바이스<br>LUHearty 개발',
        'portfolio02_title' => 'PORTFOLIO 02',
        'portfolio02_desc' => '(주)시즈글로벌 Outsourced project<br>Smart ICT 사이클 장갑 개발',
        'portfolio03_title' => 'PORTFOLIO 03',
        'portfolio03_desc' => '수영 분석용 스포츠 웨어러블 디바이스<br>신제품 개발 중',
        'news_title' => 'News',
        'news_empty' => '등록된 뉴스가 없습니다.',
        'shop' => 'SHOP',
        'login' => 'LOGIN'
    ],
    'en' => [
        'title' => 'Main',
        'hero_title' => '"Wear the Future"',
        'hero_subtitle' => 'Wearable Device Development Specialist',
        'hero_desc1' => 'Wearable Device Development',
        'hero_desc2' => 'Sensor Data Processing and Management Platform',
        'hero_desc3' => 'Professional R&D Company',
        'company_story' => 'Company Story',
        'innovation' => 'Innovation',
        'innovation_desc' => 'We always combine new technology and design to develop innovative products that are one step ahead.',
        'joy' => 'Joy',
        'joy_desc' => 'We enhance daily satisfaction through products that bring new joy to users\' lives.',
        'health' => 'Health',
        'health_desc' => 'We provide healthcare solutions that prioritize the health and well-being of both pets and people.',
        'achievements_title' => 'Major Achievements',
        'achievements_subtitle' => 'We continue to strengthen wearable technology through in-house development of pet and sports wearable devices<br>and collaboration on large-scale projects with mid-sized companies.',
        'portfolio01_title' => 'PORTFOLIO 01',
        'portfolio01_desc' => '다무상회 Pet Healthcare Wearable Device<br>LUHearty Development',
        'portfolio02_title' => 'PORTFOLIO 02',
        'portfolio02_desc' => 'Seize Global Co., Ltd. Outsourced Project<br>Smart ICT Cycling Glove Development',
        'portfolio03_title' => 'PORTFOLIO 03',
        'portfolio03_desc' => 'Swimming Analysis Sports Wearable Device<br>New Product Under Development',
        'news_title' => 'News',
        'news_empty' => 'No news available.',
        'shop' => 'SHOP',
        'login' => 'LOGIN'
    ]
];

// 언어별 텍스트 가져오기 (기본값: 한국어)
$t = isset($text[$lang]) ? $text[$lang] : $text['ko'];
// 누락된 키가 있으면 한국어 버전으로 채우기
$t = array_merge($text['ko'], $t);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main>
        <section class="hero">
            <div class="container">
                <h1><?php echo htmlspecialchars(isset($t['hero_title']) ? $t['hero_title'] : ''); ?></h1>
                <h2><?php echo htmlspecialchars(isset($t['hero_subtitle']) ? $t['hero_subtitle'] : ''); ?></h2>
                <p><?php echo htmlspecialchars(isset($t['hero_desc1']) ? $t['hero_desc1'] : ''); ?><br>
                <?php echo htmlspecialchars(isset($t['hero_desc2']) ? $t['hero_desc2'] : ''); ?><br>
                <?php echo htmlspecialchars(isset($t['hero_desc3']) ? $t['hero_desc3'] : ''); ?></p>
            </div>
        </section>

        <section class="company-story">
            <div class="container">
                <h2><?php echo htmlspecialchars(isset($t['company_story']) ? $t['company_story'] : 'Company Story'); ?></h2>
                <div class="values">
                    <div class="value-item">
                        <div class="value-icon">💡</div>
                        <h3><?php echo htmlspecialchars(isset($t['innovation']) ? $t['innovation'] : '혁신'); ?></h3>
                        <p><?php echo htmlspecialchars(isset($t['innovation_desc']) ? $t['innovation_desc'] : '항상 새로운 기술과 디자인을 결합하여 한 발 앞서가는 혁신적인 제품을 개발합니다.'); ?></p>
                    </div>
                    <div class="value-item">
                        <div class="value-icon">😊</div>
                        <h3><?php echo htmlspecialchars(isset($t['joy']) ? $t['joy'] : '즐거움'); ?></h3>
                        <p><?php echo htmlspecialchars(isset($t['joy_desc']) ? $t['joy_desc'] : '사용자의 삶에 새로운 즐거움을 주는 제품을 통해 일상의 만족도를 높입니다.'); ?></p>
                    </div>
                    <div class="value-item">
                        <div class="value-icon">❤️</div>
                        <h3><?php echo htmlspecialchars(isset($t['health']) ? $t['health'] : '건강'); ?></h3>
                        <p><?php echo htmlspecialchars(isset($t['health_desc']) ? $t['health_desc'] : '반려동물과 사람 모두의 건강과 웰빙을 최우선으로 생각하는 헬스케어 솔루션을 제공합니다.'); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="achievements">
            <div class="container">
                <h2><?php echo htmlspecialchars(isset($t['achievements_title']) ? $t['achievements_title'] : 'Major Achievements'); ?></h2>
                <p class="section-subtitle"><?php echo isset($t['achievements_subtitle']) ? $t['achievements_subtitle'] : ''; ?></p>
                <div class="portfolio-grid">
                    <div class="portfolio-item">
                        <div class="portfolio-image">📱</div>
                        <h3><?php echo htmlspecialchars(isset($t['portfolio01_title']) ? $t['portfolio01_title'] : 'PORTFOLIO 01'); ?></h3>
                        <p><?php echo isset($t['portfolio01_desc']) ? $t['portfolio01_desc'] : ''; ?></p>
                    </div>
                    <div class="portfolio-item">
                        <div class="portfolio-image">🚴</div>
                        <h3><?php echo htmlspecialchars(isset($t['portfolio02_title']) ? $t['portfolio02_title'] : 'PORTFOLIO 02'); ?></h3>
                        <p><?php echo isset($t['portfolio02_desc']) ? $t['portfolio02_desc'] : ''; ?></p>
                    </div>
                    <div class="portfolio-item">
                        <div class="portfolio-image">🏊</div>
                        <h3><?php echo htmlspecialchars(isset($t['portfolio03_title']) ? $t['portfolio03_title'] : 'PORTFOLIO 03'); ?></h3>
                        <p><?php echo isset($t['portfolio03_desc']) ? $t['portfolio03_desc'] : ''; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="news-section">
            <div class="container">
                <h2><?php echo htmlspecialchars(isset($t['news_title']) ? $t['news_title'] : 'News'); ?></h2>
                <div class="news-list">
                    <?php
                    $news = g5_fetch_all("SELECT * FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 3");
                    if (!empty($news)):
                        foreach ($news as $item):
                    ?>
                    <div class="news-item">
                        <div class="news-date"><?php echo date('Y-m-d', strtotime($item['wr_datetime'])); ?></div>
                        <div class="news-title"><?php echo htmlspecialchars($item['wr_subject']); ?></div>
                    </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                    <p><?php echo htmlspecialchars(isset($t['news_empty']) ? $t['news_empty'] : '등록된 뉴스가 없습니다.'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

