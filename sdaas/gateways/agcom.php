<?php
/**
 * (c)2019 by enrico@LinkedData.Center
 * examples:  
 *  agcom.php https://www.agcom.it/documents/10179/14509260/Dati+monitoraggio+24-04-2019#2019-04-08T00:00:00/P14D tests/data/test1.pdf
 *
 */
include 'vendor/autoload.php';

function usage($note='') {
    $usage="agcom <source>#<gregorianUnit>/YYY-MM-DDTmm:ss/PddD  <input file>";
    $example="php agcom.php https://www.agcom.it/documents/10179/14509260/Dati+monitoraggio+24-04-2019#2019-04-08T00:00:00/P14D tests/data/test1.pdf";
   die($note."\nusage: $usage)\n$example\n");
}

isset($argv[2]) || usage("no file");

preg_match('/(.+)#(.+)/',$argv[1],$opt) || usage("parameter 1 mismatch");

$source = $opt[1];
$interval= $opt[2];
$id = md5($source);
$period = "http://reference.data.gov.uk/id/gregorian-interval/$interval";
$inputFile = $argv[2];

echo "# (c)2019 by enrico@LinkedData.Center
# usage php build_agcom.php
# example:  php build_agcom.php < data/agcom-test.csv
#
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix qb: <http://purl.org/linked-data/cube#> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
@prefix skos: <http://www.w3.org/2004/02/skos/core#> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .
@prefix interval: <http://reference.data.gov.uk/def/intervals/> .
@prefix sdmx-subject: <http://purl.org/linked-data/sdmx/2009/subject#> .
@prefix agcom: <https://g0v-it.github.io/ontologies/agcom#> .
@prefix resource: <http://agcom.linkeddata.cloud/resource/> .

resource:$id a qb:DataSet ;
    dct:title \"Report pluralismo politico-istituzionale in televisione ($interval)\"@it;
    dct:identifier \"$id\";
    dct:source <$source> ;
    qb:structure agcom:rilevazioni_pluralismo ;
    dct:subject
        sdmx-subject:1.10 ,      # Political and other community activities
        sdmx-subject:1.11 ,      # Time use
        sdmx-subject:3.3.3; 	 # Information society
    agcom:refPeriod <$period> ;
    dct:publisher  resource:AGCOM
.

# following data derived from AGCOM pdf report
";



function normalizeLabel($label) {
    $label = preg_replace('/[[:^print:]]/', '', $label );
    $label = preg_replace('/\s+/', '_', $label);
    $label = preg_replace('/_/', ' ', $label);
    $label = trim($label);
    $label = strtoupper($label);
    
    return $label ;
}

/**
 * This function translates the name of a AGCOM subject into token id
 * that is expected to be defined in the agcom:soggetti taxonomy
 */
function strToAgcomId($str) {
    // Normalizzazione standard
    $str = trim($str);
    $str = preg_replace('/[[:^print:]]/', '', $str); 
    $str = str_replace([' ', "'", "\t", '(', ')'], ' ', $str);
    $str = preg_replace('/\s+/', '_', $str);
    $str = preg_replace('/\+/', 'Piu', $str);
    
    // normalizzazione dei casi speciali noti
    $signature= strtoupper( preg_replace('/[\W_]/','', $str));
    if(preg_match('/PIUEUROPA/', $signature))               { $str="PiuEuropa"; }
    elseif( preg_match('/LEGA/', $signature))               { $str="Lega"; }
    elseif( preg_match('/PARTITODEMOCRATICO/', $signature)) { $str="PD"; }
    elseif( preg_match('/5STELLE/', $signature))            { $str="M5S"; }
    elseif( preg_match('/GOVERNO/', $signature))            { $str="Governo"; }
    elseif( preg_match('/FORZAITALIA/', $signature))        { $str="Forza_Italia"; }
    elseif( preg_match('/FRATELLIDITALIA/', $signature))    { $str="Fratelli_dItalia"; }
    elseif( preg_match('/CASAPOUND/', $signature))          { $str="Casa_Pound"; }
    
    return $str;
}

function seconds($str_time) {
    sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
    return $hours*3600+$minutes*60+$seconds;
}

/**
 * This function detect the context in  apage of data
 */
