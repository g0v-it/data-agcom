<?php
echo "# File created by agcom gateway by LinkedData.Center
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix sdmx-subject:    <http://purl.org/linked-data/sdmx/2009/subject#> .
@prefix agcom: <http://agcom.linkeddata.cloud/resource/> .
@prefix : <#>
";


echo "
:ds a qb:DataSet ;
    dct:subject
        sdmx-subject:1.10 ,      # Political and other community activities
        sdmx-subject:1.11 ,      # Time use
        sdmx-subject:3.3.3; 	 # Information society
    :refPeriod <http://reference.data.gov.uk/id/gregorian-month/2019-02> ;
    dct:publisher   :AGCOM;
.	
echo ";