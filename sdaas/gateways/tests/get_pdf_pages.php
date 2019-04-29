<?php
 
// Include Composer autoloader if not already done.
include '../vendor/autoload.php';
 
isset($argv[1]) ||  die("File missing (usage: get_pdf_pages.php file)");

// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile($argv[1]);
 
// Retrieve all pages from the pdf file.
$pages  = $pdf->getPages();
 
// Loop over each page to extract text.
$p=0;
foreach ($pages as $page) {
    $p++;
    $pageText = explode("\n", preg_replace('/\t+/', '', $page->getText()));
    echo "---------- page: $p --------\n";
    print_r($pageText);
}