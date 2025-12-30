<?php
/**
 * Gnuboard 데이터베이스 설정 파일
 * SQLite 사용
 */

if (!defined('G5_DB_TYPE')) {
    define('G5_DB_TYPE', 'sqlite');
    define('G5_DB_PATH', __DIR__ . '/db/gnuboard.db');
    define('G5_SITE_NAME', 'Gnuboard Site');
}

// SQLite 연결 함수
function g5_get_db() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO('sqlite:' . G5_DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('데이터베이스 연결 실패: ' . $e->getMessage());
        }
    }
    return $db;
}

// 쿼리 실행 헬퍼 함수
function g5_query($sql, $params = []) {
    $db = g5_get_db();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// 단일 행 조회
function g5_fetch($sql, $params = []) {
    $stmt = g5_query($sql, $params);
    return $stmt->fetch();
}

// 여러 행 조회
function g5_fetch_all($sql, $params = []) {
    $stmt = g5_query($sql, $params);
    return $stmt->fetchAll();
}
