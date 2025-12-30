<?php
/**
 * test 계정 생성 스크립트
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$test_id = 'test';
$test_password = 'test123';
$test_name = '테스트 사용자';
$test_email = 'test@example.com';

try {
    $db = g5_get_db();
    
    // 기존 계정 확인
    $existing = g5_fetch("SELECT mb_id FROM g5_member WHERE mb_id = ?", [$test_id]);
    
    if ($existing) {
        echo "test 계정이 이미 존재합니다.\n";
        echo "비밀번호를 업데이트합니다...\n";
        
        // 비밀번호 업데이트
        $hashed_password = password_hash($test_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE g5_member SET mb_password = ?, mb_name = ?, mb_email = ? WHERE mb_id = ?");
        $stmt->execute([$hashed_password, $test_name, $test_email, $test_id]);
        
        echo "✓ test 계정 비밀번호 업데이트 완료\n";
    } else {
        // 새 계정 생성
        $hashed_password = password_hash($test_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO g5_member (mb_id, mb_password, mb_name, mb_email, mb_level, mb_regdate) VALUES (?, ?, ?, ?, 1, datetime('now'))");
        $stmt->execute([$test_id, $hashed_password, $test_name, $test_email]);
        
        echo "✓ test 계정 생성 완료\n";
    }
    
    echo "\n계정 정보:\n";
    echo "  ID: $test_id\n";
    echo "  비밀번호: $test_password\n";
    echo "  이름: $test_name\n";
    echo "  이메일: $test_email\n";
    echo "  권한: 일반 사용자 (mb_level: 1)\n";
    
} catch (Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
}

