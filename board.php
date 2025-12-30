<?php
/**
 * 게시판 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$board_text = [
    'ko' => [
        'no_board' => '게시판이 존재하지 않습니다.',
        'no' => '번호',
        'subject' => '제목',
        'writer' => '작성자',
        'date' => '작성일',
        'hit' => '조회',
        'no_posts' => '등록된 게시글이 없습니다.',
        'prev' => '이전',
        'next' => '다음'
    ],
    'en' => [
        'no_board' => 'Board does not exist.',
        'no' => 'No.',
        'subject' => 'Subject',
        'writer' => 'Writer',
        'date' => 'Date',
        'hit' => 'Views',
        'no_posts' => 'No posts available.',
        'prev' => 'Previous',
        'next' => 'Next'
    ]
];

$bt = $board_text[$lang] ?? $board_text['ko'];

$bo_table = $_GET['bo_table'] ?? 'free';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 15;
$offset = ($page - 1) * $per_page;

// 게시판 정보 조회
$board = g5_fetch("SELECT * FROM g5_board WHERE bo_table = ?", [$bo_table]);

if (!$board) {
    die($bt['no_board']);
}

// 게시글 목록 조회
$table_name = 'g5_write_' . $bo_table;
$total = g5_fetch("SELECT COUNT(*) as cnt FROM $table_name")['cnt'];
$posts = g5_fetch_all("SELECT * FROM $table_name ORDER BY wr_num DESC, wr_id DESC LIMIT ? OFFSET ?", [$per_page, $offset]);

$total_pages = ceil($total / $per_page);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($board['bo_subject']); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .board-container {
            padding: 60px 0;
        }
        .board-header {
            margin-bottom: 30px;
        }
        .board-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .board-list {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .board-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .board-list th,
        .board-list td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .board-list th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .board-list tr:hover {
            background: #f9f9f9;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        .pagination a {
            padding: 8px 15px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
        }
        .pagination a:hover,
        .pagination a.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="board-container">
        <div class="container">
            <div class="board-header">
                <h1><?php echo htmlspecialchars($board['bo_subject']); ?></h1>
            </div>
            <div class="board-list">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php echo htmlspecialchars($bt['no']); ?></th>
                            <th><?php echo htmlspecialchars($bt['subject']); ?></th>
                            <th style="width: 120px;"><?php echo htmlspecialchars($bt['writer']); ?></th>
                            <th style="width: 120px;"><?php echo htmlspecialchars($bt['date']); ?></th>
                            <th style="width: 80px;"><?php echo htmlspecialchars($bt['hit']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;"><?php echo htmlspecialchars($bt['no_posts']); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?php echo $post['wr_id']; ?></td>
                                    <td>
                                        <a href="view.php?bo_table=<?php echo $bo_table; ?>&wr_id=<?php echo $post['wr_id']; ?>">
                                            <?php echo htmlspecialchars($post['wr_subject']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($post['wr_name']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($post['wr_datetime'])); ?></td>
                                    <td><?php echo $post['wr_hit']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?bo_table=<?php echo $bo_table; ?>&page=<?php echo $page - 1; ?>&lang=<?php echo $lang; ?>"><?php echo htmlspecialchars($bt['prev']); ?></a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?bo_table=<?php echo $bo_table; ?>&page=<?php echo $i; ?>&lang=<?php echo $lang; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?bo_table=<?php echo $bo_table; ?>&page=<?php echo $page + 1; ?>&lang=<?php echo $lang; ?>"><?php echo htmlspecialchars($bt['next']); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

