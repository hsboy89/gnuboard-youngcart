<?php
/**
 * 웹에서 주문 테이블 생성하는 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 관리자 권한 확인
$is_admin = isset($_SESSION['mb_level']) && $_SESSION['mb_level'] >= 10;

if (!$is_admin) {
    die('관리자만 접근할 수 있습니다. <a href="login.php">로그인</a>');
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_tables'])) {
    try {
        $db = g5_get_db();
        
        // 주문 테이블 생성
        $sql = "
        CREATE TABLE IF NOT EXISTS g5_shop_order (
            od_id INTEGER PRIMARY KEY AUTOINCREMENT,
            od_no TEXT UNIQUE NOT NULL,
            mb_id TEXT,
            od_name TEXT NOT NULL,
            od_email TEXT NOT NULL,
            od_tel TEXT NOT NULL,
            od_hp TEXT,
            od_zip TEXT,
            od_addr1 TEXT,
            od_addr2 TEXT,
            od_addr3 TEXT,
            od_addr_jibeon TEXT,
            od_memo TEXT,
            od_status TEXT DEFAULT '주문완료',
            od_settle_case TEXT DEFAULT '무통장입금',
            od_receipt_price INTEGER DEFAULT 0,
            od_receipt_point INTEGER DEFAULT 0,
            od_receipt_coupon INTEGER DEFAULT 0,
            od_cart_price INTEGER DEFAULT 0,
            od_send_cost INTEGER DEFAULT 0,
            od_send_coupon INTEGER DEFAULT 0,
            od_send_point INTEGER DEFAULT 0,
            od_receipt_time TEXT,
            od_send_time TEXT,
            od_delivery_company TEXT,
            od_invoice TEXT,
            od_time TEXT DEFAULT (datetime('now')),
            od_update_time TEXT
        );
        ";
        
        $db->exec($sql);
        
        // 주문 상세 테이블 생성
        $sql = "
        CREATE TABLE IF NOT EXISTS g5_shop_order_item (
            oi_id INTEGER PRIMARY KEY AUTOINCREMENT,
            od_id INTEGER NOT NULL,
            od_no TEXT NOT NULL,
            it_id INTEGER NOT NULL,
            it_name TEXT NOT NULL,
            it_price INTEGER NOT NULL,
            ct_qty INTEGER NOT NULL,
            ct_price INTEGER NOT NULL,
            it_img1 TEXT,
            FOREIGN KEY (od_id) REFERENCES g5_shop_order(od_id)
        );
        ";
        
        $db->exec($sql);
        
        $message = "✓ 주문 테이블과 주문 상세 테이블이 생성되었습니다!";
        $success = true;
        
    } catch (Exception $e) {
        $message = "오류 발생: " . $e->getMessage();
        $success = false;
    }
}

// 테이블 존재 여부 확인
$order_table_exists = false;
$order_item_table_exists = false;

try {
    $db = g5_get_db();
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='g5_shop_order'");
    $order_table_exists = $result->fetch() !== false;
    
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='g5_shop_order_item'");
    $order_item_table_exists = $result->fetch() !== false;
} catch (Exception $e) {
    // 무시
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 테이블 생성</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
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
        .status {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .status-item {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .status-item .icon {
            margin-right: 10px;
            font-size: 20px;
        }
        .status-item .exists {
            color: #28a745;
        }
        .status-item .missing {
            color: #dc3545;
        }
        .btn {
            padding: 12px 24px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>주문 테이블 생성</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="status">
            <h3>테이블 상태:</h3>
            <div class="status-item">
                <span class="icon"><?php echo $order_table_exists ? '✓' : '✗'; ?></span>
                <span class="<?php echo $order_table_exists ? 'exists' : 'missing'; ?>">
                    g5_shop_order: <?php echo $order_table_exists ? '존재함' : '없음'; ?>
                </span>
            </div>
            <div class="status-item">
                <span class="icon"><?php echo $order_item_table_exists ? '✓' : '✗'; ?></span>
                <span class="<?php echo $order_item_table_exists ? 'exists' : 'missing'; ?>">
                    g5_shop_order_item: <?php echo $order_item_table_exists ? '존재함' : '없음'; ?>
                </span>
            </div>
        </div>
        
        <?php if (!$order_table_exists || !$order_item_table_exists): ?>
            <form method="POST">
                <p>주문 기능을 사용하려면 주문 테이블이 필요합니다.</p>
                <button type="submit" name="create_tables" class="btn">주문 테이블 생성</button>
            </form>
        <?php else: ?>
            <p style="color: #28a745; font-weight: bold;">✓ 모든 주문 테이블이 존재합니다.</p>
        <?php endif; ?>
        
        <p style="margin-top: 30px;">
            <a href="admin_view_data.php?table=g5_shop_item">← 상품 관리로 돌아가기</a>
        </p>
    </div>
</body>
</html>

