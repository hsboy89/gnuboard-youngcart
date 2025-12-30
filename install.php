<?php
/**
 * Gnuboard + YoungCart 설치 스크립트
 * SQLite 데이터베이스 사용
 */

// 설치 확인
if (file_exists('data/dbconfig.php')) {
    die('이미 설치가 완료되었습니다. 설치를 다시 진행하려면 data/dbconfig.php 파일을 삭제하세요.');
}

// PHP 버전 확인
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('PHP 7.4 이상이 필요합니다. 현재 버전: ' . PHP_VERSION);
}

// SQLite 확장 확인
if (!extension_loaded('pdo_sqlite')) {
    die('PDO SQLite 확장이 필요합니다. php.ini에서 extension=pdo_sqlite를 활성화하세요.');
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

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_path = $_POST['db_path'] ?? 'data/db/gnuboard.db';
    $admin_id = $_POST['admin_id'] ?? 'admin';
    $admin_password = $_POST['admin_password'] ?? '';
    $admin_email = $_POST['admin_email'] ?? 'admin@example.com';
    $site_name = $_POST['site_name'] ?? 'Gnuboard Site';
    
    // 데이터베이스 생성
    try {
        $db = new PDO('sqlite:' . $db_path);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 기본 테이블 생성
        $sql = file_get_contents('install/schema.sql');
        $db->exec($sql);
        
        // 관리자 계정 생성
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO g5_member (mb_id, mb_password, mb_name, mb_email, mb_level, mb_regdate) VALUES (?, ?, ?, ?, 10, datetime('now'))");
        $stmt->execute([$admin_id, $hashed_password, '관리자', $admin_email]);
        
        // 설정 파일 생성
        $config_content = "<?php\n";
        $config_content .= "define('G5_DB_TYPE', 'sqlite');\n";
        $config_content .= "define('G5_DB_PATH', '" . addslashes($db_path) . "');\n";
        $config_content .= "define('G5_SITE_NAME', '" . addslashes($site_name) . "');\n";
        $config_content .= "define('G5_ADMIN_ID', '" . addslashes($admin_id) . "');\n";
        
        file_put_contents('data/dbconfig.php', $config_content);
        
        echo "<script>alert('설치가 완료되었습니다!'); location.href='index.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = '데이터베이스 생성 실패: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gnuboard 설치</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"], input[type="email"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Gnuboard + YoungCart 설치</h1>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>데이터베이스 경로:</label>
            <input type="text" name="db_path" value="data/db/gnuboard.db" required>
        </div>
        <div class="form-group">
            <label>사이트 이름:</label>
            <input type="text" name="site_name" value="Gnuboard Site" required>
        </div>
        <div class="form-group">
            <label>관리자 ID:</label>
            <input type="text" name="admin_id" value="admin" required>
        </div>
        <div class="form-group">
            <label>관리자 비밀번호:</label>
            <input type="password" name="admin_password" required>
        </div>
        <div class="form-group">
            <label>관리자 이메일:</label>
            <input type="email" name="admin_email" value="admin@example.com" required>
        </div>
        <button type="submit">설치 시작</button>
    </form>
</body>
</html>

