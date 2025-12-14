<?php
require 'vendor/autoload.php';

use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Common\Entity\Row;

$filePath = __DIR__ . '/test_holdings.xlsx';

$writer = new Writer();
$writer->openToFile($filePath);

// Row::fromValues is the standard helper in v4
$header = Row::fromValues(['Symbol', 'ISIN', 'Sector', 'Quantity Available', 'Average Price', 'Previous Closing Price', 'Unrealized P&L', 'Unrealized P&L Pct.', 'Quantity Discrepant', 'Quantity Long Term', 'Quantity Pledged (Margin)', 'Quantity Pledged (Loan)']);
$writer->addRow($header);
$row = Row::fromValues(['TATASTEEL', 'INE081A01012', 'Metals', 10, 150.5, 148.0, -25.0, -1.6, 0, 10, 0, 0]);
$writer->addRow($row);
$writer->close();

echo "Created test file at $filePath\n";

$reader = new Reader();
$reader->open($filePath);

foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
        $cells = $row->getCells();
        $data = [];
        foreach ($cells as $cell) {
            $data[] = $cell->getValue();
        }
        echo implode(", ", $data) . "\n";
    }
}
$reader->close();
unlink($filePath);
