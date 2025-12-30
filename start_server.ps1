Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Gnuboard PHP 내장 서버 시작" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "서버가 시작되면 브라우저에서 다음 주소로 접속하세요:" -ForegroundColor Yellow
Write-Host "http://localhost:8000" -ForegroundColor Green
Write-Host ""
Write-Host "종료하려면 Ctrl+C를 누르세요." -ForegroundColor Yellow
Write-Host ""
php -S localhost:8000