function getContext($pageText) {
  
    // Formato usato nel report 14239063/Dati+monitoraggio+13-03-2019
    if( preg_match( "/^(.+):\sRanking dei 20 soggetti/",$pageText[0], $matches) ) {
        $context = strtoupper(preg_replace('/\s+/', '', $matches[1]));
        $extraTg = preg_match( "/^nei programmi extra/",$pageText[1]);
    } 
    
    
    // Formato usato nel report 14509260/Dati+monitoraggio+10-04-2019 extra tg
    elseif (preg_match( "/^Ranking\s*programmi\s*Extra.*Tg di Testata\s+$/",$pageText[0])) {
        $extraTg = true;
        preg_match ("/(.*):/",$pageText[1], $matches) || die (print_r($pageText,1));//"invalid format in line 1(${pageText[1]})");
        $context = preg_replace('/\s+/', '', $matches[1]);
    }
    

    // Formato usato nel report 14509260/Dati+monitoraggio+10-04-2019 tg
    elseif (preg_match( "/^Ranking Telegiornali\s+$/",$pageText[0], $matches)) {
        $extraTg = false;
        preg_match ("/(.*):/",$pageText[1], $matches) || die (print_r($pageText,1));//"invalid format in line 1(${pageText[1]})");
        $context = preg_replace('/\s+/', '', $matches[1]);
    }
    
    // Formato usato nel report 14509260/Dati+monitoraggio+07-05-2019 tg
    elseif (preg_match( "/^Ranking Telegiornali\s+(.+):/",$pageText[0], $matches)) {
        $extraTg = false;
        $context = preg_replace('/\s+/', '', $matches[1]);
    }
    
    
    // Formato usato nel report 14509260/Dati+monitoraggio+24-04-2019+1556130645484 extra tg
    elseif (preg_match( "/^Ranking\s+programmi\s+Extra\s+-\s+Tg\s+di\s+Testata\s+(.+):/",$pageText[0], $matches)) {
        $extraTg = true ;
        $context = preg_replace('/\s+/', '', $matches[1]);
    }
    
    // Formato usato nel report 14509260/Dati+monitoraggio+24-04-2019+1556130645484 tg
    elseif (preg_match( "/^(.+):\s+I\s+2\s+0/",$pageText[0], $matches)) {
        $extraTg = false ;
        $context = preg_match( "/^Ranking programmi/",$matches[1])
            ?false
            :preg_replace('/\s+/', '', $matches[1]);
    }
    
    else {
        $context=false;
    }
    
    if( $context) {
        $context .= $extraTg?'-extra_tg':'';
    }

    return $context;
}









// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile($inputFile);

// Retrieve all pages from the pdf file.
$pages  = $pdf->getPages();

// Loop over each page to extract text.
$labels=array();
$pageCount=0;
foreach ($pages as $page) {
    $pageCount++;
    $pageText = explode("\n", $page->getText());
    if ($contextLabel=getContext($pageText)) {       
        // generate context id and save its label
        $context = strToAgcomId($contextLabel). '_programma';
        $labels[$context]=$contextLabel;
        
        // find data in page
        $i=0;
        while( isset($pageText[++$i])) {
            // es: Zingaretti   Nicola   (Partito Democratico)   0:01:15   5,93%
            if (preg_match("/(.+[^\s])\s+\((.+)\)\s+(\d+:\d+:\d+)\s+\d+,\d+%/", $pageText[$i], $matches)){
                
                // generate subject id and save its label
                $subject = strToAgcomId($matches[1]);
                $labels[$subject] = normalizeLabel($matches[1]); 
                
                // generate role id and save its label
                $role = strToAgcomId($matches[2]);
                $labels[$role] = normalizeLabel($matches[2]);
                
                // generate speaking time in seconds
                $speakingTime = seconds($matches[3]);
                $riga=$i+1;
                echo "
resource:{$id}_p${pageCount}_r${riga} a qb:Observation;
    rdfs:label \"${labels[$subject]} su $contextLabel\"@it;
    rdfs:comment \"Rilevazione AGCOM del tempo di parola di ${labels[$subject]} nel ruolo di ${labels[$role]} su $contextLabel nel periodo $interval\"@it;
    qb:dataSet resource:$id ;
    agcom:subject resource:${subject} ;
    agcom:role resource:${role} ;
    agcom:context resource:${context} ;
    agcom:speakingTime $speakingTime
.\n";
            }
        }
    }
}


// Serializza le labels associate ai concetti
echo "\n\n# Concept hidden labels: \n\n";
foreach( $labels as $token => $label) {
    echo "resource:${token} skos:hiddenLabel \"$label\"@it .\n";
}

