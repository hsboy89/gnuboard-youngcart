# Gnuboard & YoungCart 기능 사용 현황

## 개요

이 문서는 현재 프로젝트에서 실제로 사용된 Gnuboard 기능과 YoungCart 기능을 상세히 정리합니다.

---

## Gnuboard 기능 사용 현황

### 1. 회원 관리 시스템

#### 데이터베이스 테이블
- **테이블명**: `g5_member`
- **위치**: `install/schema.sql` (1-13줄)

#### 주요 필드
- `mb_no`: 회원 번호 (PK, AUTO_INCREMENT)
- `mb_id`: 회원 ID (UNIQUE)
- `mb_password`: 비밀번호 (해시 저장)
- `mb_name`: 이름
- `mb_email`: 이메일
- `mb_level`: 권한 레벨 (1=일반, 10=관리자)
- `mb_regdate`: 가입일시
- `mb_lastlogin`: 최종 로그인

#### 구현 파일
- **로그인 페이지**: `login.php`
  - 로그인 폼 제공
  - 비밀번호 검증 (`password_verify()`)
  - 세션에 회원 정보 저장
  - 다국어 지원

#### 사용 예시
```php
// 회원 조회
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", [$mb_id]);

// 로그인 검증
if ($member && password_verify($mb_password, $member['mb_password'])) {
    $_SESSION['mb_id'] = $member['mb_id'];
    $_SESSION['mb_name'] = $member['mb_name'];
    $_SESSION['mb_level'] = $member['mb_level'];
}
```

#### 기능 상태
- ✅ 회원 조회
- ✅ 로그인/인증
- ✅ 세션 관리
- ❌ 회원 가입 (미구현)
- ❌ 회원 정보 수정 (미구현)
- ❌ 비밀번호 찾기 (미구현)

---

### 2. 게시판 시스템

#### 데이터베이스 테이블

**게시판 설정 테이블**
- **테이블명**: `g5_board`
- **위치**: `install/schema.sql` (15-28줄)

**게시글 테이블**
- **테이블명**: `g5_write_free` (동적 생성 가능)
- **위치**: `install/schema.sql` (30-49줄)
- **동적 테이블**: 게시판별로 `g5_write_{bo_table}` 형식으로 생성 가능

#### 주요 필드

**게시판 설정 (`g5_board`)**
- `bo_table`: 게시판 테이블명 (PK)
- `bo_subject`: 게시판 제목
- `bo_skin`: 스킨 이름
- `bo_list_level`: 목록 보기 권한 레벨
- `bo_read_level`: 읽기 권한 레벨
- `bo_write_level`: 쓰기 권한 레벨
- `bo_comment_level`: 댓글 권한 레벨
- `bo_page_rows`: 페이지당 게시글 수

**게시글 (`g5_write_free`)**
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

#### 구현 파일
- **게시판 페이지**: `board.php`
  - 게시판 목록 조회
  - 페이지네이션
  - 다국어 지원

- **메인 페이지**: `index.php` (150-171줄)
  - 최신 뉴스 게시글 조회
  - `g5_write_free` 테이블에서 데이터 조회

#### 사용 예시
```php
// 게시판 정보 조회
$board = g5_fetch("SELECT * FROM g5_board WHERE bo_table = ?", [$bo_table]);

// 게시글 목록 조회
$posts = g5_fetch_all(
    "SELECT * FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 10"
);

// 최신 뉴스 조회 (index.php)
$news = g5_fetch_all(
    "SELECT * FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 3"
);
```

#### 생성된 게시판
설치 시 자동 생성되는 게시판:
- `news`: News 게시판
- `review`: Review 게시판
- `info`: Info 게시판
- `free`: 자유게시판

#### 기능 상태
- ✅ 게시판 목록 조회
- ✅ 게시글 목록 조회
- ✅ 페이지네이션
- ✅ 조회수 표시
- ❌ 게시글 작성 (미구현)
- ❌ 게시글 수정 (미구현)
- ❌ 게시글 삭제 (미구현)
- ❌ 댓글 기능 (미구현)
- ❌ 파일 첨부 (미구현)

