# 관리자 패널 문서

## 개요

이 문서는 프로젝트의 관리자 GUI 기반 데이터 관리 시스템에 대한 상세 정보를 제공합니다.

---

## 접근 권한

### 관리자 권한
- `mb_level >= 10`인 회원만 접근 가능
- 일반 사용자 (`mb_level = 1`)는 접근 불가

### 권한 확인
```php
$is_admin = isset($_SESSION['mb_level']) && $_SESSION['mb_level'] >= 10;
if (!$is_admin) {
    die('관리자만 접근할 수 있습니다.');
}
```

---

## 관리 가능한 데이터

### 1. 회원 관리 (`g5_member`)

**기능**:
- 회원 목록 조회
- 회원 추가
- 회원 수정
- 회원 삭제

**특징**:
- 비밀번호 자동 해싱 (`password_hash()`)
- 권한 레벨 설정 (`mb_level`)
- 이메일, 이름 등 기본 정보 관리

**주요 필드**:
- `mb_id`: 회원 ID (UNIQUE)
- `mb_password`: 비밀번호 (해시)
- `mb_name`: 이름
- `mb_email`: 이메일
- `mb_level`: 권한 레벨 (1=일반, 10=관리자)
- `mb_regdate`: 가입일시

---

### 2. 게시판 관리 (`g5_board`)

**기능**:
- 게시판 목록 조회
- 게시판 추가
- 게시판 수정
- 게시판 삭제

**주요 필드**:
- `bo_table`: 게시판 테이블명 (PK)
- `bo_subject`: 게시판 제목
- `bo_skin`: 스킨 이름
- `bo_list_level`: 목록 보기 권한
- `bo_read_level`: 읽기 권한
- `bo_write_level`: 쓰기 권한
- `bo_comment_level`: 댓글 권한
- `bo_page_rows`: 페이지당 게시글 수

---

### 3. 게시글 관리 (`g5_write_free`)

**기능**:
- 게시글 목록 조회
- 게시글 추가
- 게시글 수정
- 게시글 삭제

**주요 필드**:
- `wr_id`: 게시글 ID (PK)
- `wr_subject`: 제목
- `wr_content`: 내용
- `mb_id`: 작성자 ID
- `wr_name`: 작성자 이름
- `wr_datetime`: 작성일시
- `wr_hit`: 조회수
- `wr_notice`: 공지사항 여부 (0/1)
- `wr_secret`: 비밀글 여부 (0/1)

---

### 4. 상품 관리 (`g5_shop_item`)

**기능**:
- 상품 목록 조회
- 상품 추가
- 상품 수정
- 상품 삭제
- 상품 이미지 표시 (썸네일)

**특징**:
- 이미지 URL 표시 (최대 100x100px)
- 재고 수량 관리
- 판매 여부 설정

**주요 필드**:
- `it_id`: 상품 ID (PK)
- `ca_id`: 카테고리 ID
- `it_name`: 상품명
- `it_price`: 판매가
- `it_cust_price`: 정가
- `it_content`: 상품 설명
- `it_img1`, `it_img2`, `it_img3`: 상품 이미지
- `it_use`: 사용 여부 (0/1)
- `it_sell_use`: 판매 여부 (0/1)
- `it_stock_qty`: 재고 수량
- `it_hit`: 조회수

---

### 5. 카테고리 관리 (`g5_shop_category`)

**기능**:
- 카테고리 목록 조회
- 카테고리 추가
- 카테고리 수정
- 카테고리 삭제

**주요 필드**:
- `ca_id`: 카테고리 ID (PK)
- `ca_name`: 카테고리명
- `ca_order`: 정렬 순서
- `ca_use`: 사용 여부 (0/1)

---

## 사용 방법

### 1. 접근
- 관리자 계정으로 로그인
- 헤더 메뉴에서 "관리" 클릭
- 또는 직접 `admin_view_data.php` 접근

### 2. 테이블 선택
- 상단 탭에서 관리할 테이블 선택
- 회원, 게시판, 게시글, 상품, 카테고리 중 선택

