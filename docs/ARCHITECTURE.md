# 프로젝트 아키텍처 문서

## 프로젝트 개요

**프로젝트명**: Gnuboard + YoungCart 기반 웹사이트  
**목적**: pumae.kr 스타일의 웹사이트 구축  
**개발 환경**: Windows 10, PHP 8.3.29  
**개발 기간**: 2025년

---

## 기술 스택

### 백엔드
- **언어**: PHP 8.3.29
- **엔진**: Gnuboard (커스텀 구현)
- **쇼핑몰**: YoungCart (통합)
- **데이터베이스**: SQLite 3
- **웹 서버**: PHP Built-in Web Server (개발 환경)

### 프론트엔드
- **HTML5**: 시맨틱 마크업
- **CSS3**: 커스텀 스타일시트 (반응형 디자인)
- **JavaScript**: Vanilla JS (프레임워크 없음)

### 데이터베이스
- **타입**: SQLite (파일 기반)
- **위치**: `data/db/gnuboard.db`
- **ORM/쿼리 빌더**: PDO (PHP Data Objects)

---

## 시스템 아키텍처

### 계층 구조

```
┌─────────────────────────────────────┐
│      Presentation Layer             │
│  (HTML/CSS/JavaScript)              │
│  - theme/pumae/                     │
│  - index.php, shop.php, board.php   │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│      Application Layer              │
│  (PHP Business Logic)               │
│  - 다국어 처리                       │
│  - 세션 관리                         │
│  - 라우팅                           │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│      Data Access Layer              │
│  (PDO + Helper Functions)            │
│  - data/dbconfig.php                │
│  - g5_get_db()                      │
│  - g5_query(), g5_fetch()           │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│      Database Layer                 │
│  (SQLite)                           │
│  - data/db/gnuboard.db              │
└─────────────────────────────────────┘
```

### 데이터 흐름

1. **요청 처리**
   - 사용자 요청 → `index.php` (또는 다른 페이지)
   - 세션 확인 및 언어 설정
   - 다국어 텍스트 로드

2. **데이터 조회**
   - `g5_fetch()` / `g5_fetch_all()` 호출
   - PDO를 통한 SQLite 쿼리 실행
   - 결과 반환

3. **렌더링**
   - 템플릿 파일 포함 (`header.php`, `footer.php`)
   - 다국어 텍스트 적용
   - HTML 출력

---

## 디렉토리 구조

```
Gnuboard-test/
├── data/                          # 데이터 디렉토리
│   ├── db/                        # SQLite 데이터베이스
│   │   └── gnuboard.db           # 메인 DB 파일
│   ├── file/                      # 업로드된 파일
│   ├── editor/                    # 에디터 파일
│   ├── config/                    # 설정 파일
│   └── dbconfig.php               # DB 설정 및 헬퍼 함수
│
├── theme/                         # 테마 디렉토리
│   └── pumae/                     # pumae.kr 스타일 테마
│       ├── css/
│       │   └── style.css         # 메인 스타일시트
│       ├── js/
│       │   └── main.js           # 메인 JavaScript
│       ├── images/                # 이미지 리소스
│       ├── header.php             # 헤더 템플릿
│       └── footer.php             # 푸터 템플릿
│
├── install/                       # 설치 관련
│   └── schema.sql                 # 데이터베이스 스키마
│
├── plugin/                        # 플러그인
│   └── youngcart/                 # YoungCart 플러그인
│
├── docs/                          # 문서
│   ├── ARCHITECTURE.md            # 아키텍처 문서
│   ├── TECH_STACK.md              # 기술 스택 문서
│   └── API.md                     # API 문서
│
├── index.php                      # 메인 페이지
├── shop.php                       # 쇼핑몰 페이지
├── product_detail.php             # 상품 상세 페이지
├── order.php                      # 주문/결제 페이지
├── order_complete.php             # 주문 완료 페이지
├── board.php                      # 게시판 페이지
├── login.php                      # 로그인 페이지
├── logout.php                     # 로그아웃 처리
├── admin_view_data.php            # 관리자 데이터 관리 (GUI)
│
├── sports_tech.php                # Sports Tech 페이지
├── pet_tech.php                   # Pet Tech 페이지
├── platform.php                   # Platform 페이지
├── notice.php                     # Notice 페이지
│
├── payment/                       # 결제 게이트웨이
│   ├── config.php                 # PG사 설정
│   ├── process.php                # 결제 처리 라우터
│   ├── kcp.php                    # NHN KCP 연동
│   ├── inicis.php                 # KG이니시스 연동
│   ├── toss.php                   # 토스페이먼츠 연동
│   ├── success.php                # 결제 성공 처리
│   └── fail.php                   # 결제 실패 처리
│
├── auto_create_tables.php         # 주문 테이블 자동 생성
├── create_test_user.php           # 테스트 계정 생성
├── update_stock_web.php           # 재고 업데이트 (웹)
│
├── install.php                    # 웹 기반 설치 스크립트
├── install_cli.php                # CLI 설치 스크립트
├── quick_install.php              # 빠른 설치 스크립트
│
├── start_server.bat               # Windows 서버 시작 스크립트
├── start_server.ps1               # PowerShell 서버 시작 스크립트
├── install_auto.bat               # 자동 설치 및 실행
│
└── README.md                      # 기본 문서
```

