<?php
/**
 * YoungCart 쇼핑몰 메인 페이지
 */

require_once 'data/dbconfig.php';

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$shop_text = [
    'ko' => [
        'title' => 'SHOP',
        'no_products' => '등록된 상품이 없습니다.',
        'no_image' => '이미지 없음',
        'currency' => '원'
    ],
    'en' => [
        'title' => 'SHOP',
        'no_products' => 'No products available.',
        'no_image' => 'No Image',
        'currency' => 'KRW'
    ]
];

$st = $shop_text[$lang] ?? $shop_text['ko'];

// 상품 목록 조회
$items = g5_fetch_all("SELECT * FROM g5_shop_item WHERE it_use = 1 AND it_sell_use = 1 ORDER BY it_time DESC LIMIT 12");
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($st['title']); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .shop-container {
            padding: 60px 0;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        .product-item {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .product-item:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            height: 250px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-info {
            padding: 20px;
        }
        .product-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 20px;
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="shop-container">
        <div class="container">
            <h1><?php echo htmlspecialchars($st['title']); ?></h1>
            <div class="products-grid">
                <?php if (empty($items)): ?>
                    <p><?php echo htmlspecialchars($st['no_products']); ?></p>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <a href="product_detail.php?id=<?php echo $item['it_id']; ?>&lang=<?php echo $lang; ?>" style="text-decoration: none; color: inherit;">
                            <div class="product-item">
                                <div class="product-image">
                                    <?php if ($item['it_img1']): ?>
                                        <img src="<?php echo htmlspecialchars($item['it_img1']); ?>" alt="<?php echo htmlspecialchars($item['it_name']); ?>" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <span><?php echo htmlspecialchars($st['no_image']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-name"><?php echo htmlspecialchars($item['it_name']); ?></div>
                                    <div class="product-price"><?php echo number_format($item['it_price']); ?><?php echo htmlspecialchars($st['currency']); ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

