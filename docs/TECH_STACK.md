# 기술 스택 상세 문서

## 개요

이 문서는 프로젝트에서 사용하는 모든 기술 스택과 라이브러리에 대한 상세 정보를 제공합니다.

---

## 백엔드 기술

### PHP

**버전**: 8.3.29  
**용도**: 서버 사이드 스크립팅 언어  
**특징**:
- 객체 지향 프로그래밍 지원
- 동적 타입 시스템
- 풍부한 내장 함수

**주요 사용 기능**:
- 세션 관리 (`session_start()`, `$_SESSION`)
- PDO (PHP Data Objects)
- 배열 조작
- 문자열 처리

**설치 위치**: `C:\php\` (Windows 환경)

---

### Gnuboard 엔진

**타입**: 커스텀 CMS/게시판 엔진  
**기반**: PHP  
**특징**:
- 경량 구조
- SQLite 지원
- 모듈화된 설계

**주요 구성 요소**:
- 데이터베이스 추상화 레이어
- 회원 관리 시스템
- 게시판 시스템
- 파일 관리 시스템

**데이터베이스 접두사**: `g5_` (Gnuboard 5)

---

### YoungCart

**타입**: 쇼핑몰 플러그인  
**통합 방식**: Gnuboard와 통합  
**기능**:
- 상품 관리
- 카테고리 관리
- 주문 관리 (구현 완료)
- 결제 시스템 (구현 완료)
- 장바구니 (미구현)

**테이블**:
- `g5_shop_item`: 상품 정보
- `g5_shop_category`: 상품 카테고리
- `g5_shop_order`: 주문 정보
- `g5_shop_order_item`: 주문 상세 정보

---

## 데이터베이스

### SQLite

**버전**: 3.x  
**타입**: 파일 기반 관계형 데이터베이스  
**위치**: `data/db/gnuboard.db`

**특징**:
- 서버리스 (별도 DB 서버 불필요)
- 경량 (단일 파일)
- 개발 환경에 적합
- 트랜잭션 지원

**제한사항**:
- 동시 쓰기 제한
- 프로덕션 환경에는 부적합
- 복잡한 쿼리 성능 제한

**PDO 드라이버**: `pdo_sqlite`

**연결 예시**:
```php
$db = new PDO('sqlite:data/db/gnuboard.db');
```

---

### PDO (PHP Data Objects)

**용도**: 데이터베이스 추상화 레이어  
**드라이버**: PDO_SQLITE

**주요 메서드**:
- `PDO::__construct()`: 데이터베이스 연결
- `PDO::prepare()`: Prepared Statement 생성
- `PDOStatement::execute()`: 쿼리 실행
- `PDOStatement::fetch()`: 단일 행 조회
- `PDOStatement::fetchAll()`: 여러 행 조회

**보안 기능**:
- Prepared Statements (SQL Injection 방지)
- 파라미터 바인딩

---

## 결제 게이트웨이

### NHN KCP

**타입**: 전자결제대행사 (PG)  
**용도**: 신용카드, 계좌이체, 가상계좌 결제  
**구현**: `payment/kcp.php`

**필요한 키**:
- `PG_KCP_SITE_CD`: 사이트 코드
- `PG_KCP_SITE_KEY`: 사이트 키

**특징**:
- iframe 통합 지원
- 테스트 모드 지원
- API 키 없이도 주문 플로우 테스트 가능

---

### KG이니시스

**타입**: 전자결제대행사 (PG)  
**용도**: 신용카드, 계좌이체, 가상계좌 결제  
**구현**: `payment/inicis.php`

**필요한 키**:
- `PG_INICIS_MID`: 상점 ID (MID)
- `PG_INICIS_KEY`: 상점 키

**특징**:
- iframe 통합 지원
- 테스트 모드 지원
- API 키 없이도 주문 플로우 테스트 가능

---

### 토스페이먼츠

**타입**: 전자결제대행사 (PG)  
**용도**: 신용카드, 계좌이체, 가상계좌, 간편결제  
**구현**: `payment/toss.php`

**필요한 키**:
- `PG_TOSS_CLIENT_KEY`: 클라이언트 키 (공개)
- `PG_TOSS_SECRET_KEY`: 시크릿 키 (비공개)

**특징**:
- JavaScript SDK 사용
- iframe 통합 지원
- 테스트 모드 지원
- API 키 없이도 주문 플로우 테스트 가능

---

## 프론트엔드 기술

### HTML5

**버전**: HTML5  
**특징**:
- 시맨틱 마크업
- 반응형 메타 태그
- 접근성 고려

**주요 태그**:
- `<header>`, `<nav>`, `<main>`, `<footer>`
- `<section>`, `<article>`
- `<meta name="viewport">`

---

### CSS3

**파일**: `theme/pumae/css/style.css`  
**특징**:
- 커스텀 스타일시트
- 반응형 디자인 (미디어 쿼리)
- Flexbox/Grid 레이아웃
- CSS 변수 미사용 (향후 개선 가능)

**주요 기능**:
- 반응형 그리드
- 호버 효과
- 트랜지션 애니메이션
- 모바일 최적화

**미디어 쿼리 예시**:
```css
@media (max-width: 768px) {
    .main-nav ul {
        flex-direction: column;
    }
}
```

---

### JavaScript

**타입**: Vanilla JavaScript (프레임워크 없음)  
**파일**: `theme/pumae/js/main.js`

**주요 기능**:
- DOM 조작
- 이벤트 리스너
- 스무스 스크롤

**사용 라이브러리**: 없음 (순수 JavaScript)

**향후 개선 가능**:
- 모듈화 (ES6 Modules)
- 빌드 도구 (Webpack, Vite)
- 프레임워크 도입 (Vue.js, React 등)

---

## 웹 서버

### PHP Built-in Web Server

**명령어**: `php -S localhost:8000`  
**용도**: 개발 환경 전용

**특징**:
- PHP 5.4.0 이상에서 제공
- 별도 웹 서버 설치 불필요
- 빠른 개발/테스트

**제한사항**:
- 단일 스레드
- 프로덕션 환경 부적합
- 고급 기능 제한

**대안 (프로덕션)**:
- Apache 2.4+ with mod_php
- Nginx 1.18+ with PHP-FPM

---

## 개발 도구

### 설치 스크립트

**웹 기반**: `install.php`
- 브라우저에서 실행
- GUI 폼 제공

**CLI 기반**: `install_cli.php`
- 터미널에서 실행
- 대화형 입력

**자동 설치**: `quick_install.php`
- 기본값으로 자동 설치
- 테스트용

---

### 서버 실행 스크립트

**Windows 배치**: `start_server.bat`
```batch
php -S localhost:8000
```

**PowerShell**: `start_server.ps1`
```powershell
php -S localhost:8000
```

**자동 설치 및 실행**: `install_auto.bat`
- 설치 확인
- 자동 서버 시작

---

## 보안 기술

### 비밀번호 해싱

**함수**: `password_hash()`  
**알고리즘**: bcrypt (기본)  
**검증**: `password_verify()`

**예시**:
```php
$hash = password_hash($password, PASSWORD_DEFAULT);
$valid = password_verify($password, $hash);
```

---

### XSS 방지

**함수**: `htmlspecialchars()`  
**용도**: 사용자 입력 출력 시 이스케이프

**예시**:
```php
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