---

### 3. 파일 관리 시스템

#### 데이터베이스 테이블
- **테이블명**: `g5_board_file`
- **위치**: `install/schema.sql` (51-61줄)

#### 주요 필드
- `bf_no`: 파일 번호 (PK)
- `bo_table`: 게시판 테이블명
- `wr_id`: 게시글 ID
- `bf_source`: 원본 파일명
- `bf_file`: 저장된 파일명
- `bf_filesize`: 파일 크기
- `bf_download`: 다운로드 수
- `bf_datetime`: 업로드 일시

#### 구현 상태
- ✅ 테이블 생성됨
- ❌ 파일 업로드 기능 (미구현)
- ❌ 파일 다운로드 기능 (미구현)
- ❌ 파일 관리 기능 (미구현)

#### 디렉토리 구조
- `data/file/`: 업로드된 파일 저장 디렉토리 (생성됨)
- `data/editor/`: 에디터 파일 저장 디렉토리 (생성됨)

---

### 4. 데이터베이스 추상화 레이어

#### 구현 파일
- **설정 파일**: `data/dbconfig.php`

#### 제공 함수

**g5_get_db()**
- SQLite 데이터베이스 연결 반환
- 싱글톤 패턴 사용
- PDO 객체 반환

**g5_query($sql, $params = [])**
- SQL 쿼리 실행
- Prepared Statements 사용
- PDOStatement 반환

**g5_fetch($sql, $params = [])**
- 단일 행 조회
- 연관 배열 반환

**g5_fetch_all($sql, $params = [])**
- 여러 행 조회
- 배열 반환

#### 사용 예시
```php
// 모든 페이지에서 사용
require_once 'data/dbconfig.php';

// 회원 조회
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", ['admin']);

// 게시글 목록
$posts = g5_fetch_all("SELECT * FROM g5_write_free ORDER BY wr_datetime DESC");
```

#### 기능 상태
- ✅ 데이터베이스 연결
- ✅ 쿼리 실행
- ✅ 단일/다중 행 조회
- ✅ Prepared Statements (보안)
- ✅ SQLite 지원

---

### 5. 세션 관리

#### 구현 위치
- 모든 PHP 페이지에서 사용
- `session_start()` 호출
- `$_SESSION` 배열 사용

#### 저장 정보
- `$_SESSION['lang']`: 현재 언어 설정 (ko/en)
- `$_SESSION['mb_id']`: 로그인한 회원 ID
- `$_SESSION['mb_name']`: 로그인한 회원 이름
- `$_SESSION['mb_level']`: 로그인한 회원 권한 레벨

#### 사용 예시
```php
// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 언어 설정 저장
$_SESSION['lang'] = $lang;

// 로그인 정보 저장
$_SESSION['mb_id'] = $member['mb_id'];
```

#### 기능 상태
- ✅ 세션 시작/관리
- ✅ 언어 설정 저장
- ✅ 로그인 상태 저장
- ✅ 세션 기반 인증

---

## YoungCart 기능 사용 현황

### 1. 상품 관리 시스템

#### 데이터베이스 테이블
- **테이블명**: `g5_shop_item`
- **위치**: `install/schema.sql` (63-83줄)

#### 주요 필드
- `it_id`: 상품 ID (PK, AUTO_INCREMENT)
- `ca_id`: 카테고리 ID
- `it_name`: 상품명
- `it_price`: 판매가
- `it_cust_price`: 정가
- `it_content`: 상품 설명
- `it_mobile_content`: 모바일 상품 설명
- `it_img1`, `it_img2`, `it_img3`: 상품 이미지 (최대 3개)
- `it_use`: 사용 여부 (1=사용, 0=미사용)
- `it_sell_use`: 판매 여부 (1=판매, 0=미판매)
- `it_stock_qty`: 재고 수량
- `it_hit`: 조회수
- `it_time`: 등록일시

