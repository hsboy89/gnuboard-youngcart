<?php
/**
 * 상품 이미지를 로컬 이미지로 업데이트하는 스크립트
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_images'])) {
    try {
        $db = g5_get_db();
        
        // LUHearty for dogs에 1.png 설정
        $stmt = $db->prepare("UPDATE g5_shop_item SET it_img1 = ? WHERE it_name = 'LUHearty for dogs'");
        $stmt->execute(['theme/pumae/images/1.png']);
        
        // DIVA for swim에 2.png 설정
        $stmt = $db->prepare("UPDATE g5_shop_item SET it_img1 = ? WHERE it_name = 'DIVA for swim'");
        $stmt->execute(['theme/pumae/images/2.png']);
        
        $count = $stmt->rowCount();
        $message = "✓ 상품 이미지가 업데이트되었습니다!";
        $success = true;
        
    } catch (Exception $e) {
        $message = "오류 발생: " . $e->getMessage();
        $success = false;
    }
}

// 현재 상품 이미지 확인
$products = g5_fetch_all("SELECT it_id, it_name, it_img1 FROM g5_shop_item WHERE it_use = 1 AND it_sell_use = 1 ORDER BY it_id");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상품 이미지 업데이트</title>
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
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .product-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .product-image-url {
            font-size: 12px;
            color: #666;
            word-break: break-all;
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
        <h1>상품 이미지 업데이트</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <p>다음 상품의 이미지를 로컬 이미지로 업데이트합니다:</p>
        <ul>
            <li><strong>LUHearty for dogs</strong> → <code>theme/pumae/images/1.png</code></li>
            <li><strong>DIVA for swim</strong> → <code>theme/pumae/images/2.png</code></li>
        </ul>
        
        <div class="product-list">
            <h3>현재 상품 이미지:</h3>
            <?php if (empty($products)): ?>
                <p>등록된 상품이 없습니다.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <?php if ($product['it_img1']): ?>
                            <img src="<?php echo htmlspecialchars($product['it_img1']); ?>" alt="<?php echo htmlspecialchars($product['it_name']); ?>" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3E이미지 없음%3C/text%3E%3C/svg%3E';">
                        <?php else: ?>
                            <div style="width: 100px; height: 100px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999;">이미지 없음</div>
                        <?php endif; ?>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($product['it_name']); ?></div>
                            <div class="product-image-url">이미지: <?php echo htmlspecialchars($product['it_img1'] ?: '없음'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <form method="POST">
            <button type="submit" name="update_images" class="btn">이미지 업데이트</button>
        </form>
        
        <p style="margin-top: 30px;">
            <a href="admin_view_data.php?table=g5_shop_item">← 상품 관리로 돌아가기</a>
        </p>
    </div>
</body>
</html>

