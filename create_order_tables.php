<?php
/**
 * 주문 테이블 생성 스크립트
 */

require_once 'data/dbconfig.php';

try {
    $db = g5_get_db();
    
    echo "주문 테이블 생성 중...\n";
    
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
    echo "✓ 주문 테이블 생성 완료\n";
    
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
    echo "✓ 주문 상세 테이블 생성 완료\n";
    
    echo "\n✅ 모든 테이블이 생성되었습니다!\n";
    
} catch (Exception $e) {
    echo "❌ 오류 발생: " . $e->getMessage() . "\n";
}