---

### SQL Injection 방지

**방법**: Prepared Statements  
**PDO 사용**:
```php
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
```

---

## 세션 관리

### PHP 세션

**함수**: `session_start()`  
**저장소**: 서버 측 (파일 또는 메모리)

**사용 목적**:
- 언어 설정 저장
- 로그인 상태 관리
- 사용자 정보 저장

**설정**:
- 기본 설정 사용
- 향후 Redis/Memcached 연동 가능

---

## 다국어 지원

### 구현 방식

**방법**: 배열 기반 다국어  
**저장**: PHP 배열  
**전환**: GET 파라미터 + 세션

**구조**:
```php
$text = [
    'ko' => ['key' => '한국어'],
    'en' => ['key' => 'English']
];
```

**향후 개선**:
- gettext 사용
- JSON 파일 기반
- 데이터베이스 저장

---

## 파일 구조

### 템플릿 시스템

**헤더**: `theme/pumae/header.php`
- 네비게이션
- 언어 전환
- 로그인 버튼

**푸터**: `theme/pumae/footer.php`
- 회사 정보
- 링크
- 저작권

**포함 방식**: `include` 또는 `require`

---

## 의존성

### 필수 확장

- **PDO**: 데이터베이스 접근
- **PDO_SQLITE**: SQLite 지원
- **Session**: 세션 관리

### 선택적 확장

- **GD**: 이미지 처리 (향후)
- **cURL**: HTTP 요청 (향후)
- **JSON**: JSON 처리 (기본 포함)

---

## 버전 호환성

### PHP 버전

- **최소**: PHP 7.4.0
- **권장**: PHP 8.0+
- **현재**: PHP 8.3.29

### 브라우저 지원

- Chrome (최신 2개 버전)
- Firefox (최신 2개 버전)
- Safari (최신 2개 버전)
- Edge (최신 2개 버전)

---

## 성능 고려사항

### 현재 상태

- 정적 파일 캐싱 없음
- 데이터베이스 쿼리 최적화 필요
- 이미지 최적화 미구현

### 개선 방안

1. **캐싱**
   - OPcache 활성화
   - 정적 파일 캐싱
   - 데이터베이스 쿼리 캐싱

2. **최적화**
   - CSS/JS 압축
   - 이미지 최적화 (WebP)
   - Gzip 압축

3. **CDN**
   - 정적 리소스 CDN 배포
   - 이미지 CDN

---

## 라이선스

### 오픈소스 라이선스

- **PHP**: PHP License
- **SQLite**: Public Domain
- **Gnuboard**: GPL v2
- **YoungCart**: 상업 라이선스 (확인 필요)

---

## 추가 페이지

### 콘텐츠 페이지
- **sports_tech.php**: Sports Tech 소개 페이지
- **pet_tech.php**: Pet Tech 소개 페이지
- **platform.php**: Platform 소개 페이지
- **notice.php**: 공지사항 페이지 (게시판 요약)

**특징**:
- 다국어 지원
- 일관된 레이아웃 (header/footer 통합)
- 반응형 디자인

---

## 작성일
2025년 12월 29일