#### 구현 파일
- **쇼핑몰 페이지**: `shop.php`
  - 상품 목록 조회
  - 상품 그리드 레이아웃
  - 상품 이미지 표시
  - 상품 가격 표시
  - 다국어 지원

#### 사용 예시
```php
// 판매 중인 상품 목록 조회
$items = g5_fetch_all(
    "SELECT * FROM g5_shop_item 
     WHERE it_use = 1 AND it_sell_use = 1 
     ORDER BY it_time DESC LIMIT 12"
);

// 상품 표시
foreach ($items as $item) {
    echo $item['it_name'];
    echo number_format($item['it_price']) . '원';
}
```

#### 구현 파일
- **쇼핑몰 페이지**: `shop.php`
  - 상품 목록 조회
  - 상품 그리드 레이아웃
  - 상품 이미지 표시
  - 상품 가격 표시
  - 다국어 지원

- **상품 상세 페이지**: `product_detail.php` (신규)
  - 상품 상세 정보 표시
  - 상품 이미지 갤러리 (메인 이미지 + 썸네일)
  - 재고 상태 표시
  - 제조사/원산지 정보
  - 바로 구매 버튼
  - 다국어 지원

#### 기능 상태
- ✅ 상품 목록 조회
- ✅ 상품 정보 표시
- ✅ 상품 이미지 표시
- ✅ 가격 표시
- ✅ 상품 상세 페이지 (구현 완료)
- ✅ 상품 조회수 증가
- ✅ 재고 상태 표시
- ❌ 상품 등록/수정 (관리자 GUI에서 가능)
- ❌ 상품 삭제 (관리자 GUI에서 가능)
- ❌ 상품 검색 (미구현)
- ❌ 상품 필터링 (미구현)

---

### 2. 상품 카테고리 시스템

#### 데이터베이스 테이블
- **테이블명**: `g5_shop_category`
- **위치**: `install/schema.sql` (85-91줄)

#### 주요 필드
- `ca_id`: 카테고리 ID (PK)
- `ca_name`: 카테고리명
- `ca_order`: 정렬 순서
- `ca_use`: 사용 여부 (1=사용, 0=미사용)

#### 구현 상태
- ✅ 테이블 생성됨
- ❌ 카테고리 조회 (미구현)
- ❌ 카테고리별 상품 필터링 (미구현)
- ❌ 카테고리 관리 (미구현)

#### 향후 활용 방안
```php
// 카테고리별 상품 조회 (구현 예정)
$items = g5_fetch_all(
    "SELECT * FROM g5_shop_item 
     WHERE ca_id = ? AND it_use = 1 
     ORDER BY it_time DESC", 
    [$ca_id]
);
```

### 3. 주문 관리 시스템

#### 데이터베이스 테이블
- **주문 테이블**: `g5_shop_order`
- **주문 상세 테이블**: `g5_shop_order_item`
- **위치**: `install/schema.sql` (93-137줄), `auto_create_tables.php`

#### 주요 필드

**주문 (`g5_shop_order`)**
- `od_id`: 주문 ID (PK)
- `od_no`: 주문번호 (UNIQUE)
- `mb_id`: 회원 ID
- `od_name`: 주문자 이름
- `od_email`: 주문자 이메일
- `od_tel`: 전화번호
- `od_hp`: 휴대폰
- `od_addr1`, `od_addr2`, `od_addr3`: 주소
- `od_memo`: 배송 메모
- `od_status`: 주문 상태
- `od_settle_case`: 결제 방법
- `od_receipt_price`: 결제 금액
- `od_cart_price`: 상품 금액
- `od_send_cost`: 배송비
- `od_time`: 주문일시

**주문 상세 (`g5_shop_order_item`)**
- `oi_id`: 주문 상세 ID (PK)
- `od_id`: 주문 ID (FK)
- `od_no`: 주문번호
- `it_id`: 상품 ID
- `it_name`: 상품명
- `it_price`: 상품 단가
- `ct_qty`: 주문 수량
- `ct_price`: 상품 총액
- `it_img1`: 상품 이미지

