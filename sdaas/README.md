# agcom-data knowledge Base

This directory contains all is needed for setting up a formal RDF knowledge base to analyze 
the historical presence of politicians in the main TV shows in Italy.

The data ingestion process is managed by the [LinkedData.Center SDaaS platform community edition](https://github.com/linkeddatacenter/sdaas-ce) according [KEES](http://linkeddata.center/kees) specifications. 

## Data sources

Following data sources are considered:

- AGCOM open data 
- AUDITEL public data
- Wikidata
- LODMAP configuration data
- KEES configuration data
- Reference periods from UK services


### AGCOM raw data

AGCOM collects periodic records about the presence of politicians in main TV shows.

Source raw datasets are published in the [AGCOM web site](https://www.agcom.it/).
See [this page as example](https://www.agcom.it/documentazione/documento?p_p_auth=fLw7zRht&p_p_id=101_INSTANCE_ls3TZlzsK0hm&p_p_lifecycle=0&p_p_col_id=column-1&p_p_col_count=1&_101_INSTANCE_ls3TZlzsK0hm_struts_action=%2Fasset_publisher%2Fview_content&_101_INSTANCE_ls3TZlzsK0hm_assetEntryId=14262570&_101_INSTANCE_ls3TZlzsK0hm_type=document)

Waiting for open data publishing, the data are manually extracted form the AGCOM PDF reports and stored in the  [data directory](data)

### AUDITEL open data

AUDITEL is a private consortium that collects data about italian TV shows audience. It publishes some
aggregated data in its [web site](http://www.auditel.it/dati/) as a pdf table.

This project uses the  [2018 Sintesi Annuale 2018 file](http://www.auditel.it/media/filer_public/40/ad/40ad8125-d438-4dc9-af36-19c5320e436f/sintesi_annuale_2018.pdf)
to estimate the audience of the broadcaster networks refereed in AGCOM data. The data are manually extracted 
form the AUDITEL site and stored in the  [data/2018_auditel.ttl file](data/2018_auditel.ttl)

### Wikidata 

Picture and descriptions about polictical individuals, TV shows, networks and editors are extracted from the wikidata SPARQL endpoint.

### LODMAP data visualization configuration

The [LODMAP Bubble Graph Ontology](https://github.com/linkeddatacenter/LODMAP-ontologies/tree/master/v1/bgo) 
is used to describe the graphical objects that represent the presence of politics in TV.
  
Configuration data are contained in [data/agcom-strings.json file](data/agcom-strings.json) and  [data/g0v_app.ttl file](data/0v_app.ttl). Configuration information are intended to be used with any [LODMAP-2D compliant application](https://it.linkeddata.center/p/lodmap2d) such as [g0v.it web-budget](https://github.com/g0v-it/web-budget)

### KEES configuration data

Tfhe [data/kees.ttl file](data/kees.ttl) contains meta data about knowledge base according [KEES](http://linkeddata.center/kees) specifications. 

### Reference periods

Reference periods are published as linked data by [GOV.UK team](http://reference.data.gov.uk/)


## Data semantic

Raw data are annotated according with [agcom vocabulary](https://g0v-it.github.io/ontologies/agcom) 
and [auditel vocabulary](https://g0v-it.github.io/ontologies/auditel) and that extends 
the [RDF Data Cube Vocabulary](https://www.w3.org/TR/vocab-data-cube/) 

Raw data are are translated to RDF turtle data stream through simple [PHP gateways](gateways) and
the resulting triples are stored in a RDF graph database.

##  Data visualization axioms

For each observation, a "normalized speak time" (nst) is calculated using the formula:

`nst := seconds_in(speakingTime)/days_in(refPeriod )` 

The speakingTime is the time that an individual subject with a specific political role, uses in a TV channel.
Only main news programs (TG) are considered, all other programa are consolidated in a general "extra tg" container for each broadcast channel.


The broadcast weight index (bwi) is a subjective rank related to an estimated audience of TV programs 
that is computed starting from the penetration data provided by AUDITEL according this a:

`bwi(program) = broadcast_network_audience(program) * program_relative_weight(program)`

broadcast_network_audience is the potential audience of the whole TV channel respect italian population.

program_relative_weight is an further index to weight the importance of a program in a tv chennel (default=1)

Each AGCOM observation can be displayed as a bubble whose area is determined by following formula: 

`bubble_area(agcom observation) = nst * bwi`

Note that, because the broadcast weight index is subjective. Pure AGICOM data can be displayed just setting bwi(program) = 1 for each program.

In details, [bwi is computed by a specific sparql axiom](axioms/025_compute_bwi.sparql_update)


## Updating the knowledge base

knowledge base build process requires to:

- edit files in the *data* directory
- develop the *gateways* for transforming web resources in linked data. See [gateways doc.](gateways/README.md)
- write *axioms* and rules to generate new data. See [axioms doc.](axioms/README.md)
- edit the *build script* that drives the data ingestion process.
- run sdaas

### debugging the build script with docker

the test of the build script require at least 2GB of ram available to the docker machine:

```
docker run -d -p 9999:8080 -v $PWD/.:/workspace --name kb linkeddatacenter/sdaas-ce
docker exec -ti kb bash
apk --no-cache add php7
sdaas --debug -f build.sdaas --reboot
# Access the workbench pointing browser to http://localhost:9999/sdaas
exit
docker rm -f kb
```

logs info and debug traces will be created in .cache directory


 
### publishing  the knowledge base

You can pack data and services with :

```
docker build . -t sdaas
docker run -d -p 8889:8080 --name datastore sdaas
```

The resulting container will provide a read only distribution of the whole knowlede base in a stand-alone graph database with a SPARQL interface.


## Directory structure

- the **build.sdaas** file is a script to populate the knowledge base from scratch. It requires sdaas platform community edition 2.0+
- the **axioms** directory contains inferences to be computed during reasoning windows.
- the **data** directory contains local data files
- the **linked_data** directory contains constructor for linked data imported from external sparql end points
- the **gateways** directory contains the code to transform raw data in linked data
- the **tests** directory contains some axioms that must be verified on knowledge base building termination
  in order to validate the whole building process.
- the **.cache** temporary directory that contains logs and debugging info. Not saved in repo.


## Credits and license

- the sdaas platform is derived from [LinkedData.Center SDaas Product](https://it.linkeddata.center/p/sdaas) and licensed with CC-by-nd-nc by LinkedData.Center to g0v community