---

## 데이터베이스 스키마

### 주요 테이블

#### 1. 회원 관리
- **테이블**: `g5_member`
- **용도**: 사용자 계정 정보
- **주요 필드**: 
  - `mb_no`: 회원 번호 (PK, AUTO_INCREMENT)
  - `mb_id`: 회원 ID (UNIQUE)
  - `mb_password`: 비밀번호 (해시)
  - `mb_name`: 이름
  - `mb_email`: 이메일
  - `mb_level`: 권한 레벨 (1=일반, 10=관리자)
  - `mb_regdate`: 가입일
  - `mb_lastlogin`: 최종 로그인

#### 2. 게시판
- **테이블**: `g5_board`
- **용도**: 게시판 설정
- **주요 필드**: 
  - `bo_table`: 게시판 테이블명 (PK)
  - `bo_subject`: 게시판 제목
  - `bo_skin`: 스킨 이름
  - `bo_list_level`: 목록 보기 권한
  - `bo_read_level`: 읽기 권한
  - `bo_write_level`: 쓰기 권한
  - `bo_comment_level`: 댓글 권한

#### 3. 게시글
- **테이블**: `g5_write_free` (동적 생성 가능)
- **용도**: 게시글 데이터
- **주요 필드**: 
  - `wr_id`: 게시글 ID (PK)
  - `wr_num`: 정렬 번호
  - `wr_subject`: 제목
  - `wr_content`: 내용
  - `mb_id`: 작성자 ID
  - `wr_name`: 작성자 이름
  - `wr_datetime`: 작성일시
  - `wr_hit`: 조회수
  - `wr_good`: 좋아요 수
  - `wr_nogood`: 싫어요 수
  - `wr_notice`: 공지사항 여부
  - `wr_secret`: 비밀글 여부

#### 4. 파일 관리
- **테이블**: `g5_board_file`
- **용도**: 첨부 파일 정보
- **주요 필드**: 
  - `bf_no`: 파일 번호 (PK)
  - `bo_table`: 게시판 테이블명
  - `wr_id`: 게시글 ID
  - `bf_source`: 원본 파일명
  - `bf_file`: 저장된 파일명
  - `bf_filesize`: 파일 크기
  - `bf_download`: 다운로드 수

#### 5. 상품 관리 (YoungCart)
- **테이블**: `g5_shop_item`
- **용도**: 쇼핑몰 상품 정보
- **주요 필드**: 
  - `it_id`: 상품 ID (PK)
  - `ca_id`: 카테고리 ID
  - `it_name`: 상품명
  - `it_price`: 판매가
  - `it_cust_price`: 정가
  - `it_content`: 상품 설명
  - `it_img1` ~ `it_img3`: 상품 이미지
  - `it_use`: 사용 여부
  - `it_sell_use`: 판매 여부
  - `it_stock_qty`: 재고 수량
  - `it_hit`: 조회수

