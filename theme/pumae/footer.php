<?php
// 세션 확인
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_SESSION['lang'] ?? 'ko';

$footer_text = [
    'ko' => [
        'company_name' => '회사명',
        'company' => '주식회사 다무상회',
        'ceo' => '대표자: 홍길동',
        'business_no' => '사업자번호: 171-81-03249',
        'contact' => '연락처',
        'email' => 'E-MAIL: pumae2021@gmail.com',
        'instagram' => '인스타그램: @cordswim',
        'links' => '링크',
        'terms' => '서비스 이용약관',
        'privacy' => '개인정보처리방침'
    ],
    'en' => [
        'company_name' => 'Company Name',
        'company' => '다무상회 Inc.',
        'ceo' => 'CEO: 홍길동',
        'business_no' => 'Business Registration: 171-81-03249',
        'contact' => 'Contact',
        'email' => 'E-MAIL: pumae2021@gmail.com',
        'instagram' => 'Instagram: @cordswim',
        'links' => 'Links',
        'terms' => 'Terms of Service',
        'privacy' => 'Privacy Policy'
    ]
];

$ft = $footer_text[$lang] ?? $footer_text['ko'];
?>
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4><?php echo htmlspecialchars($ft['company_name']); ?></h4>
                <p><?php echo htmlspecialchars($ft['company']); ?></p>
                <p><?php echo htmlspecialchars($ft['ceo']); ?></p>
                <p><?php echo htmlspecialchars($ft['business_no']); ?></p>
            </div>
            <div class="footer-section">
                <h4><?php echo htmlspecialchars($ft['contact']); ?></h4>
                <p><?php echo htmlspecialchars($ft['email']); ?></p>
                <p><?php echo htmlspecialchars($ft['instagram']); ?></p>
            </div>
            <div class="footer-section">
                <h4><?php echo htmlspecialchars($ft['links']); ?></h4>
                <ul>
                    <li><a href="terms.php"><?php echo htmlspecialchars($ft['terms']); ?></a></li>
                    <li><a href="privacy.php"><?php echo htmlspecialchars($ft['privacy']); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Copyright © 다무상회 INC All Rights Reserved.</p>
        </div>
    </div>
</footer>

