<?php
/**
 * 빠른 설치 스크립트 (자동 입력)
 */

echo "=== Gnuboard 빠른 설치 ===\n\n";

// PHP 버전 확인
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die("❌ PHP 7.4 이상이 필요합니다.\n");
}

// SQLite 확장 확인
if (!extension_loaded('pdo_sqlite')) {
    die("❌ PDO SQLite 확장이 필요합니다.\n");
}

// 디렉토리 생성
$dirs = ['data', 'data/db', 'data/file', 'data/editor', 'data/config', 'theme/pumae', 'theme/pumae/css', 'theme/pumae/js', 'theme/pumae/images', 'plugin/youngcart'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 기본값으로 설치
$db_path = 'data/db/gnuboard.db';
$site_name = 'Gnuboard Site';
$admin_id = 'admin';
$admin_password = 'admin123';
$admin_email = 'admin@example.com';

echo "데이터베이스 생성 중...\n";
try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 스키마 실행
    $sql = file_get_contents('install/schema.sql');
    $db->exec($sql);
    echo "✓ 테이블 생성 완료\n";
    
    // 관리자 계정
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO g5_member (mb_id, mb_password, mb_name, mb_email, mb_level, mb_regdate) VALUES (?, ?, ?, ?, 10, datetime('now'))");
    $stmt->execute([$admin_id, $hashed_password, '관리자', $admin_email]);
    echo "✓ 관리자 계정 생성 완료\n";
    
    // 기본 게시판
    $stmt = $db->prepare("INSERT INTO g5_board (bo_table, bo_subject) VALUES (?, ?)");
    $boards = [['news', 'News'], ['review', 'Review'], ['info', 'Info'], ['free', '자유게시판']];
    foreach ($boards as $board) {
        $stmt->execute($board);
    }
    echo "✓ 기본 게시판 생성 완료\n";
    
    // 샘플 게시글
    $stmt = $db->prepare("INSERT INTO g5_write_free (wr_subject, wr_content, wr_name, mb_id, wr_datetime) VALUES (?, ?, ?, ?, datetime('now'))");
    $posts = [
        ['2025-02-20 품애, 디휴먼브릿지, 스윔닥터(SDmall)이 손을잡고 스포츠테크 혁신 수영 특화 솔루션 개발을 추진합니다.', '품애는 디휴먼브릿지, 스윔닥터와 함께 수영 특화 솔루션 개발을 위한 협업을 시작했습니다.', '관리자', 'admin'],
        ['2024-10-08 2024 제주 펫 페어에서 품애가 Pet Tech 세미나를 하고 왔습니다.', '2024년 제주 펫 페어에서 품애의 Pet Tech 세미나가 성공적으로 개최되었습니다.', '관리자', 'admin']
    ];
    foreach ($posts as $post) {
        $stmt->execute($post);
    }
    echo "✓ 샘플 게시글 생성 완료\n";
    
    // 샘플 상품 (간단한 구조로)
    try {
        $stmt = $db->prepare("INSERT INTO g5_shop_item (it_name, it_price, it_content, it_img1, it_use, it_sell_use, it_time) VALUES (?, ?, ?, ?, 1, 1, datetime('now'))");
        $products = [
            ['LUHearty for dogs', 199000, '반려견 헬스케어 웨어러블 디바이스', 'https://via.placeholder.com/400x400?text=LUHearty'],
            ['DIVA for swim', 299000, '수영 분석용 스포츠 웨어러블 디바이스', 'https://via.placeholder.com/400x400?text=DIVA']
        ];
        foreach ($products as $product) {
            $stmt->execute($product);
        }
        echo "✓ 샘플 상품 생성 완료\n";
    } catch (Exception $e) {
        echo "⚠️  상품 생성 건너뜀: " . $e->getMessage() . "\n";
    }
    
    // 설정 파일
    $config = "<?php\n";
    $config .= "/**\n";
    $config .= " * Gnuboard 데이터베이스 설정 파일\n";
    $config .= " * SQLite 사용\n";
    $config .= " */\n\n";
    $config .= "if (!defined('G5_DB_TYPE')) {\n";
    $config .= "    define('G5_DB_TYPE', 'sqlite');\n";
    $config .= "    define('G5_DB_PATH', __DIR__ . '/db/gnuboard.db');\n";
    $config .= "    define('G5_SITE_NAME', '$site_name');\n";
    $config .= "}\n\n";
    $config .= "// SQLite 연결 함수\n";
    $config .= "function g5_get_db() {\n";
    $config .= "    static \$db = null;\n";
    $config .= "    if (\$db === null) {\n";
    $config .= "        try {\n";
    $config .= "            \$db = new PDO('sqlite:' . G5_DB_PATH);\n";
    $config .= "            \$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
    $config .= "            \$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);\n";
    $config .= "        } catch (PDOException \$e) {\n";
    $config .= "            die('데이터베이스 연결 실패: ' . \$e->getMessage());\n";
    $config .= "        }\n";
    $config .= "    }\n";
    $config .= "    return \$db;\n";
    $config .= "}\n\n";
    $config .= "// 쿼리 실행 헬퍼 함수\n";
    $config .= "function g5_query(\$sql, \$params = []) {\n";
    $config .= "    \$db = g5_get_db();\n";
    $config .= "    \$stmt = \$db->prepare(\$sql);\n";
    $config .= "    \$stmt->execute(\$params);\n";
    $config .= "    return \$stmt;\n";
    $config .= "}\n\n";
    $config .= "// 단일 행 조회\n";
    $config .= "function g5_fetch(\$sql, \$params = []) {\n";
    $config .= "    \$stmt = g5_query(\$sql, \$params);\n";
    $config .= "    return \$stmt->fetch();\n";
    $config .= "}\n\n";
    $config .= "// 여러 행 조회\n";
    $config .= "function g5_fetch_all(\$sql, \$params = []) {\n";
    $config .= "    \$stmt = g5_query(\$sql, \$params);\n";
    $config .= "    return \$stmt->fetchAll();\n";
    $config .= "}\n";
    file_put_contents('data/dbconfig.php', $config);
    echo "✓ 설정 파일 생성 완료\n";
    
    echo "\n✅ 설치 완료!\n";
    echo "관리자 ID: $admin_id\n";
    echo "관리자 비밀번호: $admin_password\n";
    
} catch (Exception $e) {
    die("❌ 오류: " . $e->getMessage() . "\n");
}