#### 6. 상품 카테고리
- **테이블**: `g5_shop_category`
- **용도**: 상품 분류
- **주요 필드**: 
  - `ca_id`: 카테고리 ID (PK)
  - `ca_name`: 카테고리명
  - `ca_order`: 정렬 순서
  - `ca_use`: 사용 여부

#### 7. 주문 관리 (YoungCart)
- **테이블**: `g5_shop_order`
- **용도**: 주문 정보 저장
- **주요 필드**: 
  - `od_id`: 주문 ID (PK, AUTO_INCREMENT)
  - `od_no`: 주문번호 (UNIQUE)
  - `mb_id`: 회원 ID
  - `od_name`: 주문자 이름
  - `od_email`: 주문자 이메일
  - `od_tel`: 전화번호
  - `od_hp`: 휴대폰
  - `od_addr1`, `od_addr2`, `od_addr3`: 주소
  - `od_memo`: 배송 메모
  - `od_status`: 주문 상태 (기본값: '주문완료')
  - `od_settle_case`: 결제 방법 (기본값: '무통장입금')
  - `od_receipt_price`: 결제 금액
  - `od_cart_price`: 상품 금액
  - `od_send_cost`: 배송비
  - `od_time`: 주문일시
  - `od_receipt_time`: 결제일시
  - `od_send_time`: 배송일시

#### 8. 주문 상세 (YoungCart)
- **테이블**: `g5_shop_order_item`
- **용도**: 주문 상품 상세 정보
- **주요 필드**: 
  - `oi_id`: 주문 상세 ID (PK, AUTO_INCREMENT)
  - `od_id`: 주문 ID (FK)
  - `od_no`: 주문번호
  - `it_id`: 상품 ID
  - `it_name`: 상품명
  - `it_price`: 상품 단가
  - `ct_qty`: 주문 수량
  - `ct_price`: 상품 총액
  - `it_img1`: 상품 이미지

---

## 핵심 기능

### 1. 다국어 지원 (i18n)

**구현 방식**:
- 세션 기반 언어 저장
- GET 파라미터로 언어 전환 (`?lang=ko`, `?lang=en`)
- 배열 기반 다국어 텍스트 관리

**파일 위치**:
- `index.php`: 메인 페이지 다국어 배열
- `theme/pumae/header.php`: 헤더 다국어 배열
- `theme/pumae/footer.php`: 푸터 다국어 배열

**사용 예시**:
```php
$text = [
    'ko' => ['title' => '메인'],
    'en' => ['title' => 'Main']
];
$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$t = $text[$lang] ?? $text['ko'];
echo $t['title'];
```

### 2. 데이터베이스 추상화

**PDO 기반 헬퍼 함수**:
- `g5_get_db()`: 데이터베이스 연결 (싱글톤 패턴)
- `g5_query($sql, $params)`: 쿼리 실행
- `g5_fetch($sql, $params)`: 단일 행 조회
- `g5_fetch_all($sql, $params)`: 여러 행 조회

**보안**:
- Prepared Statements 사용 (SQL Injection 방지)
- PDO Exception 처리

### 3. 세션 관리

**구현**:
- PHP 세션 사용
- 언어 설정 저장
- 로그인 상태 관리

### 4. 템플릿 시스템

**구조**:
- 헤더/푸터 분리 (`header.php`, `footer.php`)
- 재사용 가능한 컴포넌트
- 일관된 레이아웃

---

## 보안 고려사항

### 현재 구현
- ✅ Prepared Statements (SQL Injection 방지)
- ✅ `htmlspecialchars()` 사용 (XSS 방지)
- ✅ 비밀번호 해싱 (`password_hash()`)
- ✅ 세션 기반 인증

### 개선 필요 사항
- ⚠️ CSRF 토큰 미구현
- ⚠️ 파일 업로드 검증 미구현
- ⚠️ Rate Limiting 미구현
- ⚠️ 프로덕션 환경에서는 SQLite → MySQL/MariaDB 전환 권장

