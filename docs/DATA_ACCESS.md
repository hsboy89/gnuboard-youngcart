# 데이터 조회 방법 가이드

## 개요

이 문서는 프로젝트에서 데이터를 조회하는 다양한 방법을 설명합니다.

---

## 방법 1: 관리자 페이지 (권장)

### 접속 방법
```
http://localhost:8000/admin_view_data.php
```

### 기능
- ✅ 테이블별 데이터 조회
- ✅ 데이터 추가/수정/삭제 (GUI)
- ✅ 이미지 표시 (상품)
- ✅ 관리자 권한 필요 (mb_level >= 10)

### 사용 가능한 테이블
- `g5_member`: 회원 정보 (CRUD)
- `g5_board`: 게시판 설정 (CRUD)
- `g5_write_free`: 게시글 (CRUD)
- `g5_shop_item`: 상품 (CRUD, 이미지 표시)
- `g5_shop_category`: 상품 카테고리 (CRUD)

### 사용 방법
1. 관리자 계정으로 로그인
2. 헤더 메뉴에서 "관리" 클릭
3. 상단 탭에서 테이블 선택
4. 목록에서 "추가", "수정", "삭제" 버튼 사용

**자세한 내용**: [ADMIN_PANEL.md](./ADMIN_PANEL.md) 참조

---

## 방법 2: PHP 코드에서 조회

### 기본 사용법

**파일**: `data/dbconfig.php`에 정의된 함수 사용

```php
// 설정 파일 로드
require_once 'data/dbconfig.php';

// 단일 행 조회
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", ['admin']);

// 여러 행 조회
$members = g5_fetch_all("SELECT * FROM g5_member ORDER BY mb_regdate DESC");

// 쿼리 실행 (UPDATE, INSERT 등)
g5_query("UPDATE g5_member SET mb_name = ? WHERE mb_id = ?", ['홍길동', 'admin']);
```

### 실제 사용 예시

**회원 조회** (`login.php`):
```php
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", [$mb_id]);
```

**게시글 조회** (`board.php`):
```php
$posts = g5_fetch_all("SELECT * FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 10");
```

**상품 조회** (`shop.php`):
```php
$items = g5_fetch_all(
    "SELECT * FROM g5_shop_item WHERE it_use = 1 AND it_sell_use = 1 ORDER BY it_time DESC"
);
```

---

## 방법 3: SQLite 데이터베이스 직접 접근

### 데이터베이스 파일 위치
```
data/db/gnuboard.db
```

### SQLite 도구 사용

#### 1. SQLite Browser (DB Browser for SQLite)
- 다운로드: https://sqlitebrowser.org/
- 파일 열기: `data/db/gnuboard.db`
- GUI로 데이터 조회/수정 가능

#### 2. 명령줄 (SQLite CLI)

**PowerShell에서 실행**:
```powershell
# SQLite3가 설치되어 있다면
sqlite3 data/db/gnuboard.db

# 또는 PHP로 실행
C:\php\php.exe -r "require 'data/dbconfig.php'; \$db = g5_get_db(); \$result = \$db->query('SELECT * FROM g5_member'); print_r(\$result->fetchAll());"
```

#### 3. VS Code 확장
- "SQLite Viewer" 확장 설치
- `gnuboard.db` 파일 우클릭 → "Open Database"

---

## 방법 4: PHP 스크립트로 조회

### 간단한 조회 스크립트 생성

**파일**: `query_data.php`

```php
<?php
require_once 'data/dbconfig.php';

// 회원 조회
echo "=== 회원 목록 ===\n";
$members = g5_fetch_all("SELECT mb_id, mb_name, mb_email FROM g5_member");
foreach ($members as $member) {
    echo "ID: {$member['mb_id']}, 이름: {$member['mb_name']}, 이메일: {$member['mb_email']}\n";
}

// 게시글 조회
echo "\n=== 최신 게시글 ===\n";
$posts = g5_fetch_all("SELECT wr_id, wr_subject, wr_datetime FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 5");
foreach ($posts as $post) {
    echo "제목: {$post['wr_subject']}, 날짜: {$post['wr_datetime']}\n";
}

// 상품 조회
echo "\n=== 상품 목록 ===\n";
$items = g5_fetch_all("SELECT it_name, it_price FROM g5_shop_item WHERE it_use = 1");
foreach ($items as $item) {
    echo "상품: {$item['it_name']}, 가격: {$item['it_price']}원\n";
}
?>
```

**실행**:
```bash
C:\php\php.exe query_data.php
```

---

## 주요 테이블 및 조회 예시

### 1. 회원 테이블 (g5_member)

**모든 회원 조회**:
```php
$members = g5_fetch_all("SELECT * FROM g5_member");
```

**특정 회원 조회**:
```php
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", ['admin']);
```

**회원 수 조회**:
```php
$count = g5_fetch("SELECT COUNT(*) as cnt FROM g5_member")['cnt'];
```

---

### 2. 게시판 테이블 (g5_board)

**모든 게시판 조회**:
```php
$boards = g5_fetch_all("SELECT * FROM g5_board");
```

**특정 게시판 조회**:
```php
$board = g5_fetch("SELECT * FROM g5_board WHERE bo_table = ?", ['news']);
```

---

### 3. 게시글 테이블 (g5_write_free)

**최신 게시글 조회**:
```php
$posts = g5_fetch_all(
    "SELECT * FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 10"
);
```

**조회수 높은 게시글**:
```php
$posts = g5_fetch_all(
    "SELECT * FROM g5_write_free ORDER BY wr_hit DESC LIMIT 10"
);
```

