<?php
/**
 * Gnuboard + YoungCart CLI 설치 스크립트
 * XAMPP 없이 SQLite만 사용
 */

echo "=== Gnuboard + YoungCart 설치 ===\n\n";

// PHP 버전 확인
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die("❌ PHP 7.4 이상이 필요합니다. 현재 버전: " . PHP_VERSION . "\n");
}
echo "✓ PHP 버전: " . PHP_VERSION . "\n";

// SQLite 확장 확인
if (!extension_loaded('pdo_sqlite')) {
    die("❌ PDO SQLite 확장이 필요합니다. php.ini에서 extension=pdo_sqlite를 활성화하세요.\n");
}
echo "✓ SQLite 확장 확인 완료\n";

// 설치 확인
if (file_exists('data/dbconfig.php')) {
    echo "⚠️  이미 설치가 완료되었습니다.\n";
    echo "설치를 다시 진행하려면 data/dbconfig.php 파일을 삭제하세요.\n";
    exit;
}

// 디렉토리 생성
$dirs = [
    'data',
    'data/db',
    'data/file',
    'data/editor',
    'data/config',
    'theme/pumae',
    'theme/pumae/css',
    'theme/pumae/js',
    'theme/pumae/images',
    'plugin/youngcart'
];

echo "\n디렉토리 생성 중...\n";
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✓ $dir 생성 완료\n";
    } else {
        echo "  $dir 이미 존재\n";
    }
}

// 기본값 설정
$db_path = 'data/db/gnuboard.db';
$site_name = 'Gnuboard Site';
$admin_id = 'admin';
$admin_password = '';
$admin_email = 'admin@example.com';

// CLI에서 입력 받기
echo "\n=== 설치 정보 입력 ===\n";

// 사이트 이름
echo "사이트 이름 [기본값: $site_name]: ";
$input = trim(fgets(STDIN));
if (!empty($input)) {
    $site_name = $input;
}

// 관리자 ID
echo "관리자 ID [기본값: $admin_id]: ";
$input = trim(fgets(STDIN));
if (!empty($input)) {
    $admin_id = $input;
}

// 관리자 비밀번호
do {
    echo "관리자 비밀번호 (필수): ";
    $admin_password = trim(fgets(STDIN));
    if (empty($admin_password)) {
        echo "❌ 비밀번호는 필수입니다.\n";
    }
} while (empty($admin_password));

// 관리자 이메일
echo "관리자 이메일 [기본값: $admin_email]: ";
$input = trim(fgets(STDIN));
if (!empty($input)) {
    $admin_email = $input;
}

// 데이터베이스 생성
echo "\n데이터베이스 생성 중...\n";
try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ 데이터베이스 연결 성공\n";
    
    // 기본 테이블 생성
    if (!file_exists('install/schema.sql')) {
        die("❌ install/schema.sql 파일이 없습니다.\n");
    }
    
    $sql = file_get_contents('install/schema.sql');
    $db->exec($sql);
    echo "✓ 테이블 생성 완료\n";
    
    // 관리자 계정 생성
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO g5_member (mb_id, mb_password, mb_name, mb_email, mb_level, mb_regdate) VALUES (?, ?, ?, ?, 10, datetime('now'))");
    $stmt->execute([$admin_id, $hashed_password, '관리자', $admin_email]);
    echo "✓ 관리자 계정 생성 완료\n";
    
    // 기본 게시판 생성
    $stmt = $db->prepare("INSERT INTO g5_board (bo_table, bo_subject) VALUES (?, ?)");
    $boards = [
        ['news', 'News'],
        ['review', 'Review'],
        ['info', 'Info'],
        ['free', '자유게시판']
    ];
    foreach ($boards as $board) {
        $stmt->execute($board);
    }
    echo "✓ 기본 게시판 생성 완료\n";
    
    // 설정 파일 생성
    $config_content = "<?php\n";
    $config_content .= "/**\n";
    $config_content .= " * Gnuboard 데이터베이스 설정 파일\n";
    $config_content .= " * SQLite 사용\n";
    $config_content .= " */\n\n";
    $config_content .= "if (!defined('G5_DB_TYPE')) {\n";
    $config_content .= "    define('G5_DB_TYPE', 'sqlite');\n";
    $config_content .= "    define('G5_DB_PATH', __DIR__ . '/db/gnuboard.db');\n";
    $config_content .= "    define('G5_SITE_NAME', '" . addslashes($site_name) . "');\n";
    $config_content .= "}\n\n";
    $config_content .= "// SQLite 연결 함수\n";
    $config_content .= "function g5_get_db() {\n";
    $config_content .= "    static \$db = null;\n";
    $config_content .= "    if (\$db === null) {\n";
    $config_content .= "        try {\n";
    $config_content .= "            \$db = new PDO('sqlite:' . G5_DB_PATH);\n";
    $config_content .= "            \$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
    $config_content .= "            \$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);\n";
    $config_content .= "        } catch (PDOException \$e) {\n";
    $config_content .= "            die('데이터베이스 연결 실패: ' . \$e->getMessage());\n";
    $config_content .= "        }\n";
    $config_content .= "    }\n";
    $config_content .= "    return \$db;\n";
    $config_content .= "}\n\n";
    $config_content .= "// 쿼리 실행 헬퍼 함수\n";
    $config_content .= "function g5_query(\$sql, \$params = []) {\n";
    $config_content .= "    \$db = g5_get_db();\n";
    $config_content .= "    \$stmt = \$db->prepare(\$sql);\n";
    $config_content .= "    \$stmt->execute(\$params);\n";
    $config_content .= "    return \$stmt;\n";
    $config_content .= "}\n\n";
    $config_content .= "// 단일 행 조회\n";
    $config_content .= "function g5_fetch(\$sql, \$params = []) {\n";
    $config_content .= "    \$stmt = g5_query(\$sql, \$params);\n";
    $config_content .= "    return \$stmt->fetch();\n";
    $config_content .= "}\n\n";
    $config_content .= "// 여러 행 조회\n";
    $config_content .= "function g5_fetch_all(\$sql, \$params = []) {\n";
    $config_content .= "    \$stmt = g5_query(\$sql, \$params);\n";
    $config_content .= "    return \$stmt->fetchAll();\n";
    $config_content .= "}\n";
    
    file_put_contents('data/dbconfig.php', $config_content);
    echo "✓ 설정 파일 생성 완료\n";
    
    echo "\n";
    echo "========================================\n";
    echo "✅ 설치가 완료되었습니다!\n";
    echo "========================================\n\n";
    echo "다음 단계:\n";
    echo "1. PHP 내장 서버 실행:\n";
    echo "   php -S localhost:8000\n\n";
    echo "2. 브라우저에서 접속:\n";
    echo "   http://localhost:8000/index.php\n\n";
    echo "관리자 정보:\n";
    echo "  ID: $admin_id\n";
    echo "  비밀번호: (입력하신 비밀번호)\n";
    echo "  이메일: $admin_email\n\n";
    
} catch (Exception $e) {
    die("❌ 오류 발생: " . $e->getMessage() . "\n");
}

