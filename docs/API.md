# API 문서

## 개요

이 문서는 프로젝트에서 사용하는 주요 함수와 API에 대한 설명을 제공합니다.

---

## 데이터베이스 함수

### g5_get_db()

**설명**: SQLite 데이터베이스 연결을 반환합니다 (싱글톤 패턴).

**위치**: `data/dbconfig.php`

**반환값**: `PDO` 객체

**예시**:
```php
$db = g5_get_db();
```

**특징**:
- 싱글톤 패턴으로 한 번만 연결
- 자동 예외 처리
- FETCH_ASSOC 모드 기본 설정

---

### g5_query($sql, $params = [])

**설명**: SQL 쿼리를 실행하고 PDOStatement를 반환합니다.

**파라미터**:
- `$sql` (string): 실행할 SQL 쿼리
- `$params` (array): 바인딩할 파라미터 배열 (기본값: 빈 배열)

**반환값**: `PDOStatement` 객체

**예시**:
```php
// SELECT 쿼리
$stmt = g5_query("SELECT * FROM g5_member WHERE mb_id = ?", ['admin']);

// INSERT 쿼리
g5_query("INSERT INTO g5_member (mb_id, mb_name) VALUES (?, ?)", ['user1', '홍길동']);

// UPDATE 쿼리
g5_query("UPDATE g5_member SET mb_name = ? WHERE mb_id = ?", ['김철수', 'user1']);
```

**보안**: Prepared Statements 사용으로 SQL Injection 방지

---

### g5_fetch($sql, $params = [])

**설명**: 단일 행을 조회하여 연관 배열로 반환합니다.

**파라미터**:
- `$sql` (string): 실행할 SQL 쿼리
- `$params` (array): 바인딩할 파라미터 배열 (기본값: 빈 배열)

**반환값**: `array|false` - 조회된 행 또는 false (결과 없음)

**예시**:
```php
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", ['admin']);
if ($member) {
    echo $member['mb_name'];
}
```

---

### g5_fetch_all($sql, $params = [])

**설명**: 여러 행을 조회하여 배열로 반환합니다.

**파라미터**:
- `$sql` (string): 실행할 SQL 쿼리
- `$params` (array): 바인딩할 파라미터 배열 (기본값: 빈 배열)

**반환값**: `array` - 조회된 행들의 배열

**예시**:
```php
$members = g5_fetch_all("SELECT * FROM g5_member ORDER BY mb_regdate DESC");
foreach ($members as $member) {
    echo $member['mb_name'] . "\n";
}
```

---

## 세션 함수

### 세션 시작

**함수**: `session_start()`

**위치**: 각 페이지 상단

**용도**: 세션 초기화

**예시**:
```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

---

### 언어 설정

**변수**: `$_SESSION['lang']`

**값**: `'ko'` 또는 `'en'`

**설정**:
```php
$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ko');
$_SESSION['lang'] = $lang;
```

---

## 다국어 함수

### 텍스트 배열 구조

**형식**:
```php
$text = [
    'ko' => [
        'key1' => '한국어 텍스트',
        'key2' => '한국어 텍스트2'
    ],
    'en' => [
        'key1' => 'English text',
        'key2' => 'English text2'
    ]
];
```

**사용**:
```php
$lang = $_SESSION['lang'] ?? 'ko';
$t = $text[$lang] ?? $text['ko'];
echo $t['key1'];
```

---

## 인증 함수

### 비밀번호 해싱

**함수**: `password_hash()`

**예시**:
```php
$hashed = password_hash($password, PASSWORD_DEFAULT);
```

---

### 비밀번호 검증

**함수**: `password_verify()`

**예시**:
```php
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", [$mb_id]);
if ($member && password_verify($password, $member['mb_password'])) {
    // 로그인 성공
}
```

---

## 출력 함수

### XSS 방지 출력

**함수**: `htmlspecialchars()`

**예시**:
```php
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

**권장 사용**:
```php
// 안전한 출력
echo htmlspecialchars(isset($t['title']) ? $t['title'] : 'Default');

// 또는 null 체크
$title = $t['title'] ?? 'Default';
echo htmlspecialchars($title);
```

---

## 페이지 구조

### 헤더 포함

**위치**: 각 페이지 상단

**예시**:
```php
<?php include 'theme/pumae/header.php'; ?>
```

---

### 푸터 포함

**위치**: 각 페이지 하단

**예시**:
```php
<?php include 'theme/pumae/footer.php'; ?>
```

---

## 주문 테이블 자동 생성

### ensure_order_tables()

**설명**: 주문 테이블(`g5_shop_order`, `g5_shop_order_item`)이 없으면 자동으로 생성합니다.