### 3. 데이터 조회
- 선택한 테이블의 모든 데이터가 목록으로 표시됨
- 각 행은 수정/삭제 버튼 제공

### 4. 데이터 추가
- "추가" 버튼 클릭
- 폼에 데이터 입력
- "저장" 버튼 클릭

### 5. 데이터 수정
- 목록에서 "수정" 버튼 클릭
- 폼에 기존 데이터 자동 입력
- 수정 후 "저장" 버튼 클릭

### 6. 데이터 삭제
- 목록에서 "삭제" 버튼 클릭
- 확인 메시지 표시
- 확인 시 삭제

---

## 폼 처리

### GET 파라미터
- `table`: 테이블명 (`g5_member`, `g5_board`, `g5_write_free`, `g5_shop_item`, `g5_shop_category`)
- `action`: 동작 (`list`, `add`, `edit`, `delete`)
- `id`: 수정/삭제할 레코드 ID

### POST 파라미터
- `action`: 동작 (`create`, `update`, `delete`)
- 테이블별 필드 값

---

## 특수 처리

### 1. 비밀번호 해싱
```php
if (!empty($data['mb_password'])) {
    $data['mb_password'] = password_hash($data['mb_password'], PASSWORD_DEFAULT);
}
```

### 2. 체크박스 처리
```php
// 체크박스는 체크되지 않으면 전송되지 않으므로 기본값 0 설정
$value = isset($_POST[$field]) ? 1 : 0;
```

### 3. 이미지 표시
```php
if (!empty($value) && filter_var($value, FILTER_VALIDATE_URL)) {
    echo '<img src="' . htmlspecialchars($value) . '" style="max-width: 100px; max-height: 100px;">';
}
```

---

## 다국어 지원

### 지원 언어
- 한국어 (`ko`)
- 영어 (`en`)

### 언어 전환
- URL 파라미터: `?lang=ko` 또는 `?lang=en`
- 세션에 저장되어 유지됨

---

## 보안 고려사항

### 현재 구현
- ✅ 관리자 권한 확인
- ✅ Prepared Statements (SQL Injection 방지)
- ✅ `htmlspecialchars()` 사용 (XSS 방지)
- ✅ 비밀번호 해싱

### 개선 필요 사항
- ⚠️ CSRF 토큰 미구현
- ⚠️ 입력 검증 강화 필요
- ⚠️ 파일 업로드 검증 미구현
- ⚠️ 로그 기록 미구현

---

## 파일 구조

```
admin_view_data.php         # 관리자 데이터 관리 페이지
```

**의존 파일**:
- `data/dbconfig.php`: 데이터베이스 연결
- `theme/pumae/header.php`: 헤더 템플릿
- `theme/pumae/footer.php`: 푸터 템플릿

---

## 사용 예시

### 회원 추가
1. "회원" 탭 선택
2. "추가" 버튼 클릭
3. 회원 정보 입력:
   - ID: `test`
   - 비밀번호: `test123`
   - 이름: `테스트 사용자`
   - 이메일: `test@example.com`
   - 권한 레벨: `1` (일반 사용자)
4. "저장" 버튼 클릭

### 상품 수정
1. "상품" 탭 선택
2. 수정할 상품의 "수정" 버튼 클릭
3. 가격, 재고 등 수정
4. "저장" 버튼 클릭

### 게시글 삭제
1. "게시글" 탭 선택
2. 삭제할 게시글의 "삭제" 버튼 클릭
3. 확인 메시지에서 "확인" 클릭

---

## 향후 개선 사항

### 단기
- 주문 관리 추가
- 파일 업로드 기능
- 대량 작업 (선택 삭제 등)

### 중기
- 검색 기능
- 정렬 기능
- 페이지네이션
- 필터링

### 장기
- 대시보드 (통계)
- 로그 기록
- 권한 세분화
- 작업 내역 추적

---

## 작성일
2025년 12월 29일

