<?php
require 'vendor/autoload.php';
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

$filePath = __DIR__ . '/test_holdings.xlsx';
$writer = new Writer();
$writer->openToFile($filePath);
$header = Row::fromValues(['Symbol', 'ISIN', 'Sector', 'Quantity Available', 'Average Price', 'Previous Closing Price', 'Unrealized P&L', 'Unrealized P&L Pct.', 'Quantity Discrepant', 'Quantity Long Term', 'Quantity Pledged (Margin)', 'Quantity Pledged (Loan)']);
$writer->addRow($header);
$writer->addRow(Row::fromValues(['TATASTEEL', 'INE081A01012', 'Metals', 10, 150.5, 148.0, -25.0, -1.6, 0, 10, 0, 0]));
$writer->addRow(Row::fromValues(['RELIANCE', 'INE002A01018', 'Oil & Gas', 5, 2500.0, 2600.0, 500.0, 4.0, 0, 5, 0, 0]));
$writer->close();
echo "Created $filePath";
