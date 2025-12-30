# 주문 시스템 문서

## 개요

이 문서는 프로젝트의 주문 및 결제 시스템에 대한 상세 정보를 제공합니다.

---

## 주문 프로세스

### 1. 상품 선택
- 사용자가 `shop.php`에서 상품 목록 확인
- 상품 클릭 시 `product_detail.php`로 이동
- 상품 상세 정보 확인

### 2. 주문 정보 입력
- `product_detail.php`에서 "바로 구매" 버튼 클릭
- `order.php`로 이동
- 주문 정보 자동 입력 (상품 정보, 가격)
- 배송 정보 입력 (이름, 이메일, 전화번호, 주소, 메모)
- 수량 선택
- 결제 방법 선택

### 3. 주문 생성
- "주문하기" 버튼 클릭
- 주문 테이블 자동 생성 (없는 경우)
- `g5_shop_order` 테이블에 주문 정보 저장
- `g5_shop_order_item` 테이블에 주문 상세 정보 저장
- 재고 감소 (재고가 있는 경우)

### 4. 결제 처리
- **무통장입금**: 바로 `order_complete.php`로 이동
- **PG 결제**: `payment/process.php`로 이동 → 선택한 PG사 결제 페이지로 리다이렉트

### 5. 주문 완료
- 결제 완료 후 `order_complete.php`로 이동
- 주문번호 표시
- 주문 상품 목록 표시
- 배송 정보 표시
- 결제 상태 표시

---

## 데이터베이스 스키마

### g5_shop_order (주문 테이블)

```sql
CREATE TABLE IF NOT EXISTS g5_shop_order (
    od_id INTEGER PRIMARY KEY AUTOINCREMENT,
    od_no TEXT UNIQUE NOT NULL,
    mb_id TEXT,
    od_name TEXT NOT NULL,
    od_email TEXT NOT NULL,
    od_tel TEXT NOT NULL,
    od_hp TEXT,
    od_zip TEXT,
    od_addr1 TEXT,
    od_addr2 TEXT,
    od_addr3 TEXT,
    od_addr_jibeon TEXT,
    od_memo TEXT,
    od_status TEXT DEFAULT '주문완료',
    od_settle_case TEXT DEFAULT '무통장입금',
    od_receipt_price INTEGER DEFAULT 0,
    od_receipt_point INTEGER DEFAULT 0,
    od_receipt_coupon INTEGER DEFAULT 0,
    od_cart_price INTEGER DEFAULT 0,
    od_send_cost INTEGER DEFAULT 0,
    od_send_coupon INTEGER DEFAULT 0,
    od_send_point INTEGER DEFAULT 0,
    od_receipt_time TEXT,
    od_send_time TEXT,
    od_delivery_company TEXT,
    od_invoice TEXT,
    od_time TEXT DEFAULT (datetime('now')),
    od_update_time TEXT
);
```

**주요 필드 설명**:
- `od_no`: 주문번호 (형식: `ORD{YYYYMMDDHHmmss}{랜덤4자리}`)
- `od_status`: 주문 상태 ('주문완료', '결제완료', '배송중', '배송완료', '취소' 등)
- `od_settle_case`: 결제 방법 ('무통장입금', 'KCP', '이니시스', '토스페이먼츠')
- `od_receipt_price`: 실제 결제 금액
- `od_cart_price`: 상품 총액
- `od_send_cost`: 배송비

### g5_shop_order_item (주문 상세 테이블)

```sql
CREATE TABLE IF NOT EXISTS g5_shop_order_item (
    oi_id INTEGER PRIMARY KEY AUTOINCREMENT,
    od_id INTEGER NOT NULL,
    od_no TEXT NOT NULL,
    it_id INTEGER NOT NULL,
    it_name TEXT NOT NULL,
    it_price INTEGER NOT NULL,
    ct_qty INTEGER NOT NULL,
    ct_price INTEGER NOT NULL,
    it_img1 TEXT,
    FOREIGN KEY (od_id) REFERENCES g5_shop_order(od_id)
);
```

**주요 필드 설명**:
- `od_id`: 주문 ID (FK)
- `od_no`: 주문번호
- `it_id`: 상품 ID
- `it_name`: 상품명 (주문 시점의 상품명 저장)
- `it_price`: 상품 단가 (주문 시점의 가격 저장)
- `ct_qty`: 주문 수량
- `ct_price`: 상품 총액 (`it_price * ct_qty`)

---

## 자동 테이블 생성

### auto_create_tables.php

주문 테이블이 없으면 자동으로 생성하는 함수를 제공합니다.

**함수**: `ensure_order_tables()`

**호출 위치**:
- `order.php`: 주문 페이지 접근 시
- `payment/process.php`: 결제 처리 시

