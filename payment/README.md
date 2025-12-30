# PG사 결제 연동 가이드

## 개요

이 프로젝트는 국내 3대 PG사(NHN KCP, KG이니시스, 토스페이먼츠)와 연동할 수 있는 결제 시스템을 포함하고 있습니다.

## 설정 방법

### 1. 설정 파일 수정

`payment/config.php` 파일을 열어서 각 PG사의 인증 정보를 입력하세요:

```php
// NHN KCP
define('PG_KCP_SITE_CD', 'YOUR_KCP_SITE_CD');
define('PG_KCP_SITE_KEY', 'YOUR_KCP_SITE_KEY');

// KG이니시스
define('PG_INICIS_MID', 'YOUR_INICIS_MID');
define('PG_INICIS_KEY', 'YOUR_INICIS_KEY');

// 토스페이먼츠
define('PG_TOSS_CLIENT_KEY', 'YOUR_TOSS_CLIENT_KEY');
define('PG_TOSS_SECRET_KEY', 'YOUR_TOSS_SECRET_KEY');
```

### 2. PG사 계정 발급

각 PG사에 가입하고 상점 정보를 발급받아야 합니다:

- **NHN KCP**: https://admin.kcp.co.kr/
- **KG이니시스**: https://www.inicis.com/
- **토스페이먼츠**: https://www.tosspayments.com/

### 3. 테스트 모드

개발 중에는 `PG_TEST_MODE`를 `true`로 설정하세요:

```php
define('PG_TEST_MODE', true);
```

운영 환경에서는 `false`로 변경하세요.

## 결제 흐름

1. 사용자가 상품 상세 페이지에서 "바로 구매" 클릭
2. 주문/결제 페이지(`order.php`)에서 배송 정보 입력
3. 결제 방법 선택 (무통장입금, KCP, 이니시스, 토스페이먼츠)
4. "주문하기" 버튼 클릭
5. 선택한 PG사 결제 페이지로 리다이렉트
6. 결제 완료 후 `order_complete.php`로 이동

## 지원 결제 방법

- **무통장입금**: 별도 PG 연동 없이 주문만 생성
- **NHN KCP**: 신용카드, 계좌이체, 가상계좌 등
- **KG이니시스**: 신용카드, 계좌이체, 가상계좌 등
- **토스페이먼츠**: 신용카드, 계좌이체, 가상계좌, 간편결제 등

## 보안 주의사항

- `payment/config.php` 파일은 절대 공개되지 않도록 주의하세요
- 운영 환경에서는 HTTPS를 사용하세요
- PG사의 시크릿 키는 안전하게 관리하세요

