<?php
/**
 * REPORT CONTROLLER — Refactored
 *
 * Added:
 * - PDF report export (printable HTML)
 * - Occupancy report
 * - Multi-currency revenue display
 *
 * PATTERN: ServiceFactory for all services
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/ServiceFactory.php';
require_once BASE_PATH . '/models/Payment.php';

class ReportController extends Controller
{
    private Payment $paymentModel;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel = new Payment();
    }

    public function adminReports(): void
    {
        $this->requireRole('admin');
        $reportService = ServiceFactory::make('report'); // PATTERN: Factory
        $currency      = ServiceFactory::make('currency');

        $monthly   = $reportService->generate(['type' => 'revenue']);
        $bySpot    = $reportService->generate(['type' => 'spots']);
        $total     = $this->paymentModel->getTotalRevenue();
        $selectedCurrency = $this->get('currency', 'USD');
        $converted = $currency->convert($total, $selectedCurrency);

        $this->render('admin/reports', [
            'monthly'           => $monthly,
            'bySpot'            => $bySpot,
            'total'             => $total,
            'converted'         => $converted,
            'selected_currency' => $selectedCurrency,
            'currencies'        => $currency->getSupportedCurrencies(),
            'currency_service'  => $currency,
        ]);
    }

    public function generate(): void
    {
        $this->requireRole('admin');
        $type          = $this->get('type', 'revenue');
        $reportService = ServiceFactory::make('report');
        $data          = $reportService->generate(['type' => $type]);
        $this->render('reports/generate', ['data' => $data, 'type' => $type]);
    }

    public function revenue(): void
    {
        $this->requireRole('admin');
        $reportService = ServiceFactory::make('report');
        $this->render('reports/revenue', [
            'monthly' => $reportService->generate(['type' => 'revenue']),
            'bySpot'  => $reportService->generate(['type' => 'spots']),
            'total'   => $this->paymentModel->getTotalRevenue(),
        ]);
    }

    public function heatmap(): void
    {
        $this->requireRole('admin');
        $reportService = ServiceFactory::make('report');
        $bySpot        = $reportService->generate(['type' => 'spots']);
        $this->render('reports/heatmap', ['bySpot' => $bySpot]);
    }

    /**
     * EXPORT PDF REPORT — SRS: PDF Report Generation
     *
     * Generates a printable HTML page the user can save as PDF via browser.
     */
    public function exportPdf(): void
    {
        $this->requireRole('admin');
        $type          = $this->get('type', 'revenue');
        $reportService = ServiceFactory::make('report');
        $pdfService    = ServiceFactory::make('pdf');

        $data  = $reportService->generate(['type' => $type]);
        $title = ucfirst($type) . ' Report — ' . date('Y-m-d');
        $html  = $pdfService->generateRevenueHtml($data, $title);
        $pdfService->streamToBrowser($html, $type . '-report-' . date('Ymd') . '.html');
    }

    /**
     * OCCUPANCY REPORT — SRS: Real-Time Occupancy Predictor
     */
    public function occupancy(): void
    {
        $this->requireRoles(['admin', 'officer']);
        $occupancyService = ServiceFactory::make('occupancy');
        $liveData         = $occupancyService->getLiveOccupancy();
        $this->render('reports/occupancy', ['live_data' => $liveData]);
    }
}