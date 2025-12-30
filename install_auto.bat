@echo off
chcp 65001 >nul
echo ========================================
echo Gnuboard 자동 설치 및 실행
echo ========================================
echo.

REM PHP 확인
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [오류] PHP가 설치되어 있지 않습니다.
    echo.
    echo PHP 설치 방법:
    echo 1. https://windows.php.net/download/ 에서 PHP 다운로드
    echo 2. C:\php 폴더에 압축 해제
    echo 3. 환경 변수 PATH에 C:\php 추가
    echo 4. PowerShell 재시작 후 다시 실행
    echo.
    echo 또는 Chocolatey로 설치:
    echo    choco install php
    echo.
    pause
    exit /b 1
)

echo [확인] PHP가 설치되어 있습니다.
php -v
echo.

REM 설치 확인
if exist "data\dbconfig.php" (
    echo [확인] 이미 설치가 완료되었습니다.
    echo.
) else (
    echo [설치] Gnuboard를 설치합니다...
    echo.
    php install_cli.php
    if %ERRORLEVEL% NEQ 0 (
        echo.
        echo [오류] 설치 중 오류가 발생했습니다.
        pause
        exit /b 1
    )
    echo.
)

REM 데이터베이스 확인
if not exist "data\db\gnuboard.db" (
    echo [경고] 데이터베이스 파일이 없습니다. 설치를 다시 실행하세요.
    pause
    exit /b 1
)

echo ========================================
echo 서버를 시작합니다...
echo ========================================
echo.
echo 브라우저에서 다음 주소로 접속하세요:
echo   http://localhost:8000
echo.
echo 종료하려면 Ctrl+C를 누르세요.
echo.
echo ========================================
echo.

php -S localhost:8000

