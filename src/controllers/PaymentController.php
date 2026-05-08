<?php
/**
 * PAYMENT CONTROLLER
 *
 * PATTERN: ServiceFactory used for PaymentGatewayService (Factory Pattern).
 *
 * FIX: Removed direct `new PaymentGatewayService()` and `new EscrowService()`.
 *      All service instantiation goes through ServiceFactory::make() to maintain
 *      consistency with the Factory Pattern used across the whole project.
 *
 * FIX: escrow() now correctly calls $paymentModel->getEscrowPayments()
 *      instead of getByUser() — shows only escrow-locked payments.
 *
 * Payment Lifecycle:
 *   pending → escrow → completed → (optionally) refunded
 *   pending → failed
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/ServiceFactory.php';
require_once BASE_PATH . '/models/Payment.php';
require_once BASE_PATH . '/models/PromoCode.php';

class PaymentController extends Controller
{
    private Payment $paymentModel;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel = new Payment();
    }

    /**
     * VIEW PAYMENT HISTORY — all payments for logged-in user.
     */
    public function paymentHistory(): void
    {
        $this->requireLogin();
        $payments = $this->paymentModel->getByUser(current_user_id());
        $this->render('payment/payment-history', ['payments' => $payments]);
    }

    /**
     * MAKE PAYMENT — driver pays for a reservation.
     *
     * Creates payment in 'escrow' state (funds locked until checkout).
     */
    public function makePayment(): void
    {
        $this->requireLogin();

        if ($this->isPost()) {
            $amount = (float)$this->post('amount');
            $method = $this->post('method', 'credit_card');
            $resId  = (int)$this->post('reservation_id');

            if ($amount <= 0) {
                set_flash('error', 'Invalid payment amount.');
                $this->render('payment/make-payment');
                return;
            }

            /** @var PaymentGatewayService $gateway */
            $gateway = ServiceFactory::make('payment'); // PATTERN: Factory

            $result = $gateway->charge($amount, [
                'user_id'        => current_user_id(),
                'reservation_id' => $resId ?: null,
                'method'         => $method,
            ]);

            if ($result['success']) {
                set_flash(
                    'success',
                    "Payment of \$" . number_format($amount, 2)
                    . " received. Ref: {$result['transaction_ref']}."
                );
                $this->redirect('?page=payment-history');
                return;
            }

            set_flash('error', $result['message'] ?? 'Payment failed. Please try again.');
        }

        $this->render('payment/make-payment');
    }

    /**
     * RECEIPT — show individual payment receipt.
     */
    public function receipt(): void
    {
        $this->requireLogin();
        $id      = (int)$this->get('id');
        $payment = $this->paymentModel->findById($id);

        if (!$payment || (int)$payment['user_id'] !== current_user_id()) {
            set_flash('error', 'Payment not found.');
            $this->redirect('?page=payment-history');
            return;
        }

        $this->render('payment/receipt', ['payment' => $payment]);
    }

    /**
     * ESCROW VIEW — shows payments currently locked in escrow.
     *
     * FIX: Now calls getEscrowPayments() to filter only escrow-locked payments,
     *      not getByUser() which would show all payments.
     */
    public function escrow(): void
    {
        $this->requireLogin();
        $payments = $this->paymentModel->getEscrowPayments(current_user_id());
        $this->render('payment/escrow', ['payments' => $payments]);
    }

    /**
     * PROMO CODE — validate a promotional discount code.
     */
    public function promoCode(): void
    {
        $this->requireRole('driver');

        $result = null;

        if ($this->isPost()) {
            $code  = trim($this->post('code'));
            $promo = (new PromoCode())->findByCode($code);

            if ($promo) {
                $result = [
                    'found'    => true,
                    'promo'    => $promo,
                    'message'  => "Code '{$code}' gives {$promo['discount_percent']}% off!",
                ];
            } else {
                $result = [
                    'found'   => false,
                    'message' => "Code '{$code}' is invalid or expired.",
                ];
            }
        }

        $this->render('payment/promo-code', ['result' => $result]);
    }
}