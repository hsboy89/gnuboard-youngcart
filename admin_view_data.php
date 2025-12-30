<?php
/**
 * GUI 기반 데이터 관리 페이지
 * 회원, 게시판, 게시글, 상품, 카테고리 관리
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 언어 설정
$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

// 관리자 권한 확인
$is_admin = isset($_SESSION['mb_level']) && $_SESSION['mb_level'] >= 10;

if (!$is_admin) {
    die('관리자만 접근할 수 있습니다. <a href="login.php?lang=' . $lang . '">로그인</a>');
}

$table = $_GET['table'] ?? 'g5_member';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// 다국어 텍스트
$text = [
    'ko' => [
        'title' => '데이터 관리',
        'members' => '회원',
        'boards' => '게시판',
        'posts' => '게시글',
        'products' => '상품',
        'categories' => '카테고리',
        'add' => '추가',
        'edit' => '수정',
        'delete' => '삭제',
        'save' => '저장',
        'cancel' => '취소',
        'confirm_delete' => '정말 삭제하시겠습니까?',
        'success' => '성공적으로 처리되었습니다.',
        'error' => '오류가 발생했습니다.',
        'no_data' => '데이터가 없습니다.'
    ],
    'en' => [
        'title' => 'Data Management',
        'members' => 'Members',
        'boards' => 'Boards',
        'posts' => 'Posts',
        'products' => 'Products',
        'categories' => 'Categories',
        'add' => 'Add',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'confirm_delete' => 'Are you sure you want to delete?',
        'success' => 'Successfully processed.',
        'error' => 'An error occurred.',
        'no_data' => 'No data available.'
    ]
];
// 언어별 텍스트 가져오기 (누락된 키는 한국어로 채우기)
$t = isset($text[$lang]) ? $text[$lang] : $text['ko'];
$t = array_merge($text['ko'], $t);

// POST 처리
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['action'] ?? '';
    
    try {
        if ($post_action === 'create' || $post_action === 'update' || $post_action === 'edit') {
            // edit를 update로 변환
            if ($post_action === 'edit') {
                $post_action = 'update';
            }
            $result = handle_form_submit($table, $post_action, $_POST);
            if ($result) {
                $message = $t['success'];
                $message_type = 'success';
                $action = 'list'; // 목록으로 리다이렉트
            }
        } elseif ($post_action === 'delete') {
            $result = handle_delete($table, $_POST['id'] ?? null);
            if ($result) {
                $message = $t['success'];
                $message_type = 'success';
                $action = 'list';
            }
        }
    } catch (Exception $e) {
        $message = $t['error'] . ': ' . $e->getMessage();
        $message_type = 'error';
    }
}

// 폼 제출 처리 함수
function handle_form_submit($table, $action, $data) {
    $db = g5_get_db();
    
    if ($table === 'g5_member') {
        if ($action === 'create') {
            $stmt = $db->prepare("INSERT INTO g5_member (mb_id, mb_password, mb_name, mb_email, mb_level) VALUES (?, ?, ?, ?, ?)");
            $password = password_hash($data['mb_password'], PASSWORD_DEFAULT);
            $stmt->execute([$data['mb_id'], $password, $data['mb_name'], $data['mb_email'], $data['mb_level'] ?? 1]);
        } else {
            $stmt = $db->prepare("UPDATE g5_member SET mb_name = ?, mb_email = ?, mb_level = ? WHERE mb_no = ?");
            $stmt->execute([$data['mb_name'], $data['mb_email'], $data['mb_level'] ?? 1, $data['mb_no']]);
            if (!empty($data['mb_password'])) {
                $stmt = $db->prepare("UPDATE g5_member SET mb_password = ? WHERE mb_no = ?");
                $password = password_hash($data['mb_password'], PASSWORD_DEFAULT);
                $stmt->execute([$password, $data['mb_no']]);
            }
        }
    } elseif ($table === 'g5_board') {
        if ($action === 'create') {
            $stmt = $db->prepare("INSERT INTO g5_board (bo_table, bo_subject, bo_skin, bo_use_category, bo_list_level, bo_read_level, bo_write_level) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['bo_table'], $data['bo_subject'], $data['bo_skin'] ?? 'basic', isset($data['bo_use_category']) ? 1 : 0, $data['bo_list_level'] ?? 1, $data['bo_read_level'] ?? 1, $data['bo_write_level'] ?? 1]);
        } else {
            $stmt = $db->prepare("UPDATE g5_board SET bo_subject = ?, bo_skin = ?, bo_use_category = ?, bo_list_level = ?, bo_read_level = ?, bo_write_level = ? WHERE bo_table = ?");
            $stmt->execute([$data['bo_subject'], $data['bo_skin'], isset($data['bo_use_category']) ? 1 : 0, $data['bo_list_level'] ?? 1, $data['bo_read_level'] ?? 1, $data['bo_write_level'] ?? 1, $data['bo_table']]);
        }
    } elseif ($table === 'g5_write_free') {
        if ($action === 'create') {
            $stmt = $db->prepare("INSERT INTO g5_write_free (wr_subject, wr_content, mb_id, wr_name, wr_email, wr_notice, wr_secret) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['wr_subject'], $data['wr_content'], $data['mb_id'] ?? '', $data['wr_name'], $data['wr_email'] ?? '', isset($data['wr_notice']) ? 1 : 0, isset($data['wr_secret']) ? 1 : 0]);
        } else {
            $stmt = $db->prepare("UPDATE g5_write_free SET wr_subject = ?, wr_content = ?, mb_id = ?, wr_name = ?, wr_email = ?, wr_notice = ?, wr_secret = ? WHERE wr_id = ?");
            $stmt->execute([$data['wr_subject'], $data['wr_content'], $data['mb_id'] ?? '', $data['wr_name'], $data['wr_email'] ?? '', isset($data['wr_notice']) ? 1 : 0, isset($data['wr_secret']) ? 1 : 0, $data['wr_id']]);
        }
    } elseif ($table === 'g5_shop_item') {
        if ($action === 'create') {
            $stmt = $db->prepare("INSERT INTO g5_shop_item (ca_id, it_name, it_maker, it_origin, it_img1, it_cust_price, it_price, it_stock_qty, it_content, it_use, it_sell_use) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['ca_id'] ?? '', $data['it_name'], $data['it_maker'] ?? '', $data['it_origin'] ?? '', $data['it_img1'] ?? '', $data['it_cust_price'] ?? 0, $data['it_price'] ?? 0, $data['it_stock_qty'] ?? 0, $data['it_content'] ?? '', isset($data['it_use']) ? 1 : 0, isset($data['it_sell_use']) ? 1 : 0]);
        } else {
            $stmt = $db->prepare("UPDATE g5_shop_item SET ca_id = ?, it_name = ?, it_maker = ?, it_origin = ?, it_img1 = ?, it_cust_price = ?, it_price = ?, it_stock_qty = ?, it_content = ?, it_use = ?, it_sell_use = ? WHERE it_id = ?");
            $stmt->execute([$data['ca_id'] ?? '', $data['it_name'], $data['it_maker'] ?? '', $data['it_origin'] ?? '', $data['it_img1'] ?? '', $data['it_cust_price'] ?? 0, $data['it_price'] ?? 0, $data['it_stock_qty'] ?? 0, $data['it_content'] ?? '', isset($data['it_use']) ? 1 : 0, isset($data['it_sell_use']) ? 1 : 0, $data['it_id']]);
        }
    } elseif ($table === 'g5_shop_category') {
        if ($action === 'create') {
            $stmt = $db->prepare("INSERT INTO g5_shop_category (ca_id, ca_name, ca_order, ca_use) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['ca_id'], $data['ca_name'], $data['ca_order'] ?? 0, isset($data['ca_use']) ? 1 : 0]);
        } else {
            $stmt = $db->prepare("UPDATE g5_shop_category SET ca_name = ?, ca_order = ?, ca_use = ? WHERE ca_id = ?");
            $stmt->execute([$data['ca_name'], $data['ca_order'] ?? 0, isset($data['ca_use']) ? 1 : 0, $data['ca_id']]);
        }
    }
    
    return true;
}

// 삭제 처리 함수
function handle_delete($table, $id) {
    if (!$id) return false;
    
    $db = g5_get_db();
    
    if ($table === 'g5_member') {
        $stmt = $db->prepare("DELETE FROM g5_member WHERE mb_no = ?");
    } elseif ($table === 'g5_board') {
        $stmt = $db->prepare("DELETE FROM g5_board WHERE bo_table = ?");
    } elseif ($table === 'g5_write_free') {
        $stmt = $db->prepare("DELETE FROM g5_write_free WHERE wr_id = ?");
    } elseif ($table === 'g5_shop_item') {
        $stmt = $db->prepare("DELETE FROM g5_shop_item WHERE it_id = ?");
    } elseif ($table === 'g5_shop_category') {
        $stmt = $db->prepare("DELETE FROM g5_shop_category WHERE ca_id = ?");
    } else {
        return false;
    }
    
    $stmt->execute([$id]);
    return true;
}

// 테이블별 필드 정의
$table_fields = [
    'g5_member' => [
        'mb_no' => ['type' => 'hidden', 'label' => '번호'],
        'mb_id' => ['type' => 'text', 'label' => '아이디', 'required' => true],
        'mb_password' => ['type' => 'password', 'label' => '비밀번호', 'required' => false, 'edit_note' => '수정 시에만 입력'],
        'mb_name' => ['type' => 'text', 'label' => '이름', 'required' => true],
        'mb_email' => ['type' => 'email', 'label' => '이메일'],
        'mb_level' => ['type' => 'number', 'label' => '권한레벨', 'default' => 1, 'note' => '10=관리자, 1=일반']
    ],
    'g5_board' => [
        'bo_table' => ['type' => 'text', 'label' => '게시판ID', 'required' => true],
        'bo_subject' => ['type' => 'text', 'label' => '게시판명', 'required' => true],
        'bo_skin' => ['type' => 'text', 'label' => '스킨', 'default' => 'basic'],
        'bo_use_category' => ['type' => 'checkbox', 'label' => '카테고리 사용'],
        'bo_list_level' => ['type' => 'number', 'label' => '목록 권한', 'default' => 1],
        'bo_read_level' => ['type' => 'number', 'label' => '읽기 권한', 'default' => 1],
        'bo_write_level' => ['type' => 'number', 'label' => '쓰기 권한', 'default' => 1]
    ],
    'g5_write_free' => [
        'wr_id' => ['type' => 'hidden', 'label' => '번호'],
        'wr_subject' => ['type' => 'text', 'label' => '제목', 'required' => true],
        'wr_content' => ['type' => 'textarea', 'label' => '내용', 'required' => true],
        'mb_id' => ['type' => 'text', 'label' => '작성자ID'],
        'wr_name' => ['type' => 'text', 'label' => '작성자명', 'required' => true],
        'wr_email' => ['type' => 'email', 'label' => '이메일'],
        'wr_notice' => ['type' => 'checkbox', 'label' => '공지사항'],
        'wr_secret' => ['type' => 'checkbox', 'label' => '비밀글']
    ],
    'g5_shop_item' => [
        'it_id' => ['type' => 'hidden', 'label' => '번호'],
        'ca_id' => ['type' => 'text', 'label' => '카테고리ID'],
        'it_name' => ['type' => 'text', 'label' => '상품명', 'required' => true],
        'it_maker' => ['type' => 'text', 'label' => '제조사'],
        'it_origin' => ['type' => 'text', 'label' => '원산지'],
        'it_img1' => ['type' => 'text', 'label' => '이미지1 URL'],
        'it_cust_price' => ['type' => 'number', 'label' => '정가', 'default' => 0],
        'it_price' => ['type' => 'number', 'label' => '판매가', 'default' => 0],
        'it_stock_qty' => ['type' => 'number', 'label' => '재고수량', 'default' => 0],
        'it_content' => ['type' => 'textarea', 'label' => '상품설명'],
        'it_use' => ['type' => 'checkbox', 'label' => '사용', 'default' => 1],
        'it_sell_use' => ['type' => 'checkbox', 'label' => '판매', 'default' => 1]
    ],
    'g5_shop_category' => [
        'ca_id' => ['type' => 'text', 'label' => '카테고리ID', 'required' => true],
        'ca_name' => ['type' => 'text', 'label' => '카테고리명', 'required' => true],
        'ca_order' => ['type' => 'number', 'label' => '순서', 'default' => 0],
        'ca_use' => ['type' => 'checkbox', 'label' => '사용', 'default' => 1]
    ]
];

$table_names = [
    'ko' => ['g5_member' => '회원', 'g5_board' => '게시판', 'g5_write_free' => '게시글', 'g5_shop_item' => '상품', 'g5_shop_category' => '카테고리'],
    'en' => ['g5_member' => 'Members', 'g5_board' => 'Boards', 'g5_write_free' => 'Posts', 'g5_shop_item' => 'Products', 'g5_shop_category' => 'Categories']
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title'] ?? '데이터 관리'); ?> - Admin</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .admin-container {
            padding: 40px 0;
            min-height: 60vh;
        }
        .admin-header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .admin-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .admin-nav a {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .admin-nav a.active {
            background: #0056b3;
        }
        .admin-nav a:hover {
            background: #0056b3;
        }
        .admin-actions {
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn-primary {
            background: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-danger {
            background: #dc3545;
            color: #fff;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-success {
            background: #28a745;
            color: #fff;
        }
        .btn-success:hover {
            background: #218838;
        }
        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 800px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group input[type="password"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-group input[type="checkbox"] {
            width: auto;
            margin-right: 5px;
        }
        .form-group .note {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .message {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="admin-container">
        <div class="container">
            <div class="admin-header">
                <h1><?php echo htmlspecialchars($t['title'] ?? '데이터 관리'); ?></h1>
                <p><?php echo $lang === 'en' ? 'Admin' : '관리자'; ?>: <?php echo htmlspecialchars($_SESSION['mb_name'] ?? 'Unknown'); ?></p>
                <div class="admin-nav">
                    <a href="?table=g5_member&lang=<?php echo $lang; ?>" class="<?php echo $table === 'g5_member' ? 'active' : ''; ?>"><?php echo htmlspecialchars($t['members'] ?? '회원'); ?></a>
                    <a href="?table=g5_board&lang=<?php echo $lang; ?>" class="<?php echo $table === 'g5_board' ? 'active' : ''; ?>"><?php echo htmlspecialchars($t['boards'] ?? '게시판'); ?></a>
                    <a href="?table=g5_write_free&lang=<?php echo $lang; ?>" class="<?php echo $table === 'g5_write_free' ? 'active' : ''; ?>"><?php echo htmlspecialchars($t['posts'] ?? '게시글'); ?></a>
                    <a href="?table=g5_shop_item&lang=<?php echo $lang; ?>" class="<?php echo $table === 'g5_shop_item' ? 'active' : ''; ?>"><?php echo htmlspecialchars($t['products'] ?? '상품'); ?></a>
                    <a href="?table=g5_shop_category&lang=<?php echo $lang; ?>" class="<?php echo $table === 'g5_shop_category' ? 'active' : ''; ?>"><?php echo htmlspecialchars($t['categories'] ?? '카테고리'); ?></a>
                    <a href="index.php?lang=<?php echo $lang; ?>"><?php echo $lang === 'en' ? 'Home' : '메인으로'; ?></a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php
            if ($action === 'create' || $action === 'edit') {
                // 폼 표시
                $item = null;
                if ($action === 'edit' && $id) {
                    if ($table === 'g5_member') {
                        $item = g5_fetch("SELECT * FROM g5_member WHERE mb_no = ?", [$id]);
                    } elseif ($table === 'g5_board') {
                        $item = g5_fetch("SELECT * FROM g5_board WHERE bo_table = ?", [$id]);
                    } elseif ($table === 'g5_write_free') {
                        $item = g5_fetch("SELECT * FROM g5_write_free WHERE wr_id = ?", [$id]);
                    } elseif ($table === 'g5_shop_item') {
                        $item = g5_fetch("SELECT * FROM g5_shop_item WHERE it_id = ?", [$id]);
                    } elseif ($table === 'g5_shop_category') {
                        $item = g5_fetch("SELECT * FROM g5_shop_category WHERE ca_id = ?", [$id]);
                    }
                }
                
                $fields = $table_fields[$table] ?? [];
                ?>
                <div class="form-container">
                    <h2><?php echo $action === 'create' ? ($t['add'] ?? '추가') : ($t['edit'] ?? '수정'); ?> <?php echo htmlspecialchars($table_names[$lang][$table] ?? $table); ?></h2>
                    <form method="POST" action="?table=<?php echo $table; ?>&action=<?php echo $action; ?>&lang=<?php echo $lang; ?>">
                        <input type="hidden" name="action" value="<?php echo $action === 'edit' ? 'update' : $action; ?>">
                        <?php foreach ($fields as $field_name => $field_config): ?>
                            <?php if ($field_config['type'] === 'hidden'): ?>
                                <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo htmlspecialchars($item[$field_name] ?? ''); ?>">
                            <?php else: ?>
                                <div class="form-group">
                                    <label><?php echo htmlspecialchars($field_config['label']); ?><?php echo isset($field_config['required']) && $field_config['required'] ? ' *' : ''; ?></label>
                                    <?php if ($field_config['type'] === 'textarea'): ?>
                                        <textarea name="<?php echo $field_name; ?>" <?php echo isset($field_config['required']) && $field_config['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars($item[$field_name] ?? ''); ?></textarea>
                                    <?php elseif ($field_config['type'] === 'checkbox'): ?>
                                        <label>
                                            <input type="checkbox" name="<?php echo $field_name; ?>" value="1" <?php echo ($item[$field_name] ?? $field_config['default'] ?? 0) ? 'checked' : ''; ?>>
                                            <?php echo htmlspecialchars($field_config['label']); ?>
                                        </label>
                                    <?php else: ?>
                                        <input type="<?php echo $field_config['type']; ?>" name="<?php echo $field_name; ?>" value="<?php echo htmlspecialchars($item[$field_name] ?? $field_config['default'] ?? ''); ?>" <?php echo isset($field_config['required']) && $field_config['required'] ? 'required' : ''; ?> <?php echo ($field_name === 'mb_id' && $action === 'edit') ? 'readonly' : ''; ?> <?php echo ($field_name === 'bo_table' && $action === 'edit') ? 'readonly' : ''; ?> <?php echo ($field_name === 'ca_id' && $action === 'edit' && $table === 'g5_shop_category') ? 'readonly' : ''; ?>>
                                    <?php endif; ?>
                                    <?php if (isset($field_config['note'])): ?>
                                        <div class="note"><?php echo htmlspecialchars($field_config['note']); ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($field_config['edit_note']) && $action === 'edit'): ?>
                                        <div class="note"><?php echo htmlspecialchars($field_config['edit_note']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-success"><?php echo $t['save'] ?? '저장'; ?></button>
                            <a href="?table=<?php echo $table; ?>&lang=<?php echo $lang; ?>" class="btn btn-primary"><?php echo $t['cancel'] ?? '취소'; ?></a>
                        </div>
                    </form>
                </div>
            <?php } else { ?>
                <!-- 목록 표시 -->
                <div class="admin-actions">
                    <a href="?table=<?php echo $table; ?>&action=create&lang=<?php echo $lang; ?>" class="btn btn-primary"><?php echo $t['add'] ?? '추가'; ?> <?php echo htmlspecialchars($table_names[$lang][$table] ?? $table); ?></a>
                </div>
                
                <div class="table-container">
                    <?php
                    try {
                        $data = g5_fetch_all("SELECT * FROM $table ORDER BY " . ($table === 'g5_member' ? 'mb_no' : ($table === 'g5_board' ? 'bo_table' : ($table === 'g5_write_free' ? 'wr_id' : ($table === 'g5_shop_item' ? 'it_id' : 'ca_id')))) . " DESC LIMIT 100");
                        
                        $table_name = $table_names[$lang][$table] ?? $table;
                        $count_text = $lang === 'en' ? 'items' : '개';
                        
                        echo '<h2>' . htmlspecialchars($table_name) . ' (' . count($data) . $count_text . ')</h2>';
                        
                        if (!empty($data)) {
                            echo '<table>';
                            echo '<thead><tr>';
                            foreach (array_keys($data[0]) as $key) {
                                echo '<th>' . htmlspecialchars($key) . '</th>';
                            }
                            echo '<th>' . ($lang === 'en' ? 'Actions' : '작업') . '</th>';
                            echo '</tr></thead>';
                            echo '<tbody>';
                            foreach ($data as $row) {
                                echo '<tr>';
                                foreach ($row as $key => $value) {
                                    $display_value = $value;
                                    if (strpos($key, 'password') !== false) {
                                        $display_value = '***';
                                    } elseif (strpos($key, 'img') !== false && !empty($value)) {
                                        // 이미지 필드는 실제 이미지로 표시
                                        $display_value = '<img src="' . htmlspecialchars($value) . '" alt="' . htmlspecialchars($key) . '" style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 4px;">';
                                    } elseif (strpos($key, 'content') !== false && $value !== null && strlen($value) > 50) {
                                        $display_value = substr($value, 0, 50) . '...';
                                    }
                                    
                                    // 이미지인 경우 HTML 그대로 출력, 아니면 텍스트로 출력
                                    if (strpos($key, 'img') !== false && !empty($value)) {
                                        echo '<td>' . $display_value . '</td>';
                                    } else {
                                        echo '<td>' . htmlspecialchars($display_value ?? '') . '</td>';
                                    }
                                }
                                // 작업 버튼
                                $id_field = $table === 'g5_member' ? 'mb_no' : ($table === 'g5_board' ? 'bo_table' : ($table === 'g5_write_free' ? 'wr_id' : ($table === 'g5_shop_item' ? 'it_id' : 'ca_id')));
                                $id_value = $row[$id_field];
                                echo '<td class="action-buttons">';
                                echo '<a href="?table=' . $table . '&action=edit&id=' . urlencode($id_value) . '&lang=' . $lang . '" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">' . ($t['edit'] ?? '수정') . '</a> ';
                                echo '<form method="POST" action="?table=' . $table . '&lang=' . $lang . '" style="display:inline;" onsubmit="return confirm(\'' . ($t['confirm_delete'] ?? '정말 삭제하시겠습니까?') . '\');">';
                                echo '<input type="hidden" name="action" value="delete">';
                                echo '<input type="hidden" name="id" value="' . htmlspecialchars($id_value) . '">';
                                echo '<button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">' . ($t['delete'] ?? '삭제') . '</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo '<p>' . ($t['no_data'] ?? '데이터가 없습니다.') . '</p>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="message error">' . ($t['error'] ?? '오류가 발생했습니다.') . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    </main>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>
