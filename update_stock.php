<?php
/**
 * 모든 상품 재고를 10개로 업데이트하는 스크립트
 */

require_once 'data/dbconfig.php';

try {
    $db = g5_get_db();
    
    // 모든 상품의 재고를 10개로 업데이트
    $stmt = $db->prepare("UPDATE g5_shop_item SET it_stock_qty = 10 WHERE it_use = 1 AND it_sell_use = 1");
    $stmt->execute();
    
    $count = $stmt->rowCount();
    
    echo "✓ {$count}개의 상품 재고가 10개로 업데이트되었습니다.\n";
    
    // 업데이트된 상품 목록 출력
    $products = g5_fetch_all("SELECT it_id, it_name, it_stock_qty FROM g5_shop_item WHERE it_use = 1 AND it_sell_use = 1");
    
    echo "\n업데이트된 상품 목록:\n";
    foreach ($products as $product) {
        echo "  - {$product['it_name']}: 재고 {$product['it_stock_qty']}개\n";
    }
    
} catch (Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
}

