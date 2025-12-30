<?php
/**
 * PG사 결제 설정 파일
 */

// PG사 설정
define('PG_KCP_SITE_CD', ''); // KCP 사이트 코드
define('PG_KCP_SITE_KEY', ''); // KCP 사이트 키
define('PG_INICIS_MID', ''); // 이니시스 상점 ID
define('PG_INICIS_KEY', ''); // 이니시스 키
define('PG_TOSS_CLIENT_KEY', ''); // 토스페이먼츠 클라이언트 키
define('PG_TOSS_SECRET_KEY', ''); // 토스페이먼츠 시크릿 키

// 결제 성공/실패 URL
define('PG_SUCCESS_URL', 'http://localhost:8000/payment/success.php');
define('PG_FAIL_URL', 'http://localhost:8000/payment/fail.php');

// 테스트 모드
define('PG_TEST_MODE', true);