**동작**:
1. `g5_shop_order` 테이블 존재 여부 확인
2. 없으면 테이블 생성
3. `g5_shop_order_item` 테이블 존재 여부 확인
4. 없으면 테이블 생성

**장점**:
- 수동으로 테이블을 생성할 필요 없음
- 주문 기능 사용 시 자동으로 준비됨

---

## 주문번호 생성

### 형식
```
ORD{YYYYMMDDHHmmss}{랜덤4자리}
```

**예시**: `ORD202512291430251234`

### 생성 로직
```php
$od_no = 'ORD' . date('YmdHis') . rand(1000, 9999);
```

**특징**:
- 날짜/시간 기반 (중복 가능성 낮음)
- 랜덤 4자리 추가 (동시 주문 시 중복 방지)
- UNIQUE 제약조건으로 중복 방지

---

## 재고 관리

### 재고 감소 로직

```php
// 재고가 있는 경우에만 감소
if ($product['it_stock_qty'] > 0) {
    $new_stock = max(0, $product['it_stock_qty'] - $qty);
    g5_query("UPDATE g5_shop_item SET it_stock_qty = ? WHERE it_id = ?", 
             [$new_stock, $it_id]);
}
```

**특징**:
- 재고가 0 이하인 경우 감소하지 않음
- 재고가 없는 상품도 주문 가능 (경고 메시지 표시)
- `max(0, ...)` 사용으로 음수 방지

---

## 결제 방법

### 1. 무통장입금
- 별도 PG 연동 없이 주문만 생성
- `od_settle_case` = '무통장입금'
- `od_status` = '주문완료'
- 바로 `order_complete.php`로 이동

### 2. NHN KCP
- `payment/process.php` → `payment/kcp.php`
- iframe으로 결제 페이지 표시
- API 키가 없으면 테스트 모드
- 결제 완료 후 `order_complete.php`로 이동

### 3. KG이니시스
- `payment/process.php` → `payment/inicis.php`
- iframe으로 결제 페이지 표시
- API 키가 없으면 테스트 모드
- 결제 완료 후 `order_complete.php`로 이동

### 4. 토스페이먼츠
- `payment/process.php` → `payment/toss.php`
- JavaScript SDK 사용
- iframe으로 결제 페이지 표시
- API 키가 없으면 테스트 모드
- 결제 완료 후 `order_complete.php`로 이동

---

## 파일 구조

```
order.php                    # 주문/결제 페이지
order_complete.php          # 주문 완료 페이지
auto_create_tables.php      # 주문 테이블 자동 생성
payment/
  ├── config.php           # PG사 설정
  ├── process.php          # 결제 처리 라우터
  ├── kcp.php             # KCP 연동
  ├── inicis.php          # 이니시스 연동
  ├── toss.php            # 토스페이먼츠 연동
  ├── success.php         # 결제 성공 처리
  └── fail.php            # 결제 실패 처리
```

---

## 보안 고려사항

### 현재 구현
- ✅ Prepared Statements (SQL Injection 방지)
- ✅ `htmlspecialchars()` 사용 (XSS 방지)
- ✅ 주문번호 UNIQUE 제약조건 (중복 방지)

### 개선 필요 사항
- ⚠️ CSRF 토큰 미구현
- ⚠️ 주문 금액 검증 미구현
- ⚠️ 재고 동시성 제어 미구현 (트랜잭션 필요)
- ⚠️ 주문 취소 기능 미구현
- ⚠️ 환불 처리 미구현

---

## 테스트 방법

### 1. 주문 생성 테스트
1. `shop.php`에서 상품 선택
2. `product_detail.php`에서 "바로 구매" 클릭
3. `order.php`에서 배송 정보 입력
4. "주문하기" 버튼 클릭
5. 주문 완료 페이지 확인

### 2. 무통장입금 테스트
1. 결제 방법: "무통장입금" 선택
2. "주문하기" 버튼 클릭
3. 바로 주문 완료 페이지로 이동 확인

### 3. PG 결제 테스트 (테스트 모드)
1. 결제 방법: "KCP" / "이니시스" / "토스페이먼츠" 선택
2. "주문하기" 버튼 클릭
3. 테스트 모드 메시지 확인
4. "테스트 완료" 버튼 클릭
5. 주문 완료 페이지로 이동 확인

### 4. 재고 관리 테스트
1. 재고가 있는 상품 주문
2. 주문 후 재고 감소 확인
3. 재고가 0인 상품 주문
4. 경고 메시지 표시 확인

---

## 향후 개선 사항

### 단기
- 주문 조회 기능 (회원용)
- 주문 취소 기능
- 주문 상태 변경 (관리자)

### 중기
- 장바구니 기능
- 쿠폰 시스템
- 포인트 시스템
- 배송 추적

### 장기
- 주문 알림 (이메일/SMS)
- 자동 환불 처리
- 주문 통계 대시보드

---

## 작성일
2025년 12월 29일