---

## 개발 환경 설정

### 요구사항
- **PHP**: 7.4 이상 (현재 8.3.29 사용)
- **확장**: PDO SQLite
- **웹 서버**: PHP Built-in Web Server (개발용)

### 실행 방법

1. **PHP 설치**
   ```bash
   # Windows: https://windows.php.net/download/
   # 환경 변수 PATH에 PHP 경로 추가
   ```

2. **SQLite 확장 활성화**
   ```ini
   # php.ini
   extension=pdo_sqlite
   extension=sqlite3
   ```

3. **설치 실행**
   ```bash
   php quick_install.php
   ```

4. **서버 시작**
   ```bash
   php -S localhost:8000
   ```

---

## 배포 고려사항

### 개발 환경 (현재)
- PHP Built-in Web Server
- SQLite
- 단일 사용자 테스트

### 프로덕션 환경 (권장)
- **웹 서버**: Apache 2.4+ 또는 Nginx 1.18+
- **PHP**: PHP-FPM
- **데이터베이스**: MySQL 8.0+ 또는 MariaDB 10.5+
- **캐싱**: Redis 또는 Memcached (선택)
- **CDN**: 정적 리소스 (선택)

### 마이그레이션 계획
1. SQLite → MySQL 스키마 변환
2. PDO 연결 설정 변경
3. 웹 서버 설정 (Apache/Nginx)
4. SSL/TLS 인증서 설정
5. 환경 변수 관리 (.env 파일)

---

## 성능 최적화

### 현재 상태
- 정적 파일 캐싱 없음
- 데이터베이스 쿼리 최적화 필요
- 이미지 최적화 미구현

### 개선 방안
- 데이터베이스 인덱스 추가
- CSS/JS 파일 압축 및 병합
- 이미지 최적화 (WebP 변환)
- CDN 활용

---

## 확장 가능성

### 추가 가능한 기능
- 관리자 대시보드 (일부 구현됨: `admin_view_data.php`)
- 파일 업로드 시스템
- 댓글 시스템
- 검색 기능
- 이메일 알림
- 소셜 로그인 (OAuth)
- 장바구니 기능
- 쿠폰 시스템
- 포인트 시스템

### 플러그인 시스템
- `plugin/youngcart/`: 쇼핑몰 기능
- 추가 플러그인 확장 가능

---

## 참고 자료

- **Gnuboard**: https://sir.kr/g5
- **YoungCart**: https://sir.kr/youngcart
- **PHP 공식 문서**: https://www.php.net/
- **SQLite 문서**: https://www.sqlite.org/

---

## 버전 정보

- **PHP**: 8.3.29
- **SQLite**: 3.x (PDO)
- **프로젝트 버전**: 1.0.0 (초기 릴리스)

---

## 주요 페이지 목록

### 공개 페이지
- `index.php`: 메인 페이지 (히어로, 회사 소개, 성과, 뉴스)
- `shop.php`: 쇼핑몰 상품 목록
- `product_detail.php`: 상품 상세 페이지
- `order.php`: 주문/결제 페이지
- `order_complete.php`: 주문 완료 페이지
- `board.php`: 게시판 목록
- `login.php`: 로그인 페이지
- `sports_tech.php`: Sports Tech 페이지
- `pet_tech.php`: Pet Tech 페이지
- `platform.php`: Platform 페이지
- `notice.php`: 공지사항 페이지

### 관리자 페이지
- `admin_view_data.php`: 데이터 관리 (GUI)

### 결제 페이지
- `payment/process.php`: 결제 처리 라우터
- `payment/kcp.php`: KCP 결제
- `payment/inicis.php`: 이니시스 결제
- `payment/toss.php`: 토스페이먼츠 결제
- `payment/success.php`: 결제 성공
- `payment/fail.php`: 결제 실패

### 유틸리티
- `auto_create_tables.php`: 주문 테이블 자동 생성
- `create_test_user.php`: 테스트 계정 생성
- `update_stock_web.php`: 재고 업데이트

---

## 작성일
2025년 12월 29일

