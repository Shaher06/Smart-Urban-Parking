<?php


class PdfReportService
{
    /**
     * Generate a printable HTML revenue/data report.
     *
     * @param array  $data   Array of associative arrays (table rows)
     * @param string $title  Report title shown at top
     * @return string        Full HTML document ready to echo or stream
     */
    public function generateRevenueHtml(array $data, string $title = 'Revenue Report'): string
    {
        return $this->buildHtmlReport($data, $title);
    }

    /**
     * Generate a fine report in printable HTML.
     */
    public function generateFineHtml(array $data, string $title = 'Fines Report'): string
    {
        return $this->buildHtmlReport($data, $title);
    }

    /**
     * Generate an occupancy report in printable HTML.
     */
    public function generateOccupancyHtml(array $data, string $title = 'Occupancy Report'): string
    {
        return $this->buildHtmlReport($data, $title);
    }

    /**
     * Output the HTML directly to the browser (inline view — user can Ctrl+P).
     *
     * @param string $html      HTML content from one of the generate*() methods
     * @param string $filename  Suggested filename (shown in browser tab)
     */
    public function streamToBrowser(string $html, string $filename = 'report.html'): void
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="' . basename($filename) . '"');
        header('Cache-Control: no-cache');
        echo $html;
        exit;
    }

    /**
     * Save the HTML to a file and return the saved path.
     * Useful for attaching to emails or logging.
     *
     * @param string $html       Report HTML
     * @param string $filename   Filename relative to UPLOAD_PATH/reports/
     * @return string|false      Full path to saved file, or false on failure
     */
    public function saveToFile(string $html, string $filename): string|false
    {
        $dir = UPLOAD_PATH . '/reports';
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                error_log("PdfReportService: Could not create directory {$dir}");
                return false;
            }
        }

        $path = $dir . '/' . basename($filename);
        if (file_put_contents($path, $html) === false) {
            error_log("PdfReportService: Could not write file {$path}");
            return false;
        }

        return $path;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Build the full HTML document with print-optimised CSS.
     *
     * @param array  $data   Rows to display
     * @param string $title  Page title
     * @return string        Complete HTML
     */
    private function buildHtmlReport(array $data, string $title): string
    {
        $date = date('Y-m-d H:i:s');

        // Build table headers from the first row's keys
        $headers = '';
        if (!empty($data)) {
            foreach (array_keys($data[0]) as $col) {
                $label    = ucwords(str_replace('_', ' ', $col));
                $headers .= '<th>' . htmlspecialchars($label) . '</th>';
            }
        }

        // Build table rows
        $rows = '';
        foreach ($data as $row) {
            $cells = '';
            foreach ($row as $cell) {
                $cells .= '<td>' . htmlspecialchars((string)($cell ?? '')) . '</td>';
            }
            $rows .= "<tr>{$cells}</tr>\n";
        }

        $totalRows = count($data);
        $titleEsc  = htmlspecialchars($title);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$titleEsc}</title>
  <style>
    /* ── Print styles ── */
    @media print {
      .no-print { display: none !important; }
      body { font-size: 11pt; margin: 0; }
      table { page-break-inside: auto; }
      tr    { page-break-inside: avoid; page-break-after: auto; }
    }

    /* ── Screen styles ── */
    * { box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 30px;
      color: #333;
      background: #fff;
    }
    .report-header {
      border-bottom: 3px solid #1a56a7;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    h1 { color: #1a56a7; margin: 0 0 4px 0; font-size: 1.8em; }
    .meta { color: #666; font-size: 0.85em; margin: 0; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      font-size: 0.9em;
    }
    th {
      background: #1a56a7;
      color: #fff;
      padding: 9px 14px;
      text-align: left;
      font-weight: 600;
    }
    td {
      padding: 8px 14px;
      border-bottom: 1px solid #e0e6f0;
    }
    tr:nth-child(even) { background: #f4f8ff; }
    tr:hover { background: #e8f0fe; }
    .summary {
      margin-top: 15px;
      font-size: 0.85em;
      color: #555;
    }
    .footer {
      margin-top: 30px;
      padding-top: 10px;
      border-top: 1px solid #ddd;
      font-size: 0.78em;
      color: #999;
      text-align: center;
    }
    .btn-print {
      display: inline-block;
      margin-bottom: 18px;
      padding: 9px 22px;
      background: #1a56a7;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.95em;
      text-decoration: none;
    }
    .btn-print:hover { background: #1345a0; }
    .empty { color: #999; text-align: center; padding: 30px; font-style: italic; }
  </style>
</head>
<body>
  <button class="btn-print no-print" onclick="window.print()">
    🖨️ Print / Save as PDF
  </button>

  <div class="report-header">
    <h1>📊 {$titleEsc}</h1>
    <p class="meta">Generated: {$date} &nbsp;|&nbsp; Smart Urban Parking Management System</p>
  </div>

  <table>
    <thead>
      <tr>{$headers}</tr>
    </thead>
    <tbody>
      {$rows}
      {$this->emptyRow($data)}
    </tbody>
  </table>

  <p class="summary">Total records: <strong>{$totalRows}</strong></p>

  <div class="footer">
    Smart Urban Parking Management System &copy; {$date}
    &nbsp;|&nbsp; Confidential &mdash; For internal use only.
  </div>
</body>
</html>
HTML;
    }

    /**
     * Return an empty-state row if no data exists.
     */
    private function emptyRow(array $data): string
    {
        if (!empty($data)) return '';
        return '<tr><td colspan="99" class="empty">No records found for this report.</td></tr>';
    }
}