**특정 게시글 조회**:
```php
$post = g5_fetch("SELECT * FROM g5_write_free WHERE wr_id = ?", [1]);
```

---

### 4. 상품 테이블 (g5_shop_item)

**판매 중인 상품 조회**:
```php
$items = g5_fetch_all(
    "SELECT * FROM g5_shop_item 
     WHERE it_use = 1 AND it_sell_use = 1 
     ORDER BY it_time DESC"
);
```

**가격순 정렬**:
```php
$items = g5_fetch_all(
    "SELECT * FROM g5_shop_item 
     WHERE it_use = 1 
     ORDER BY it_price ASC"
);
```

**특정 상품 조회**:
```php
$item = g5_fetch("SELECT * FROM g5_shop_item WHERE it_id = ?", [1]);
```

**재고 업데이트**:
```php
g5_query("UPDATE g5_shop_item SET it_stock_qty = ? WHERE it_id = ?", [10, 1]);
```

---

### 5. 주문 테이블 (g5_shop_order)

**주문 조회**:
```php
$order = g5_fetch("SELECT * FROM g5_shop_order WHERE od_no = ?", ['ORD202512291430251234']);
```

**최근 주문 목록**:
```php
$orders = g5_fetch_all(
    "SELECT * FROM g5_shop_order ORDER BY od_time DESC LIMIT 10"
);
```

**회원의 주문 목록**:
```php
$orders = g5_fetch_all(
    "SELECT * FROM g5_shop_order WHERE mb_id = ? ORDER BY od_time DESC",
    ['user1']
);
```

---

### 6. 주문 상세 테이블 (g5_shop_order_item)

**주문 상세 조회**:
```php
$order_items = g5_fetch_all(
    "SELECT * FROM g5_shop_order_item WHERE od_no = ?",
    ['ORD202512291430251234']
);
```

**주문별 상품 목록**:
```php
$items = g5_fetch_all(
    "SELECT oi.*, i.it_img1 
     FROM g5_shop_order_item oi 
     LEFT JOIN g5_shop_item i ON oi.it_id = i.it_id 
     WHERE oi.od_no = ?",
    ['ORD202512291430251234']
);
```

---

## 헬퍼 함수 설명

### g5_get_db()
데이터베이스 연결 객체 반환 (PDO)

```php
$db = g5_get_db();
$stmt = $db->query("SELECT * FROM g5_member");
```

### g5_query($sql, $params = [])
쿼리 실행 (UPDATE, INSERT, DELETE 등)

```php
g5_query("INSERT INTO g5_member (mb_id, mb_name) VALUES (?, ?)", ['user1', '홍길동']);
```

### g5_fetch($sql, $params = [])
단일 행 조회

```php
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", ['admin']);
```

### g5_fetch_all($sql, $params = [])
여러 행 조회

```php
$members = g5_fetch_all("SELECT * FROM g5_member");
```

---

## 보안 주의사항

### ✅ 안전한 방법
- Prepared Statements 사용
- 파라미터 바인딩
- 관리자 권한 확인

### ❌ 위험한 방법
- 직접 SQL 문자열 연결
- 사용자 입력을 그대로 쿼리에 사용
- 관리자 권한 없이 데이터 수정

### 예시

**❌ 나쁜 예**:
```php
$id = $_GET['id'];
$sql = "SELECT * FROM g5_member WHERE mb_id = '$id'"; // SQL Injection 위험!
```

**✅ 좋은 예**:
```php
$id = $_GET['id'];
$member = g5_fetch("SELECT * FROM g5_member WHERE mb_id = ?", [$id]); // 안전!
```

---

## 데이터 수정 방법

### INSERT (추가)

```php
g5_query(
    "INSERT INTO g5_member (mb_id, mb_password, mb_name, mb_email, mb_level) 
     VALUES (?, ?, ?, ?, ?)",
    ['user2', password_hash('password123', PASSWORD_DEFAULT), '김철수', 'kim@example.com', 1]
);
```

### UPDATE (수정)

```php
g5_query(
    "UPDATE g5_member SET mb_name = ? WHERE mb_id = ?",
    ['김영수', 'user2']
);
```

### DELETE (삭제)

```php
g5_query("DELETE FROM g5_member WHERE mb_id = ?", ['user2']);
```

---

## 유용한 쿼리 예시

### 통계 조회

**회원 수**:
```php
$count = g5_fetch("SELECT COUNT(*) as cnt FROM g5_member")['cnt'];
```

**게시글 수**:
```php
$count = g5_fetch("SELECT COUNT(*) as cnt FROM g5_write_free")['cnt'];
```

**상품 수**:
```php
$count = g5_fetch("SELECT COUNT(*) as cnt FROM g5_shop_item WHERE it_use = 1")['cnt'];
```

**주문 수**:
```php
$count = g5_fetch("SELECT COUNT(*) as cnt FROM g5_shop_order")['cnt'];
```

**총 매출**:
```php
$total = g5_fetch("SELECT SUM(od_receipt_price) as total FROM g5_shop_order WHERE od_status = '결제완료'")['total'];
```

### 최근 데이터

**최근 가입 회원**:
```php
$members = g5_fetch_all(
    "SELECT * FROM g5_member ORDER BY mb_regdate DESC LIMIT 5"
);
```

**최근 게시글**:
```php
$posts = g5_fetch_all(
    "SELECT * FROM g5_write_free ORDER BY wr_datetime DESC LIMIT 5"
);
```

**최근 주문**:
```php
$orders = g5_fetch_all(
    "SELECT * FROM g5_shop_order ORDER BY od_time DESC LIMIT 5"
);
```

---

## 작성일
2025년 12월 29일

