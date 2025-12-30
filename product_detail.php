<?php
/**
 * 상품 상세 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: shop.php?lang=' . $lang);
    exit;
}

// 상품 정보 조회
$product = g5_fetch("SELECT * FROM g5_shop_item WHERE it_id = ? AND it_use = 1 AND it_sell_use = 1", [$product_id]);

if (!$product) {
    header('Location: shop.php?lang=' . $lang);
    exit;
}

// 조회수 증가
g5_query("UPDATE g5_shop_item SET it_hit = it_hit + 1 WHERE it_id = ?", [$product_id]);

$text = [
    'ko' => [
        'title' => '상품 상세',
        'price' => '판매가',
        'cust_price' => '정가',
        'stock' => '재고',
        'in_stock' => '재고 있음',
        'out_of_stock' => '재고 없음',
        'maker' => '제조사',
        'origin' => '원산지',
        'description' => '상품 설명',
        'buy_now' => '바로 구매',
        'add_to_cart' => '장바구니 담기',
        'currency' => '원',
        'back_to_shop' => '쇼핑몰로 돌아가기'
    ],
    'en' => [
        'title' => 'Product Detail',
        'price' => 'Price',
        'cust_price' => 'Original Price',
        'stock' => 'Stock',
        'in_stock' => 'In Stock',
        'out_of_stock' => 'Out of Stock',
        'maker' => 'Manufacturer',
        'origin' => 'Origin',
        'description' => 'Description',
        'buy_now' => 'Buy Now',
        'add_to_cart' => 'Add to Cart',
        'currency' => 'KRW',
        'back_to_shop' => 'Back to Shop'
    ]
];

$t = $text[$lang] ?? $text['ko'];
$t = array_merge($text['ko'], $t);

// null 안전성을 위한 기본값 설정
$product['it_img1'] = $product['it_img1'] ?? '';
$product['it_img2'] = $product['it_img2'] ?? '';
$product['it_img3'] = $product['it_img3'] ?? '';
$product['it_name'] = $product['it_name'] ?? '';
$product['it_price'] = $product['it_price'] ?? 0;
$product['it_cust_price'] = $product['it_cust_price'] ?? 0;
$product['it_stock_qty'] = $product['it_stock_qty'] ?? 0;
$product['it_maker'] = $product['it_maker'] ?? '';
$product['it_origin'] = $product['it_origin'] ?? '';
$product['it_content'] = $product['it_content'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['it_name']); ?> - <?php echo htmlspecialchars($t['title'] ?? '상품 상세'); ?></title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .product-detail-container {
            padding: 60px 0;
        }
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        .product-images {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-main-image {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .product-thumbnails {
            display: flex;
            gap: 10px;
        }
        .product-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .product-thumbnail.active {
            border-color: #007bff;
        }
        .product-info {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .product-price-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .product-price {
            font-size: 32px;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-cust-price {
            font-size: 18px;
            color: #999;
            text-decoration: line-through;
        }
        .product-meta {
            margin-bottom: 30px;
        }
        .product-meta-item {
            margin-bottom: 15px;
        }
        .product-meta-label {
            font-weight: bold;
            margin-right: 10px;
        }
        .product-stock {
            font-size: 16px;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .product-stock.in-stock {
            background: #d4edda;
            color: #155724;
        }
        .product-stock.out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
            color: #fff;
        }
        .btn-success:hover {
            background: #218838;
        }
        .product-description {
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid #eee;
        }
        .product-description h3 {
            margin-bottom: 20px;
        }
        .product-description-content {
            line-height: 1.8;
            color: #666;
        }
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="product-detail-container">
        <div class="container">
            <a href="shop.php?lang=<?php echo $lang; ?>" style="color: #666; text-decoration: none; margin-bottom: 20px; display: inline-block;">
                ← <?php echo htmlspecialchars($t['back_to_shop'] ?? '쇼핑몰로 돌아가기'); ?>
            </a>
            
            <div class="product-detail">
                <div class="product-images">
                    <?php 
                    // 이미지가 없으면 placeholder 사용
                    $mainImage = !empty($product['it_img1']) ? $product['it_img1'] : 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=500&h=500&fit=crop';
                    ?>
                    <img id="mainImage" src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($product['it_name']); ?>" class="product-main-image">
                    <?php if (!empty($product['it_img2']) || !empty($product['it_img3'])): ?>
                        <div class="product-thumbnails">
                            <?php if (!empty($product['it_img1'])): ?>
                                <img src="<?php echo htmlspecialchars($product['it_img1']); ?>" alt="Image 1" class="product-thumbnail active" onclick="changeImage(this.src)">
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="Image 1" class="product-thumbnail active" onclick="changeImage(this.src)">
                            <?php endif; ?>
                            <?php if (!empty($product['it_img2'])): ?>
                                <img src="<?php echo htmlspecialchars($product['it_img2']); ?>" alt="Image 2" class="product-thumbnail" onclick="changeImage(this.src)">
                            <?php endif; ?>
                            <?php if (!empty($product['it_img3'])): ?>
                                <img src="<?php echo htmlspecialchars($product['it_img3']); ?>" alt="Image 3" class="product-thumbnail" onclick="changeImage(this.src)">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <h1 class="product-name"><?php echo htmlspecialchars($product['it_name']); ?></h1>
                    
                    <div class="product-price-section">
                        <div class="product-price">
                            <?php echo number_format($product['it_price']); ?><?php echo htmlspecialchars($t['currency'] ?? '원'); ?>
                        </div>
                        <?php if ($product['it_cust_price'] > $product['it_price']): ?>
                            <div class="product-cust-price">
                                <?php echo number_format($product['it_cust_price']); ?><?php echo htmlspecialchars($t['currency'] ?? '원'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-meta">
                        <?php if (!empty($product['it_maker'])): ?>
                            <div class="product-meta-item">
                                <span class="product-meta-label"><?php echo htmlspecialchars($t['maker'] ?? '제조사'); ?>:</span>
                                <span><?php echo htmlspecialchars($product['it_maker']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['it_origin'])): ?>
                            <div class="product-meta-item">
                                <span class="product-meta-label"><?php echo htmlspecialchars($t['origin'] ?? '원산지'); ?>:</span>
                                <span><?php echo htmlspecialchars($product['it_origin']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-stock <?php echo $product['it_stock_qty'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                        <?php echo htmlspecialchars($t['stock'] ?? '재고'); ?>: 
                        <?php if ($product['it_stock_qty'] > 0): ?>
                            <?php echo htmlspecialchars($t['in_stock'] ?? '재고 있음'); ?> (<?php echo number_format($product['it_stock_qty']); ?>)
                        <?php else: ?>
                            <?php echo htmlspecialchars($t['out_of_stock'] ?? '재고 없음'); ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-actions">
                        <form method="GET" action="order.php" style="display: inline;">
                            <input type="hidden" name="it_id" value="<?php echo $product['it_id']; ?>">
                            <input type="hidden" name="qty" value="1">
                            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                            <button type="submit" class="btn btn-primary">
                                <?php echo htmlspecialchars($t['buy_now'] ?? '바로 구매'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($product['it_content'])): ?>
                <div class="product-description">
                    <h3><?php echo htmlspecialchars($t['description'] ?? '상품 설명'); ?></h3>
                    <div class="product-description-content">
                        <?php echo nl2br(htmlspecialchars($product['it_content'])); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'theme/pumae/footer.php'; ?>
    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.product-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>