#### 구현 파일
- **주문 페이지**: `order.php`
  - 주문 정보 입력 폼
  - 배송 정보 입력
  - 결제 방법 선택 (무통장입금, KCP, 이니시스, 토스페이먼츠)
  - 주문 생성 및 결제 처리
  - 다국어 지원

- **주문 완료 페이지**: `order_complete.php`
  - 주문 완료 정보 표시
  - 주문번호 표시
  - 주문 상품 목록
  - 배송 정보 표시
  - 결제 상태 표시

- **자동 테이블 생성**: `auto_create_tables.php`
  - 주문 테이블이 없으면 자동 생성
  - `order.php`와 `payment/process.php`에서 호출

#### 사용 예시
```php
// 주문 생성
$od_no = 'ORD' . date('YmdHis') . rand(1000, 9999);
g5_query(
    "INSERT INTO g5_shop_order (od_no, od_name, od_email, od_tel, ...) 
     VALUES (?, ?, ?, ?, ...)",
    [$od_no, $name, $email, $tel, ...]
);

// 주문 상세 생성
g5_query(
    "INSERT INTO g5_shop_order_item (od_id, od_no, it_id, it_name, ...) 
     VALUES (?, ?, ?, ?, ...)",
    [$od_id, $od_no, $it_id, $it_name, ...]
);
```

#### 기능 상태
- ✅ 주문 생성
- ✅ 주문 상세 저장
- ✅ 배송 정보 입력
- ✅ 결제 방법 선택
- ✅ 주문번호 자동 생성
- ✅ 재고 관리 (재고 감소)
- ✅ 주문 완료 페이지
- ❌ 주문 조회 (회원용)
- ❌ 주문 취소
- ❌ 배송 추적

---

### 4. 결제 시스템

#### 지원 PG사
- **NHN KCP**: 신용카드, 계좌이체, 가상계좌 등
- **KG이니시스**: 신용카드, 계좌이체, 가상계좌 등
- **토스페이먼츠**: 신용카드, 계좌이체, 가상계좌, 간편결제 등
- **무통장입금**: 별도 PG 연동 없이 주문만 생성

#### 구현 파일
- **설정 파일**: `payment/config.php`
  - PG사 API 키 설정
  - 테스트 모드 설정
  - 성공/실패 URL 설정

- **결제 처리 라우터**: `payment/process.php`
  - 주문 정보 조회
  - 선택한 PG사로 라우팅
  - 주문 테이블 자동 생성

- **KCP 연동**: `payment/kcp.php`
  - KCP 결제 페이지 표시
  - iframe 통합
  - 테스트 모드 지원

- **이니시스 연동**: `payment/inicis.php`
  - 이니시스 결제 페이지 표시
  - iframe 통합
  - 테스트 모드 지원

- **토스페이먼츠 연동**: `payment/toss.php`
  - 토스페이먼츠 결제 요청
  - JavaScript SDK 사용
  - iframe 통합
  - 테스트 모드 지원

- **결제 성공 처리**: `payment/success.php`
- **결제 실패 처리**: `payment/fail.php`

#### 결제 흐름
1. 사용자가 `order.php`에서 주문 정보 입력
2. 결제 방법 선택 (무통장입금, KCP, 이니시스, 토스페이먼츠)
3. "주문하기" 버튼 클릭
4. 주문 생성 (`g5_shop_order`, `g5_shop_order_item`)
5. PG 결제 선택 시 `payment/process.php`로 이동
6. 선택한 PG사 결제 페이지로 리다이렉트 (iframe)
7. 결제 완료 후 `order_complete.php`로 이동

#### 테스트 모드
- API 키가 없어도 주문 플로우 테스트 가능
- "테스트 완료" 버튼으로 주문 완료 페이지 이동
- 실제 결제는 진행되지 않음

