<?php
/**
 * 결제 대행사 설정 파일
 * NHN KCP, KG이니시스, 토스페이먼츠 설정
 */

if (!defined('PAYMENT_CONFIG_LOADED')) {
    define('PAYMENT_CONFIG_LOADED', true);
    
    // 결제 대행사 설정
    $GLOBALS['payment_config'] = [
        // NHN KCP 설정
        'kcp' => [
            'site_cd' => 'T0000', // 테스트 사이트 코드
            'site_key' => '3grptw1.ZW0JAtsfjF0', // 테스트 사이트 키
            'test_mode' => true,
            'api_url' => 'https://testpay.kcp.co.kr/pg/paygate/paygate.jsp'
        ],
        
        // KG이니시스 설정
        'inicis' => [
            'mid' => 'INIpayTest', // 테스트 MID
            'sign_key' => 'SU5JTElURV9UUklQTEVERVNfS0VZU1RS', // 테스트 시그니처 키
            'test_mode' => true,
            'api_url' => 'https://iniapi.inicis.com/api/v1/formpay'
        ],
        
        // 토스페이먼츠 설정
        'toss' => [
            'client_key' => 'test_ck_...', // 테스트 클라이언트 키
            'secret_key' => 'test_sk_...', // 테스트 시크릿 키
            'test_mode' => true,
            'api_url' => 'https://api.tosspayments.com/v1/payments'
        ]
    ];
}

function get_payment_config($pg) {
    return $GLOBALS['payment_config'][$pg] ?? null;
}

