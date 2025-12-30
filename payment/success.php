<?php
/**
 * 결제 성공 페이지
 */

require_once '../data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$od_no = $_GET['od_no'] ?? $_POST['ordr_idxx'] ?? $_POST['oid'] ?? null;

if ($od_no) {
    // 주문 상태 업데이트
    $db = g5_get_db();
    $stmt = $db->prepare("UPDATE g5_shop_order SET od_status = '결제완료', od_receipt_price = od_cart_price + od_send_cost, od_receipt_time = datetime('now') WHERE od_no = ?");
    $stmt->execute([$od_no]);
}

header('Location: ../order_complete.php?od_no=' . $od_no . '&lang=' . $lang);
exit;

