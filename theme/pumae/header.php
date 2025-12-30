<?php
// 세션 확인
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 다국어 텍스트
$text = [
    'ko' => [
        'sports_tech' => 'Sports Tech',
        'pet_tech' => 'Pet Tech',
        'platform' => 'Platform',
        'notice' => 'Notice',
        'news' => 'News',
        'review' => 'Review',
        'info' => 'Info',
        'shop' => 'SHOP',
        'login' => 'LOGIN'
    ],
    'en' => [
        'sports_tech' => 'Sports Tech',
        'pet_tech' => 'Pet Tech',
        'platform' => 'Platform',
        'notice' => 'Notice',
        'news' => 'News',
        'review' => 'Review',
        'info' => 'Info',
        'shop' => 'SHOP',
        'login' => 'LOGIN'
    ]
];

// 언어 설정 (GET 파라미터 우선, 없으면 세션, 없으면 기본값 ko)
$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;

// 언어별 텍스트 가져오기
$t = isset($text[$lang]) ? $text[$lang] : $text['ko'];
// 누락된 키가 있으면 한국어 버전으로 채우기
$t = array_merge($text['ko'], $t);
?>
<header class="header">
    <div class="container">
        <div class="header-top">
            <div class="logo">
                <a href="index.php?lang=<?php echo $lang; ?>">LOGO</a>
            </div>
            <div class="header-actions">
                <div class="lang-switcher">
                    <?php
                    $current_params = $_GET;
                    $current_params['lang'] = 'ko';
                    $ko_url = $_SERVER['PHP_SELF'] . '?' . http_build_query($current_params);
                    $current_params['lang'] = 'en';
                    $en_url = $_SERVER['PHP_SELF'] . '?' . http_build_query($current_params);
                    ?>
                    <a href="<?php echo htmlspecialchars($ko_url); ?>" class="<?php echo $lang === 'ko' ? 'active' : ''; ?>">KR</a>
                    <a href="<?php echo htmlspecialchars($en_url); ?>" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
                </div>
                <?php
                // 로그인 상태에 따라 버튼 변경
                if (isset($_SESSION['mb_id'])) {
                    // 로그인된 경우: 사용자 이름과 로그아웃 버튼
                    $user_text = [
                        'ko' => ['logout' => '로그아웃'],
                        'en' => ['logout' => 'Logout']
                    ];
                    $ut = $user_text[$lang] ?? $user_text['ko'];
                    $mb_name = htmlspecialchars($_SESSION['mb_name'] ?? $_SESSION['mb_id']);
                ?>
                    <span style="margin-right: 10px; color: #666; font-size: 14px;"><?php echo $mb_name; ?></span>
                    <a href="logout.php?lang=<?php echo htmlspecialchars($lang); ?>" class="login-btn"><?php echo $ut['logout']; ?></a>
                <?php } else { ?>
                    <a href="login.php?lang=<?php echo htmlspecialchars($lang); ?>" class="login-btn" id="login-link"><?php echo $t['login']; ?></a>
                <?php } ?>
            </div>
        </div>
        <nav class="main-nav">
            <ul>
                <li class="has-submenu">
                    <a href="sports_tech.php?lang=<?php echo $lang; ?>"><?php echo $t['sports_tech']; ?></a>
                    <ul class="submenu">
                        <li><a href="product.php?type=diva&lang=<?php echo $lang; ?>">DIVA for swim</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="pet_tech.php?lang=<?php echo $lang; ?>"><?php echo $t['pet_tech']; ?></a>
                    <ul class="submenu">
                        <li><a href="product.php?type=luhearty-dog&lang=<?php echo $lang; ?>">LUHearty for dogs</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="platform.php?lang=<?php echo $lang; ?>"><?php echo $t['platform']; ?></a>
                    <ul class="submenu">
                        <li><a href="product.php?type=luhearty&lang=<?php echo $lang; ?>">LUHearty</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="notice.php?lang=<?php echo $lang; ?>"><?php echo $t['notice']; ?></a>
                    <ul class="submenu">
                        <li><a href="board.php?bo_table=news&lang=<?php echo $lang; ?>"><?php echo $t['news']; ?></a></li>
                        <li><a href="board.php?bo_table=review&lang=<?php echo $lang; ?>"><?php echo $t['review']; ?></a></li>
                        <li><a href="board.php?bo_table=info&lang=<?php echo $lang; ?>"><?php echo $t['info']; ?></a></li>
                    </ul>
                </li>
                <li><a href="shop.php?lang=<?php echo $lang; ?>"><?php echo $t['shop']; ?></a></li>
                <?php
                // 관리자 메뉴 (mb_level >= 10인 경우만 표시)
                $is_admin = isset($_SESSION['mb_level']) && $_SESSION['mb_level'] >= 10;
                if ($is_admin):
                    $admin_text = [
                        'ko' => '관리',
                        'en' => 'Admin'
                    ];
                    $admin_menu_text = $admin_text[$lang] ?? $admin_text['ko'];
                ?>
                <li><a href="admin_view_data.php?lang=<?php echo $lang; ?>"><?php echo htmlspecialchars($admin_menu_text); ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

