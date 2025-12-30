<?php
/**
 * 결제 대행사 통합 클래스
 * NHN KCP, KG이니시스, 토스페이먼츠 지원
 */

class PaymentGateway {
    private $pg;
    private $config;
    
    public function __construct($pg = 'card') {
        $this->pg = $pg;
        $this->config = get_payment_config($pg);
    }
    
    /**
     * 결제 처리
     */
    public function processPayment($data) {
        switch ($this->pg) {
            case 'card':
            case 'card_kcp':
                return $this->processKCP($data);
            case 'card_inicis':
                return $this->processInicis($data);
            case 'card_toss':
                return $this->processToss($data);
            case 'bank':
                return $this->processBankTransfer($data);
            case 'virtual':
                return $this->processVirtualAccount($data);
            case 'mobile':
                return $this->processMobile($data);
            default:
                throw new Exception('지원하지 않는 결제 수단입니다.');
        }
    }
    
    /**
     * 결제 폼 렌더링
     */
    public function renderPaymentForm($order_no, $amount, $order) {
        switch ($this->pg) {
            case 'card':
            case 'card_kcp':
                return $this->renderKCPForm($order_no, $amount, $order);
            case 'card_inicis':
                return $this->renderInicisForm($order_no, $amount, $order);
            case 'card_toss':
                return $this->renderTossForm($order_no, $amount, $order);
            case 'bank':
                return $this->renderBankTransferForm($order_no, $amount, $order);
            case 'virtual':
                return $this->renderVirtualAccountForm($order_no, $amount, $order);
            case 'mobile':
                return $this->renderMobileForm($order_no, $amount, $order);
            default:
                return '<p>결제 수단을 선택해주세요.</p>';
        }
    }
    
    /**
     * 결제 수단 이름 가져오기
     */
    public function getPaymentMethodName($pg) {
        $names = [
            'card' => '신용카드 (NHN KCP)',
            'card_kcp' => '신용카드 (NHN KCP)',
            'card_inicis' => '신용카드 (KG이니시스)',
            'card_toss' => '신용카드 (토스페이먼츠)',
            'bank' => '무통장 입금',
            'virtual' => '가상계좌',
            'mobile' => '휴대폰 결제'
        ];
        return $names[$pg] ?? '결제';
    }
    
    /**
     * NHN KCP 결제 처리
     */
    private function processKCP($data) {
        // KCP 결제 처리 로직
        // 실제 구현 시 KCP API 연동 필요
        return [
            'success' => true,
            'transaction_id' => 'KCP_' . time(),
            'message' => '결제가 완료되었습니다.'
        ];
    }
    
    /**
     * NHN KCP 결제 폼
     */
    private function renderKCPForm($order_no, $amount, $order) {
        $config = get_payment_config('kcp');
        
        return '
        <form method="POST" action="' . $config['api_url'] . '" id="kcpForm">
            <input type="hidden" name="site_cd" value="' . htmlspecialchars($config['site_cd']) . '">
            <input type="hidden" name="order_no" value="' . htmlspecialchars($order_no) . '">
            <input type="hidden" name="amount" value="' . $amount . '">
            <input type="hidden" name="product_name" value="' . htmlspecialchars($order_no . ' 주문') . '">
            <input type="hidden" name="buyer_name" value="' . htmlspecialchars($order['od_name']) . '">
            <input type="hidden" name="buyer_email" value="' . htmlspecialchars($order['od_email']) . '">
            <input type="hidden" name="buyer_tel" value="' . htmlspecialchars($order['od_tel']) . '">
            <input type="hidden" name="return_url" value="' . htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . '/payment_result.php?pg=kcp&lang=' . ($_GET['lang'] ?? 'ko')) . '">
            <button type="submit" class="btn btn-primary">NHN KCP로 결제하기</button>
        </form>
        <script>
            // KCP 결제창 열기
            document.getElementById("kcpForm").addEventListener("submit", function(e) {
                e.preventDefault();
                // 실제 구현 시 KCP 결제창 팝업 또는 리다이렉트
                alert("KCP 결제 모듈 연동 필요\\n테스트 모드에서는 실제 결제가 진행되지 않습니다.");
            });
        </script>';
    }
    
    /**
     * KG이니시스 결제 처리
     */
    private function processInicis($data) {
        return [
            'success' => true,
            'transaction_id' => 'INICIS_' . time(),
            'message' => '결제가 완료되었습니다.'
        ];
    }
    
    /**
     * KG이니시스 결제 폼
     */
    private function renderInicisForm($order_no, $amount, $order) {
        $config = get_payment_config('inicis');
        
        return '
        <form method="POST" action="' . $config['api_url'] . '" id="inicisForm">
            <input type="hidden" name="mid" value="' . htmlspecialchars($config['mid']) . '">
            <input type="hidden" name="oid" value="' . htmlspecialchars($order_no) . '">
            <input type="hidden" name="price" value="' . $amount . '">
            <input type="hidden" name="goodname" value="' . htmlspecialchars($order_no . ' 주문') . '">
            <input type="hidden" name="buyername" value="' . htmlspecialchars($order['od_name']) . '">
            <input type="hidden" name="buyeremail" value="' . htmlspecialchars($order['od_email']) . '">
            <input type="hidden" name="buyertel" value="' . htmlspecialchars($order['od_tel']) . '">
            <input type="hidden" name="returnUrl" value="' . htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . '/payment_result.php?pg=inicis&lang=' . ($_GET['lang'] ?? 'ko')) . '">
            <button type="submit" class="btn btn-primary">KG이니시스로 결제하기</button>
        </form>
        <script>
            document.getElementById("inicisForm").addEventListener("submit", function(e) {
                e.preventDefault();
                alert("KG이니시스 결제 모듈 연동 필요\\n테스트 모드에서는 실제 결제가 진행되지 않습니다.");
            });
        </script>';
    }
    
