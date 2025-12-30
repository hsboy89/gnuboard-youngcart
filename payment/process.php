<?php
/**
 * PG사 결제 처리 페이지
 */

require_once '../data/dbconfig.php';
require_once '../auto_create_tables.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 주문 테이블이 없으면 자동 생성
ensure_order_tables();

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$od_no = $_GET['od_no'] ?? null;
$pg = $_GET['pg'] ?? 'kcp';

if (!$od_no) {
    header('Location: ../shop.php?lang=' . $lang);
    exit;
}

// 주문 정보 조회
$order = g5_fetch("SELECT * FROM g5_shop_order WHERE od_no = ?", [$od_no]);
$order_items = g5_fetch_all("SELECT * FROM g5_shop_order_item WHERE od_no = ?", [$od_no]);

if (!$order || empty($order_items)) {
    header('Location: ../shop.php?lang=' . $lang);
    exit;
}

$total_amount = $order['od_cart_price'] + $order['od_send_cost'];

// PG사별 처리
if ($pg === 'kcp') {
    include 'kcp.php';
} elseif ($pg === 'inicis') {
    include 'inicis.php';
} elseif ($pg === 'toss') {
    include 'toss.php';
} else {
    die('지원하지 않는 PG사입니다.');
}

