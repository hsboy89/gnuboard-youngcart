// 메인 JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // 모바일 메뉴 토글 (필요시)
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
    }
    
    // 스무스 스크롤 (해시 링크만 처리)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            // 빈 해시나 #만 있는 경우는 처리하지 않음
            if (href === '#' || href === '') {
                return;
            }
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // 로그인 버튼 - 강제 클릭 처리
    const loginBtn = document.getElementById('login-link');
    if (loginBtn) {
        // 모든 이벤트 리스너 제거 후 새로 추가
        const newBtn = loginBtn.cloneNode(true);
        loginBtn.parentNode.replaceChild(newBtn, loginBtn);
        
        // 강제 클릭 처리
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const href = this.getAttribute('href');
            console.log('로그인 버튼 클릭 - 강제 이동:', href);
            window.location.href = href;
            return false;
        }, true);
        
        // mousedown에서도 처리
        newBtn.addEventListener('mousedown', function(e) {
            if (e.button === 0) {
                const href = this.getAttribute('href');
                console.log('로그인 버튼 mousedown - 강제 이동:', href);
                setTimeout(() => {
                    window.location.href = href;
                }, 0);
            }
        }, true);
    }
});