    /**
     * 토스페이먼츠 결제 처리
     */
    private function processToss($data) {
        return [
            'success' => true,
            'transaction_id' => 'TOSS_' . time(),
            'message' => '결제가 완료되었습니다.'
        ];
    }
    
    /**
     * 토스페이먼츠 결제 폼
     */
    private function renderTossForm($order_no, $amount, $order) {
        $config = get_payment_config('toss');
        
        return '
        <form method="POST" id="tossForm">
            <input type="hidden" name="order_no" value="' . htmlspecialchars($order_no) . '">
            <input type="hidden" name="amount" value="' . $amount . '">
            <button type="button" class="btn btn-primary" onclick="requestTossPayment()">토스페이먼츠로 결제하기</button>
        </form>
        <script src="https://js.tosspayments.com/v1/payment"></script>
        <script>
            function requestTossPayment() {
                var tossPayments = TossPayments("' . htmlspecialchars($config['client_key']) . '");
                tossPayments.requestPayment("카드", {
                    amount: ' . $amount . ',
                    orderId: "' . htmlspecialchars($order_no) . '",
                    orderName: "' . htmlspecialchars($order_no . ' 주문') . '",
                    customerName: "' . htmlspecialchars($order['od_name']) . '",
                    successUrl: "http://' . $_SERVER['HTTP_HOST'] . '/payment_result.php?pg=toss&lang=' . ($_GET['lang'] ?? 'ko') . '",
                    failUrl: "http://' . $_SERVER['HTTP_HOST'] . '/payment_result.php?pg=toss&fail=1&lang=' . ($_GET['lang'] ?? 'ko') . '"
                }).catch(function(error) {
                    alert("토스페이먼츠 결제 모듈 연동 필요\\n테스트 모드에서는 실제 결제가 진행되지 않습니다.");
                });
            }
        </script>';
    }
    
    /**
     * 무통장 입금 처리
     */
    private function processBankTransfer($data) {
        // 무통장 입금은 즉시 완료 처리
        return [
            'success' => true,
            'transaction_id' => 'BANK_' . time(),
            'message' => '무통장 입금 안내가 발송되었습니다.',
            'account_info' => [
                'bank' => '테스트은행',
                'account' => '123-456-789012',
                'holder' => '테스트계좌'
            ]
        ];
    }
    
    /**
     * 무통장 입금 폼
     */
    private function renderBankTransferForm($order_no, $amount, $order) {
        return '
        <div style="padding: 20px; background: #f8f9fa; border-radius: 4px; margin-bottom: 20px;">
            <h3>입금 계좌 정보</h3>
            <p><strong>은행:</strong> 테스트은행</p>
            <p><strong>계좌번호:</strong> 123-456-789012</p>
            <p><strong>예금주:</strong> 테스트계좌</p>
            <p><strong>입금금액:</strong> ' . number_format($amount) . '원</p>
            <p style="color: #dc3545; margin-top: 10px;"><small>※ 입금 확인 후 주문이 완료됩니다.</small></p>
        </div>
        <form method="POST">
            <input type="hidden" name="payment_data" value="bank_transfer">
            <button type="submit" class="btn btn-primary">주문 완료</button>
        </form>';
    }
    
    /**
     * 가상계좌 처리
     */
    private function processVirtualAccount($data) {
        return [
            'success' => true,
            'transaction_id' => 'VIRTUAL_' . time(),
            'message' => '가상계좌가 발급되었습니다.',
            'virtual_account' => [
                'bank' => '테스트은행',
                'account' => '1234-5678-9012',
                'expiry' => date('Y-m-d H:i:s', strtotime('+7 days'))
            ]
        ];
    }
    
    /**
     * 가상계좌 폼
     */
    private function renderVirtualAccountForm($order_no, $amount, $order) {
        return '
        <div style="padding: 20px; background: #f8f9fa; border-radius: 4px; margin-bottom: 20px;">
            <h3>가상계좌 안내</h3>
            <p>가상계좌는 주문 완료 후 발급됩니다.</p>
            <p><strong>입금금액:</strong> ' . number_format($amount) . '원</p>
            <p style="color: #dc3545; margin-top: 10px;"><small>※ 가상계좌 입금 확인 후 주문이 완료됩니다.</small></p>
        </div>
        <form method="POST">
            <input type="hidden" name="payment_data" value="virtual_account">
            <button type="submit" class="btn btn-primary">가상계좌 발급 및 주문 완료</button>
        </form>';
    }
    
    /**
     * 휴대폰 결제 처리
     */
    private function processMobile($data) {
        return [
            'success' => true,
            'transaction_id' => 'MOBILE_' . time(),
            'message' => '휴대폰 결제가 완료되었습니다.'
        ];
    }
    
    /**
     * 휴대폰 결제 폼
     */
    private function renderMobileForm($order_no, $amount, $order) {
        return '
        <form method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label>휴대폰 번호</label>
                <input type="tel" name="mobile" value="' . htmlspecialchars($order['od_hp'] ?? '') . '" placeholder="010-1234-5678" required>
            </div>
            <input type="hidden" name="payment_data" value="mobile">
            <button type="submit" class="btn btn-primary">휴대폰 결제하기</button>
        </form>
        <script>
            document.querySelector("form").addEventListener("submit", function(e) {
                e.preventDefault();
                alert("휴대폰 결제 모듈 연동 필요\\n테스트 모드에서는 실제 결제가 진행되지 않습니다.");
            });
        </script>';
    }
}

