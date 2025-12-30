@echo off
chcp 65001 >nul
echo ========================================
echo Gnuboard 실행
echo ========================================
echo.

REM PHP 경로 확인
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [오류] PHP가 설치되어 있지 않거나 PATH에 등록되지 않았습니다.
    echo.
    echo 해결 방법:
    echo 1. PHP를 다운로드: https://windows.php.net/download/
    echo 2. 압축 해제 후 환경 변수 PATH에 추가
    echo 3. 또는 아래 경로에 PHP가 있다면 직접 경로를 입력하세요
    echo.
    set /p PHP_PATH="PHP 경로 (예: C:\php\php.exe): "
    if exist "!PHP_PATH!" (
        "!PHP_PATH!" -S localhost:8000
    ) else (
        echo PHP를 찾을 수 없습니다.
        pause
        exit /b 1
    )
) else (
    echo PHP를 찾았습니다. 서버를 시작합니다...
    echo.
    echo 브라우저에서 http://localhost:8000 으로 접속하세요
    echo 종료하려면 Ctrl+C를 누르세요
    echo.
    php -S localhost:8000
)

