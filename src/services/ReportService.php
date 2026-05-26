<?php



require_once BASE_PATH . '/models/Report.php';

class ReportService
{
    private Report $reportModel;

    public function __construct()
    {
        $this->reportModel = new Report();
    }

    public function generate(array $params): array
    {
        $type = $params['type'] ?? 'revenue';
        switch ($type) {
            case 'revenue':
                return $this->reportModel->getRevenueByMonth();
            case 'spots':
                return $this->reportModel->getRevenueBySpot();
            case 'owner':
                return $this->reportModel->getOwnerEarnings((int)($params['owner_id'] ?? 0));
            default:
                return [];
        }
    }

    public function export(array $data, string $format = 'csv'): string
    {
        if ($format === 'csv') {
            if (empty($data)) {
                return '';
            }
            $output = implode(',', array_keys($data[0])) . "\n";
            foreach ($data as $row) {
                $output .= implode(',', array_map(
                    fn($v) => '"' . str_replace('"', '""', (string)$v) . '"',
                    $row
                )) . "\n";
            }
            return $output;
        }
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}