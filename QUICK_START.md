# 빠른 시작 가이드

## PHP 설치 (1회만)

### 방법 1: PHP 직접 다운로드 (권장)

1. **PHP 다운로드**
   - https://windows.php.net/download/ 접속
   - "VS16 x64 Non Thread Safe" 버전 ZIP 파일 다운로드
   - 예: `php-8.2.x-Win32-vs16-x64.zip`

2. **압축 해제**
   - `C:\php` 폴더에 압축 해제

3. **환경 변수 추가**
   - Windows 검색에서 "환경 변수" 검색
   - "시스템 환경 변수 편집" 선택
   - "환경 변수" 버튼 클릭
   - "시스템 변수"에서 "Path" 선택 → "편집"
   - "새로 만들기" → `C:\php` 추가
   - 모든 창 "확인" 클릭

4. **PowerShell 재시작 후 확인**
   ```powershell
   php -v
   ```

### 방법 2: Chocolatey 사용 (선택사항)

```powershell
# 관리자 권한 PowerShell에서 실행
choco install php
```

## 설치 및 실행

### 1단계: 설치 (최초 1회)

```powershell
php install_cli.php
```

또는 웹 브라우저에서:
```
http://localhost:8000/install.php
```

### 2단계: 서버 실행

```powershell
php -S localhost:8000
```

또는 배치 파일 실행:
```powershell
.\start_server.bat
```

### 3단계: 브라우저에서 접속

```
http://localhost:8000/index.php
```

## 문제 해결

### PHP를 찾을 수 없습니다
- 환경 변수 PATH에 PHP 경로가 추가되었는지 확인
- PowerShell을 재시작했는지 확인
- `php -v` 명령어로 확인

### SQLite 확장 오류
- `php.ini` 파일에서 `extension=pdo_sqlite` 주석 제거
- PHP 재시작

