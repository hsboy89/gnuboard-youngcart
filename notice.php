<?php
/**
 * Notice 페이지 - 게시판 통합 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$text = [
    'ko' => [
        'title' => 'Notice',
        'subtitle' => '공지사항 및 소식',
        'news_title' => 'News',
        'news_desc' => '회사 소식과 업데이트를 확인하세요.',
        'review_title' => 'Review',
        'review_desc' => '고객 리뷰와 후기를 확인하세요.',
        'info_title' => 'Info',
        'info_desc' => '유용한 정보와 가이드를 확인하세요.',
        'view_all' => '전체 보기',
        'no_posts' => '등록된 게시글이 없습니다.'
    ],
    'en' => [
        'title' => 'Notice',
        'subtitle' => 'Notices and News',
        'news_title' => 'News',
        'news_desc' => 'Check company news and updates.',
        'review_title' => 'Review',
        'review_desc' => 'Check customer reviews and testimonials.',
        'info_title' => 'Info',
        'info_desc' => 'Check useful information and guides.',
        'view_all' => 'View All',
        'no_posts' => 'No posts available.'
    ]
];

$t = $text[$lang] ?? $text['ko'];
$t = array_merge($text['ko'], $t);

// 각 게시판의 최신 게시글 가져오기
$news_posts = g5_fetch_all("SELECT * FROM g5_write_free WHERE mb_id = 'admin' ORDER BY wr_datetime DESC LIMIT 3");
$review_posts = g5_fetch_all("SELECT * FROM g5_write_free WHERE mb_id != 'admin' ORDER BY wr_datetime DESC LIMIT 3");
$info_posts = g5_fetch_all("SELECT * FROM g5_write_free ORDER BY wr_hit DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title'] ?? 'Notice'); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .notice-hero {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: #fff;
            padding: 80px 0;
            text-align: center;
        }
        .notice-hero h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .notice-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        .content-section {
            padding: 80px 0;
        }
        .boards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 60px;
        }
        .board-card {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .board-card h2 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 3px solid #fa709a;
            padding-bottom: 10px;
        }
        .board-card p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .post-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .post-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .post-item:last-child {
            border-bottom: none;
        }
        .post-item a {
            color: #333;
            text-decoration: none;
            display: block;
            transition: color 0.3s;
        }
        .post-item a:hover {
            color: #fa709a;
        }
        .post-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .post-date {
            font-size: 12px;
            color: #999;
        }
        .view-all-link {
            display: inline-block;
            margin-top: 20px;
            color: #fa709a;
            text-decoration: none;
            font-weight: bold;
        }
        .view-all-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .boards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <section class="notice-hero">
        <div class="container">
            <h1><?php echo htmlspecialchars($t['title'] ?? 'Notice'); ?></h1>
            <p><?php echo htmlspecialchars($t['subtitle'] ?? '공지사항 및 소식'); ?></p>
        </div>
    </section>
    
    <main class="content-section">
        <div class="container">
            <div class="boards-grid">
                <!-- News 게시판 -->
                <div class="board-card">
                    <h2><?php echo htmlspecialchars($t['news_title'] ?? 'News'); ?></h2>
                    <p><?php echo htmlspecialchars($t['news_desc'] ?? '회사 소식과 업데이트를 확인하세요.'); ?></p>
                    <ul class="post-list">
                        <?php if (empty($news_posts)): ?>
                            <li class="post-item"><?php echo htmlspecialchars($t['no_posts'] ?? '등록된 게시글이 없습니다.'); ?></li>
                        <?php else: ?>
                            <?php foreach ($news_posts as $post): ?>
                                <li class="post-item">
                                    <a href="board.php?bo_table=news&wr_id=<?php echo $post['wr_id']; ?>&lang=<?php echo $lang; ?>">
                                        <div class="post-title"><?php echo htmlspecialchars($post['wr_subject'] ?? '제목 없음'); ?></div>
                                        <div class="post-date"><?php echo date('Y-m-d', strtotime($post['wr_datetime'])); ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="board.php?bo_table=news&lang=<?php echo $lang; ?>" class="view-all-link"><?php echo htmlspecialchars($t['view_all'] ?? '전체 보기'); ?> →</a>
                </div>
                
                <!-- Review 게시판 -->
                <div class="board-card">
                    <h2><?php echo htmlspecialchars($t['review_title'] ?? 'Review'); ?></h2>
                    <p><?php echo htmlspecialchars($t['review_desc'] ?? '고객 리뷰와 후기를 확인하세요.'); ?></p>
                    <ul class="post-list">
                        <?php if (empty($review_posts)): ?>
                            <li class="post-item"><?php echo htmlspecialchars($t['no_posts'] ?? '등록된 게시글이 없습니다.'); ?></li>
                        <?php else: ?>
                            <?php foreach ($review_posts as $post): ?>
                                <li class="post-item">
                                    <a href="board.php?bo_table=review&wr_id=<?php echo $post['wr_id']; ?>&lang=<?php echo $lang; ?>">
                                        <div class="post-title"><?php echo htmlspecialchars($post['wr_subject'] ?? '제목 없음'); ?></div>
                                        <div class="post-date"><?php echo date('Y-m-d', strtotime($post['wr_datetime'])); ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="board.php?bo_table=review&lang=<?php echo $lang; ?>" class="view-all-link"><?php echo htmlspecialchars($t['view_all'] ?? '전체 보기'); ?> →</a>
                </div>
                
                <!-- Info 게시판 -->
                <div class="board-card">
                    <h2><?php echo htmlspecialchars($t['info_title'] ?? 'Info'); ?></h2>
                    <p><?php echo htmlspecialchars($t['info_desc'] ?? '유용한 정보와 가이드를 확인하세요.'); ?></p>
                    <ul class="post-list">
                        <?php if (empty($info_posts)): ?>
                            <li class="post-item"><?php echo htmlspecialchars($t['no_posts'] ?? '등록된 게시글이 없습니다.'); ?></li>
                        <?php else: ?>
                            <?php foreach ($info_posts as $post): ?>
                                <li class="post-item">
                                    <a href="board.php?bo_table=info&wr_id=<?php echo $post['wr_id']; ?>&lang=<?php echo $lang; ?>">
                                        <div class="post-title"><?php echo htmlspecialchars($post['wr_subject'] ?? '제목 없음'); ?></div>
                                        <div class="post-date"><?php echo date('Y-m-d', strtotime($post['wr_datetime'])); ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="board.php?bo_table=info&lang=<?php echo $lang; ?>" class="view-all-link"><?php echo htmlspecialchars($t['view_all'] ?? '전체 보기'); ?> →</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

