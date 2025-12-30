<?php
/**
 * 웹에서 재고를 10개로 업데이트하는 페이지
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    try {
        $db = g5_get_db();
        
        // 모든 상품의 재고를 10개로 업데이트
        $stmt = $db->prepare("UPDATE g5_shop_item SET it_stock_qty = 10 WHERE it_use = 1 AND it_sell_use = 1");
        $stmt->execute();
        
        $count = $stmt->rowCount();
        $message = "✓ {$count}개의 상품 재고가 10개로 업데이트되었습니다.";
        $success = true;
        
    } catch (Exception $e) {
        $message = "오류 발생: " . $e->getMessage();
        $success = false;
    }
}

// 현재 상품 목록 조회
$products = g5_fetch_all("SELECT it_id, it_name, it_stock_qty FROM g5_shop_item WHERE it_use = 1 AND it_sell_use = 1 ORDER BY it_id");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>재고 업데이트</title>
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
        .product-list {
            margin: 20px 0;
        }
        .product-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>상품 재고 업데이트</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <p>모든 판매 중인 상품의 재고를 <strong>10개</strong>로 업데이트합니다.</p>
            
            <div class="product-list">
                <h3>현재 상품 목록:</h3>
                <?php if (empty($products)): ?>
                    <p>등록된 상품이 없습니다.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <span><?php echo htmlspecialchars($product['it_name']); ?></span>
                            <span>재고: <?php echo number_format($product['it_stock_qty']); ?>개</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button type="submit" name="update_stock" class="btn">재고를 10개로 업데이트</button>
        </form>
        
        <p style="margin-top: 30px;">
            <a href="admin_view_data.php?table=g5_shop_item">← 상품 관리로 돌아가기</a>
        </p>
    </div>
</body>
</html>

