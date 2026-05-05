<?php

class PaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requirePatient();
    }

    /** GET /payments */
    public function index(): void
    {
        $patient     = $this->db->fetchOne("SELECT id FROM patients WHERE user_id=?", [Session::userId()]);
        $payModel    = $this->model('Payment');
        $payments    = $payModel->getForPatient($patient['id'] ?? 0);
        $summary     = $payModel->getSummary($patient['id'] ?? 0);
        $pageTitle   = 'Payments';
        $this->view('payments.index', compact('pageTitle','payments','summary'));
    }

    /** POST /payments/process */
    public function process(): void
    {
        if (!$this->isPost()) { $this->redirect('payments'); }
        $paymentId = (int)$this->post('payment_id');
        $method    = $this->post('method', 'credit_card');
        $payModel  = $this->model('Payment');

        if ($payModel->process($paymentId, $method)) {
            $this->auditLog('payment_processed','payments',"Payment ID: $paymentId via $method");
            Session::flash('success', 'Payment processed successfully!');
        } else {
            Session::flash('error', 'Payment could not be processed. Please try again.');
        }
        $this->redirect('payments');
    }
}