#### 기능 상태
- ✅ 무통장입금 주문
- ✅ KCP 연동 (iframe)
- ✅ 이니시스 연동 (iframe)
- ✅ 토스페이먼츠 연동 (iframe)
- ✅ 테스트 모드 지원
- ✅ 주문 생성 및 저장
- ❌ 실제 결제 승인 (API 키 필요)
- ❌ 결제 취소
- ❌ 환불 처리

---

### 5. 관리자 GUI 시스템

#### 구현 파일
- **관리자 페이지**: `admin_view_data.php`
  - GUI 기반 데이터 관리
  - 회원, 게시판, 게시글, 상품, 카테고리 CRUD
  - 다국어 지원
  - 관리자 권한 확인 (`mb_level >= 10`)

#### 관리 가능한 테이블
1. **회원 관리** (`g5_member`)
   - 회원 목록 조회
   - 회원 추가/수정/삭제
   - 비밀번호 자동 해싱
   - 권한 레벨 설정

2. **게시판 관리** (`g5_board`)
   - 게시판 목록 조회
   - 게시판 추가/수정/삭제
   - 권한 설정

3. **게시글 관리** (`g5_write_free`)
   - 게시글 목록 조회
   - 게시글 추가/수정/삭제

4. **상품 관리** (`g5_shop_item`)
   - 상품 목록 조회
   - 상품 추가/수정/삭제
   - 상품 이미지 표시 (썸네일)
   - 재고 관리

5. **카테고리 관리** (`g5_shop_category`)
   - 카테고리 목록 조회
   - 카테고리 추가/수정/삭제

#### 기능 상태
- ✅ 회원 CRUD
- ✅ 게시판 CRUD
- ✅ 게시글 CRUD
- ✅ 상품 CRUD
- ✅ 카테고리 CRUD
- ✅ 관리자 권한 확인
- ✅ 비밀번호 자동 해싱
- ✅ 이미지 표시 (상품)
- ✅ 다국어 지원
- ❌ 주문 관리 (미구현)
- ❌ 파일 업로드 (미구현)
- ❌ 대량 작업 (미구현)

---

## 기능 비교표

| 기능 | Gnuboard | YoungCart | 구현 상태 |
|------|----------|-----------|-----------|
| **회원 관리** | ✅ | - | 부분 구현 |
| **로그인/인증** | ✅ | - | ✅ 완료 |
| **게시판** | ✅ | - | 부분 구현 |
| **게시글 조회** | ✅ | - | ✅ 완료 |
| **게시글 작성** | ✅ | - | ❌ 미구현 |
| **파일 관리** | ✅ | - | ❌ 미구현 |
| **상품 관리** | - | ✅ | 부분 구현 |
| **상품 목록** | - | ✅ | ✅ 완료 |
| **상품 상세** | - | ✅ | ✅ 완료 |
| **카테고리** | - | ✅ | 부분 구현 |
| **장바구니** | - | ✅ | ❌ 미구현 |
| **주문 관리** | - | ✅ | ✅ 완료 |
| **결제 시스템** | - | ✅ | ✅ 완료 |
| **관리자 GUI** | ✅ | - | ✅ 완료 |

---

## 실제 사용 현황 요약

### Gnuboard 기능 (현재 사용 중)

1. **회원 관리**
   - ✅ 회원 테이블 (`g5_member`)
   - ✅ 로그인 기능 (`login.php`)
   - ✅ 세션 기반 인증

2. **게시판 시스템**
   - ✅ 게시판 설정 테이블 (`g5_board`)
   - ✅ 게시글 테이블 (`g5_write_free`)
   - ✅ 게시글 목록 조회 (`board.php`)
   - ✅ 메인 페이지 뉴스 표시 (`index.php`)

3. **데이터베이스 레이어**
   - ✅ PDO 기반 헬퍼 함수
   - ✅ SQLite 연결
   - ✅ Prepared Statements

4. **세션 관리**
   - ✅ 언어 설정 저장
   - ✅ 로그인 상태 관리

### YoungCart 기능 (현재 사용 중)

