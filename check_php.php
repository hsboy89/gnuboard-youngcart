<?php
echo "PHP 버전: " . PHP_VERSION . "\n";
echo "SQLite 지원: " . (extension_loaded('pdo_sqlite') ? 'Yes' : 'No') . "\n";
echo "현재 디렉토리: " . __DIR__ . "\n";
echo "설치 상태: " . (file_exists('data/dbconfig.php') ? '이미 설치됨' : '미설치') . "\n";
?>

