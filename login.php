<?php
/**
 * 로그인 페이지
 */

require_once 'data/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

$login_text = [
    'ko' => [
        'title' => '로그인',
        'id' => '아이디',
        'password' => '비밀번호',
        'login_btn' => '로그인',
        'error_invalid' => '아이디 또는 비밀번호가 올바르지 않습니다.',
        'error_empty' => '아이디와 비밀번호를 입력해주세요.'
    ],
    'en' => [
        'title' => 'Login',
        'id' => 'ID',
        'password' => 'Password',
        'login_btn' => 'Login',
        'error_invalid' => 'Invalid ID or password.',
        'error_empty' => 'Please enter your ID and password.'
    ]
];

$lt = $login_text[$lang] ?? $login_text['ko'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mb_id = $_POST['mb_id'] ?? '';
    $mb_password = $_POST['mb_password'] ?? '';
    $post_lang = $_POST['lang'] ?? $lang;
    $_SESSION['lang'] = $post_lang;
    
    if ($mb_id && $mb_password) {
        $member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", [$mb_id]);
        
        if ($member && password_verify($mb_password, $member['mb_password'])) {
            $_SESSION['mb_id'] = $member['mb_id'];
            $_SESSION['mb_name'] = $member['mb_name'];
            $_SESSION['mb_level'] = $member['mb_level'];
            header('Location: index.php?lang=' . $post_lang);
            exit;
        } else {
            $error = $lt['error_invalid'];
        }
    } else {
        $error = $lt['error_empty'];
    }
}

// 이미 로그인한 경우 - 주석 처리 (로그인 페이지는 항상 보여줌)
// if (isset($_SESSION['mb_id'])) {
//     header('Location: index.php?lang=' . $lang);
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lt['title']); ?> - Gnuboard</title>
    <link rel="stylesheet" href="theme/pumae/css/style.css">
    <style>
        .login-container {
            padding: 100px 0;
            min-height: 60vh;
        }
        .login-box {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-box h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #007bff;
        }
        .error {
            color: #dc3545;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8d7da;
            border-radius: 4px;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'theme/pumae/header.php'; ?>
    
    <main class="login-container">
        <div class="container">
            <div class="login-box">
                <h1><?php echo htmlspecialchars($lt['title']); ?></h1>
                <?php if ($error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($lt['id']); ?></label>
                        <input type="text" name="mb_id" required autofocus>
                    </div>
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($lt['password']); ?></label>
                        <input type="password" name="mb_password" required>
                    </div>
                    <button type="submit" class="login-btn"><?php echo htmlspecialchars($lt['login_btn']); ?></button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'theme/pumae/footer.php'; ?>
    <script src="theme/pumae/js/main.js"></script>
</body>
</html>

