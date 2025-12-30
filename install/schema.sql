-- Gnuboard 기본 테이블 구조 (SQLite)

-- 회원 테이블
CREATE TABLE IF NOT EXISTS g5_member (
    mb_no INTEGER PRIMARY KEY AUTOINCREMENT,
    mb_id TEXT UNIQUE NOT NULL,
    mb_password TEXT NOT NULL,
    mb_name TEXT NOT NULL,
    mb_email TEXT,
    mb_level INTEGER DEFAULT 1,
    mb_regdate TEXT DEFAULT (datetime('now')),
    mb_lastlogin TEXT
);

-- 게시판 테이블
CREATE TABLE IF NOT EXISTS g5_board (
    bo_table TEXT PRIMARY KEY,
    bo_subject TEXT NOT NULL,
    bo_skin TEXT DEFAULT 'basic',
    bo_use_category INTEGER DEFAULT 0,
    bo_list_level INTEGER DEFAULT 1,
    bo_read_level INTEGER DEFAULT 1,
    bo_write_level INTEGER DEFAULT 1,
    bo_comment_level INTEGER DEFAULT 1,
    bo_page_rows INTEGER DEFAULT 15,
    bo_new INTEGER DEFAULT 24,
    bo_hot INTEGER DEFAULT 100
);

-- 게시글 테이블
CREATE TABLE IF NOT EXISTS g5_write_free (
    wr_id INTEGER PRIMARY KEY AUTOINCREMENT,
    wr_num INTEGER DEFAULT 0,
    wr_reply TEXT,
    wr_parent INTEGER DEFAULT 0,
    wr_subject TEXT,
    wr_content TEXT,
    mb_id TEXT,
    wr_password TEXT,
    wr_name TEXT,
    wr_email TEXT,
    wr_datetime TEXT DEFAULT (datetime('now')),
    wr_hit INTEGER DEFAULT 0,
    wr_good INTEGER DEFAULT 0,
    wr_nogood INTEGER DEFAULT 0,
    mb_no INTEGER DEFAULT 0,
    wr_notice INTEGER DEFAULT 0,
    wr_secret INTEGER DEFAULT 0
);

-- 파일 테이블
CREATE TABLE IF NOT EXISTS g5_board_file (
    bo_table TEXT NOT NULL,
    wr_id INTEGER NOT NULL,
    bf_no INTEGER PRIMARY KEY AUTOINCREMENT,
    bf_source TEXT NOT NULL,
    bf_file TEXT NOT NULL,
    bf_download INTEGER DEFAULT 0,
    bf_filesize INTEGER DEFAULT 0,
    bf_datetime TEXT DEFAULT (datetime('now'))
);

-- YoungCart 상품 테이블
CREATE TABLE IF NOT EXISTS g5_shop_item (
    it_id INTEGER PRIMARY KEY AUTOINCREMENT,
    ca_id TEXT,
    it_name TEXT NOT NULL,
    it_maker TEXT,
    it_origin TEXT,
    it_img1 TEXT,
    it_img2 TEXT,
    it_img3 TEXT,
    it_sum_qty INTEGER DEFAULT 0,
    it_use INTEGER DEFAULT 1,
    it_sell_use INTEGER DEFAULT 1,
    it_stock_qty INTEGER DEFAULT 0,
    it_hit INTEGER DEFAULT 0,
    it_time TEXT DEFAULT (datetime('now')),
    it_cust_price INTEGER DEFAULT 0,
    it_price INTEGER DEFAULT 0,
    it_content TEXT,
    it_mobile_content TEXT
);

-- 상품 카테고리 테이블
CREATE TABLE IF NOT EXISTS g5_shop_category (
    ca_id TEXT PRIMARY KEY,
    ca_name TEXT NOT NULL,
    ca_order INTEGER DEFAULT 0,
    ca_use INTEGER DEFAULT 1
);

-- 주문 테이블
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

-- 주문 상세 테이블
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

