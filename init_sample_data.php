<?php
/**
 * 샘플 데이터 생성 스크립트
 */

require_once 'data/dbconfig.php';

try {
    $db = g5_get_db();
    
    // 샘플 게시글 추가
    $stmt = $db->prepare("INSERT INTO g5_write_free (wr_subject, wr_content, wr_name, mb_id, wr_datetime) VALUES (?, ?, ?, ?, datetime('now'))");
    
    $posts = [
        ['2025-02-20 품애, 디휴먼브릿지, 스윔닥터(SDmall)이 손을잡고 스포츠테크 혁신 수영 특화 솔루션 개발을 추진합니다.', '품애는 디휴먼브릿지, 스윔닥터와 함께 수영 특화 솔루션 개발을 위한 협업을 시작했습니다.', '관리자', 'admin'],
        ['2024-10-08 2024 제주 펫 페어에서 품애가 Pet Tech 세미나를 하고 왔습니다.', '2024년 제주 펫 페어에서 품애의 Pet Tech 세미나가 성공적으로 개최되었습니다.', '관리자', 'admin'],
        ['LUHearty 앰베서더 Review', 'LUHearty 제품에 대한 사용자 리뷰입니다.', '사용자1', 'user1'],
        ['DIVA for swim 신제품 출시 예정', '수영 분석용 웨어러블 디바이스 DIVA for swim이 곧 출시됩니다.', '관리자', 'admin']
    ];
    
    foreach ($posts as $post) {
        $stmt->execute($post);
    }
    
    // 샘플 상품 추가
    $stmt = $db->prepare("INSERT INTO g5_shop_item (it_name, it_price, it_content, it_img1, it_use, it_sell_use) VALUES (?, ?, ?, ?, 1, 1)");
    
    $products = [
        ['LUHearty for dogs', 199000, '반려견 헬스케어 웨어러블 디바이스', 'https://via.placeholder.com/400x400?text=LUHearty', 1, 1],
        ['DIVA for swim', 299000, '수영 분석용 스포츠 웨어러블 디바이스', 'https://via.placeholder.com/400x400?text=DIVA', 1, 1],
        ['LUHearty Platform', 149000, '헬스케어 플랫폼 솔루션', 'https://via.placeholder.com/400x400?text=Platform', 1, 1]
    ];
    
    foreach ($products as $product) {
        $stmt->execute($product);
    }
    
    echo "샘플 데이터가 생성되었습니다!\n";
    
} catch (Exception $e) {
    echo "오류: " . $e->getMessage() . "\n";
}