1. **상품 관리**
   - ✅ 상품 테이블 (`g5_shop_item`)
   - ✅ 상품 목록 조회 (`shop.php`)
   - ✅ 상품 정보 표시

2. **카테고리**
   - ✅ 카테고리 테이블 (`g5_shop_category`)
   - ❌ 실제 사용 (미구현)

---

## 미구현 기능

### Gnuboard 미구현 기능
- 회원 가입
- 회원 정보 수정
- 비밀번호 찾기
- 게시글 작성/수정/삭제
- 댓글 기능
- 파일 업로드/다운로드
- 관리자 페이지

### YoungCart 미구현 기능
- 카테고리별 필터링
- 장바구니
- 배송 관리
- 쿠폰 시스템
- 포인트 시스템
- 상품 리뷰

---

## 파일별 기능 사용 현황

### index.php
- **Gnuboard**: 게시글 조회 (`g5_write_free`)
- **YoungCart**: 없음
- **기타**: 다국어 지원, 세션 관리

### board.php
- **Gnuboard**: 게시판 조회 (`g5_board`), 게시글 조회 (`g5_write_free`)
- **YoungCart**: 없음
- **기타**: 페이지네이션, 다국어 지원

### shop.php
- **Gnuboard**: 없음
- **YoungCart**: 상품 조회 (`g5_shop_item`)
- **기타**: 다국어 지원, 상품 그리드 레이아웃

### product_detail.php
- **Gnuboard**: 없음
- **YoungCart**: 상품 상세 조회 (`g5_shop_item`), 조회수 증가
- **기타**: 다국어 지원, 이미지 갤러리, 바로 구매

### order.php
- **Gnuboard**: 없음
- **YoungCart**: 주문 생성 (`g5_shop_order`, `g5_shop_order_item`), 재고 관리
- **기타**: 다국어 지원, 배송 정보 입력, 결제 방법 선택

### payment/process.php, payment/kcp.php, payment/inicis.php, payment/toss.php
- **Gnuboard**: 없음
- **YoungCart**: 결제 처리, PG 연동
- **기타**: iframe 통합, 테스트 모드

### admin_view_data.php
- **Gnuboard**: 회원 관리 (`g5_member`), 게시판 관리 (`g5_board`), 게시글 관리 (`g5_write_free`)
- **YoungCart**: 상품 관리 (`g5_shop_item`), 카테고리 관리 (`g5_shop_category`)
- **기타**: GUI 기반 CRUD, 관리자 권한 확인, 다국어 지원

### login.php
- **Gnuboard**: 회원 조회 (`g5_member`), 인증
- **YoungCart**: 없음
- **기타**: 다국어 지원, 세션 관리

### data/dbconfig.php
- **Gnuboard**: 데이터베이스 추상화 레이어
- **YoungCart**: 없음 (공통 사용)
- **기타**: PDO 연결, 헬퍼 함수

---

## 데이터베이스 테이블 분류

### Gnuboard 테이블 (g5_ 접두사)
- `g5_member`: 회원 정보
- `g5_board`: 게시판 설정
- `g5_write_free`: 게시글
- `g5_board_file`: 첨부 파일

### YoungCart 테이블 (g5_shop_ 접두사)
- `g5_shop_item`: 상품 정보
- `g5_shop_category`: 상품 카테고리

---

## 통합 사용 사례

### 공통 사용
- **데이터베이스 연결**: `g5_get_db()` (Gnuboard 제공, YoungCart도 사용)
- **헬퍼 함수**: `g5_fetch()`, `g5_fetch_all()` (공통 사용)
- **세션 관리**: 언어 설정, 로그인 상태 (Gnuboard 기반)

### 독립 사용
- **Gnuboard**: 회원 관리, 게시판은 독립적으로 작동
- **YoungCart**: 상품 관리는 독립적으로 작동

### 향후 통합 가능성
- 회원 정보를 상품 주문에 연결
- 게시판에 상품 리뷰 기능 추가
- 회원 등급에 따른 상품 할인

---

## 작성일
2025년 12월 29일