**위치**: `auto_create_tables.php`

**반환값**: `bool` - 성공 시 true, 실패 시 false

**예시**:
```php
require_once 'auto_create_tables.php';
ensure_order_tables();
```

**특징**:
- 테이블 존재 여부 확인 후 생성
- `order.php`와 `payment/process.php`에서 자동 호출
- 에러 발생 시 `error_log()`로 기록

---

## 데이터베이스 테이블

### 주요 테이블 목록

1. **g5_member**: 회원 정보
2. **g5_board**: 게시판 설정
3. **g5_write_free**: 게시글
4. **g5_board_file**: 첨부 파일
5. **g5_shop_item**: 상품 정보
6. **g5_shop_category**: 상품 카테고리
7. **g5_shop_order**: 주문 정보
8. **g5_shop_order_item**: 주문 상세 정보

---

## 쿼리 예시

### 회원 조회

```php
// 단일 회원
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", [$mb_id]);

// 모든 회원
$members = g5_fetch_all("SELECT * FROM g5_member ORDER BY mb_regdate DESC");
```

---

### 게시글 조회

```php
// 최신 게시글 10개
$posts = g5_fetch_all(
    "SELECT * FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 10"
);

// 특정 게시글
$post = g5_fetch("SELECT * FROM g5_write_free WHERE wr_id = ?", [$wr_id]);
```

---

### 상품 조회

```php
// 판매 중인 상품 목록
$items = g5_fetch_all(
    "SELECT * FROM g5_shop_item WHERE it_use = 1 AND it_sell_use = 1 ORDER BY it_time DESC"
);

// 특정 상품
$item = g5_fetch("SELECT * FROM g5_shop_item WHERE it_id = ?", [$it_id]);

// 조회수 증가
g5_query("UPDATE g5_shop_item SET it_hit = it_hit + 1 WHERE it_id = ?", [$it_id]);
```

---

### 주문 조회

```php
// 특정 주문 조회
$order = g5_fetch("SELECT * FROM g5_shop_order WHERE od_no = ?", [$od_no]);

// 주문 상세 조회
$order_items = g5_fetch_all(
    "SELECT * FROM g5_shop_order_item WHERE od_no = ?", 
    [$od_no]
);

// 회원의 주문 목록
$orders = g5_fetch_all(
    "SELECT * FROM g5_shop_order WHERE mb_id = ? ORDER BY od_time DESC",
    [$mb_id]
);
```

---

### 주문 생성

```php
// 주문번호 생성
$od_no = 'ORD' . date('YmdHis') . rand(1000, 9999);

// 주문 생성
g5_query(
    "INSERT INTO g5_shop_order (od_no, od_name, od_email, od_tel, od_addr1, od_addr2, od_settle_case, od_cart_price, od_send_cost, od_receipt_price) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    [$od_no, $name, $email, $tel, $addr1, $addr2, $settle_case, $cart_price, $send_cost, $receipt_price]
);

// 주문 ID 가져오기
$order = g5_fetch("SELECT od_id FROM g5_shop_order WHERE od_no = ?", [$od_no]);
$od_id = $order['od_id'];

// 주문 상세 생성
g5_query(
    "INSERT INTO g5_shop_order_item (od_id, od_no, it_id, it_name, it_price, ct_qty, ct_price, it_img1) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
    [$od_id, $od_no, $it_id, $it_name, $it_price, $qty, $ct_price, $it_img1]
);

// 재고 감소
if ($product['it_stock_qty'] > 0) {
    $new_stock = max(0, $product['it_stock_qty'] - $qty);
    g5_query("UPDATE g5_shop_item SET it_stock_qty = ? WHERE it_id = ?", [$new_stock, $it_id]);
}
```

---

## 에러 처리

### 데이터베이스 연결 에러

**위치**: `data/dbconfig.php`

**처리**:
```php
try {
    $db = new PDO('sqlite:' . G5_DB_PATH);
    // ...
} catch (PDOException $e) {
    die('데이터베이스 연결 실패: ' . $e->getMessage());
}
```

---

### 쿼리 실행 에러

**PDO 예외 모드**:
```php
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

---

## 상수

### G5_DB_TYPE

**값**: `'sqlite'`  
**용도**: 데이터베이스 타입 정의

---

### G5_DB_PATH

**값**: `__DIR__ . '/db/gnuboard.db'`  
**용도**: 데이터베이스 파일 경로

---

### G5_SITE_NAME

**값**: 사이트 이름  
**용도**: 사이트 설정

---

## 작성일
2025년 12월 29일

