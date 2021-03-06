# (c)2019 by enrico@LinkedData.Center
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

resource:ds_2019_02 a qb:DataSet ;
	dct:source <https://www.agcom.it/documentazione/documento?p_p_auth=fLw7zRht&p_p_id=101_INSTANCE_ls3TZlzsK0hm&p_p_lifecycle=0&p_p_col_id=column-1&p_p_col_count=1&_101_INSTANCE_ls3TZlzsK0hm_struts_action=%2Fasset_publisher%2Fview_content&_101_INSTANCE_ls3TZlzsK0hm_assetEntryId=14262570&_101_INSTANCE_ls3TZlzsK0hm_type=document> ;
	qb:structure agcom:rilevazioni_pluralismo ;
	dct:subject
		sdmx-subject:1.10 ,      # Political and other community activities
		sdmx-subject:1.11 ,      # Time use
		sdmx-subject:3.3.3; 	 # Information society
	agcom:refPeriod <http://reference.data.gov.uk/id/gregorian-month/2019-02> ;
	dct:publisher  resource:AGCOM 
.

# following data generated by a csv derived from dataset source

<?php

/*
 * This functopn translate the name of a AGCOM subject into token id
 * that is expected to be defined in the agcom:soggetti taxonomy 
 */
function strToAgcomId($str) {
    $str = trim($str);
    $str = str_replace('+ Europa', 'PiuEuropa', $str);
    $str = str_replace([' ', "'"], '_', $str);
    $str = str_replace('__', '_', $str);
    $str = str_replace('_-', '-', $str);
    $str = str_replace('-_', '-', $str);
    return $str;
}

function seconds($str_time) {
    sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
    return $hours*3600+$minutes*60+$seconds; 
}

//skip header
fgets(STDIN);

$count=1;$labels=array();
while ($rawdata = fgetcsv(STDIN)) {
    preg_match('/(.+) \((.+)\)/', $rawdata[2], $matches);
    $subject = strToAgcomId($matches[1]);
    $role = strToAgcomId($matches[2]);
    $context = strToAgcomId($rawdata[1]). '_programma';
    
    $speakingTime = seconds($rawdata[3]);

    assert( $subject && $role && $context && $speakingTime);
    
    $labels[$subject] = $matches[1];
    $labels[$role] = $matches[2];
    $labels[$context] = $rawdata[1];
    
    echo "
resource:ds_2019_02_$count a qb:Observation;
    rdfs:comment \"Tempo di parola di ${rawdata[2]} in ${rawdata[1]} a febbraio 2019\"@it ;
    qb:dataSet resource:ds_2019_02 ;
    agcom:subject resource:${subject} ;
    agcom:role resource:${role} ;
    agcom:context resource:${context} ;
    agcom:speakingTime $speakingTime
."; 
    $count++;
}

// Serializza le labels associate ai concetti
echo "\n\n# Concept labels: \n\n";
foreach( $labels as $token => $label) echo "resource:${token} skos:prefLabel \"$label\"@it .\n";

