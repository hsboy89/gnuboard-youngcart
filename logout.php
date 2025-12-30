<?php
/**
 * 로그아웃 페이지
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');

// 세션 파괴
session_destroy();

// 로그인 페이지로 리다이렉트
header('Location: login.php?lang=' . $lang);
exit;

