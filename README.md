# Gnuboard + YoungCart 설치 가이드

다무상회 스타일의 웹사이트를 구축하기 위한 Gnuboard + YoungCart 설치 및 설정 가이드입니다.

## 시스템 요구사항

- PHP 7.4 이상
- PDO SQLite 확장 활성화
- 웹 서버 (Apache, Nginx 등)

## 설치 방법

### 1. 파일 준비

프로젝트 파일을 웹 서버의 문서 루트 디렉토리에 업로드합니다.

### 2. 설치 스크립트 실행

브라우저에서 `install.php` 파일에 접속합니다:

```
http://localhost/install.php
```

### 3. 설치 정보 입력

설치 화면에서 다음 정보를 입력합니다:

- **데이터베이스 경로**: 기본값 `data/db/gnuboard.db` (변경 가능)
- **사이트 이름**: 웹사이트 이름
- **관리자 ID**: 관리자 계정 ID
- **관리자 비밀번호**: 관리자 계정 비밀번호
- **관리자 이메일**: 관리자 이메일 주소

### 4. 설치 완료

설치가 완료되면 자동으로 메인 페이지로 이동합니다.

## 디렉토리 구조

```
.
├── data/                  # 데이터 디렉토리
│   ├── db/               # SQLite 데이터베이스 파일
│   ├── file/             # 업로드된 파일
│   └── dbconfig.php      # 데이터베이스 설정 파일
├── theme/                # 테마 디렉토리
│   └── pumae/           # 다무상회 스타일 테마
│       ├── css/         # 스타일시트
│       ├── js/          # JavaScript 파일
│       ├── images/      # 이미지 파일
│       ├── header.php   # 헤더 템플릿
│       └── footer.php   # 푸터 템플릿
├── install/              # 설치 관련 파일
│   └── schema.sql       # 데이터베이스 스키마
├── index.php            # 메인 페이지
├── shop.php             # 쇼핑몰 페이지
└── install.php          # 설치 스크립트
```

## 주요 기능

### 1. 다국어 지원
- 한국어(KR) / 영어(EN) 지원
- 언어 전환 기능

### 2. 게시판 시스템
- 뉴스 게시판
- 리뷰 게시판
- 정보 게시판

### 3. 쇼핑몰 (YoungCart)
- 상품 등록 및 관리
- 상품 목록 표시
- 상품 상세 페이지

### 4. 반응형 디자인
- 모바일 친화적 레이아웃
- 다무상회 스타일 적용

## 데이터베이스

이 프로젝트는 SQLite를 사용합니다. 데이터베이스 파일은 `data/db/gnuboard.db`에 저장됩니다.

### 주요 테이블

- `g5_member`: 회원 정보
- `g5_board`: 게시판 설정
- `g5_write_free`: 게시글
- `g5_shop_item`: 상품 정보
- `g5_shop_category`: 상품 카테고리

## 커스터마이징

### 테마 수정

테마 파일은 `theme/pumae/` 디렉토리에 있습니다:

- `css/style.css`: 스타일 수정
- `header.php`: 헤더 레이아웃 수정
- `footer.php`: 푸터 레이아웃 수정

### 페이지 추가

새로운 페이지를 추가하려면:

1. PHP 파일 생성 (예: `about.php`)
2. `theme/pumae/header.php`와 `theme/pumae/footer.php` 포함
3. 필요한 스타일은 `theme/pumae/css/style.css`에 추가

## 보안 주의사항

- 설치 완료 후 `install.php` 파일을 삭제하거나 보호하세요
- `data/dbconfig.php` 파일의 권한을 적절히 설정하세요
- 프로덕션 환경에서는 SQLite 대신 MySQL/MariaDB 사용을 권장합니다

## 문제 해결

### SQLite 확장이 활성화되지 않은 경우

`php.ini` 파일에서 다음 줄의 주석을 제거하세요:

```ini
extension=pdo_sqlite
extension=sqlite3
```

### 권한 오류

데이터 디렉토리에 쓰기 권한이 필요합니다:

```bash
chmod 755 data
chmod 755 data/db
```

## 라이선스

이 프로젝트는 Gnuboard와 YoungCart를 기반으로 합니다.

## 지원

문제가 발생하면 다음을 확인하세요:

1. PHP 버전 (7.4 이상)
2. SQLite 확장 활성화 여부
3. 디렉토리 권한 설정
4. 웹 서버 설정

