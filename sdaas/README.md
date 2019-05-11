# agcom-data knowledge Base

This directory contains all is needed for setting up a formal RDF knowledge base to analyze 
the historical presence of politicians in the main TV shows in Italy.

The data ingestion process is managed by the [LinkedData.Center SDaaS platform community edition](https://github.com/linkeddatacenter/sdaas-ce) according [KEES](http://linkeddata.center/kees) specifications. 

## Data sources

Following data sources are considered:

- AGCOM open data 
- AUDITEL public data
- Linked Open Data from Camera dei deputati and Senato
- Linked Open Data from Wikidata
- Linked Open Data from UK services
- KEES configuration data
- LODMAP configuration data


### AGCOM raw data

AGCOM publishes in its [web site](https://www.agcom.it/) periodic reports about the presence of politicians in main TV shows.
See [this page as example](https://www.agcom.it/documentazione/documento?p_p_auth=fLw7zRht&p_p_id=101_INSTANCE_ls3TZlzsK0hm&p_p_lifecycle=0&p_p_col_id=column-1&p_p_col_count=1&_101_INSTANCE_ls3TZlzsK0hm_struts_action=%2Fasset_publisher%2Fview_content&_101_INSTANCE_ls3TZlzsK0hm_assetEntryId=14262570&_101_INSTANCE_ls3TZlzsK0hm_type=document)

AGCOM collects data about the speaking time of a person in a specific political or institutional role, detected during a 
specific TV show in a reference period. AGCOM reports distinguish between main news programs and other in-depth programs for journalistic publications.

For example, ISTAT collects the total speaking time in february 2019 of Matteo Salvini  with the institutional role of Goverment Ministry in the main news program (TG1) of the RAI 1 broadcast network. In the same period, the same subject, in the same tv show, can also speak with the political role of *Lega party* leader. In this case AGCOM produces two distinct records (i.e. observations).


### AUDITEL public data

AUDITEL is a private consortium that collects data about italian TV shows audience. It publishes some
aggregated data in its [web site](http://www.auditel.it/dati/) as a pdf table.

This project uses the  [2018 Sintesi Annuale 2018 file](http://www.auditel.it/media/filer_public/40/ad/40ad8125-d438-4dc9-af36-19c5320e436f/sintesi_annuale_2018.pdf)
to estimate the audience of the broadcaster networks refereed in AGCOM data. The data are manually extracted 
form the AUDITEL site and stored in the  [data/2018_auditel.ttl file](data/2018_auditel.ttl)

### Linked Open Data from Camera dei deputati and Senato

The name and picture of all Italian parliament members in the XVIII legislatura are extracted from
official SPARQL end points

### Linked Open Data from Wikidata

Provides pictures and descriptions about persons, TV shows, networks and editors.

### Linked Open Data from UK services

The great [UK reference linked data](http://reference.data.gov.uk/) are used to formally describe observation periods

### LODMAP data visualization application configuration

The [LODMAP Bubble Graph Ontology](https://github.com/linkeddatacenter/LODMAP-ontologies/tree/master/v1/bgo) 
is used to describe the graphical objects that represent the presence of politics in TV.
  
Configuration data are contained in [data/agcom-strings.json file](data/agcom-strings.json) and  [data/g0v_app.ttl file](data/0v_app.ttl). Configuration information are intended to be used with any [LODMAP-2D compliant application](https://it.linkeddata.center/p/lodmap2d) such as [g0v.it web-budget](https://github.com/g0v-it/web-budget)

### KEES configuration data

The [data/kees.ttl file](data/kees.ttl) contains meta data about knowledge base according [KEES](http://linkeddata.center/kees) specifications. 

### Reference periods

Reference periods are published as linked data by [GOV.UK team](http://reference.data.gov.uk/)


## Data semantic

Raw data are annotated according with [agcom vocabulary](https://g0v-it.github.io/ontologies/agcom) 
and [auditel vocabulary](https://g0v-it.github.io/ontologies/auditel)  that extend 
the [RDF Data Cube Vocabulary](https://www.w3.org/TR/vocab-data-cube/) 

Raw data are are translated to RDF turtle data stream through simple [PHP gateways](gateways) and
the resulting triples are stored in a RDF graph database.

## Data visualization axioms

For each AGCOM observation, a **normalized dailiy speaking time** (nst) is calculated using the formula `nst := seconds_in(speakingTime)/days_in(refPeriod )` 

For each AGCOM observation, the **broadcast weight index** (bwi) is a subjective rank related to an estimated audience of TV programs 
that is computed starting from the potential audience data provided by AUDITEL according with the formula `bwi(observation) = COALESCE( avg_audience(observation.context), avg_audience(observation.context.nework))` that considers the  potential audience of a specific tv program (e.g. Tg1) or of the whole TV channel (e.g. Rai 1).

For each AGCOM observation, the **normalized daily listening time** (dlt) is defined as `bwi(observation) * bwi(observation))`

There are some heuristics & guidelines that estimates that a speaker can pronunciate
an average rate of 100 - 125 words per minute. That is 2 words per second. Because an average sentence is composed by
10 words, we introduce a metric called **TV impressions** (tvi) computed by dividing by 5 the daily listening time. That is `bwi(observation) * nst(observation) / 5`
 
In other words, the  *TV impressions*  are just a rough estimation of the daily number of
sentences delivered to all potential TV watchers.

For example: a 30-second speech in a TV program in a day with an audience of 1000000 of people is 
equivalent to 6000000 of TV impressions (i.e. 30*1000000/5 ).

The metrics [tvi](axioms/025_compute_tvi.sparql_update), [bwi](axioms/025_compute_bwi.sparql_update) and 
[nst](axioms/024_compute_nst.sparql_update) are computed by a specific sparql axioms.


## Updating the knowledge base

knowledge base build process requires to:

- if needed, edit files in the *data* directory adding known linked data about facts.
-  if needed, develop the *gateways* for transforming web resources in linked data. See [gateways doc.](gateways/README.md)
-  if needed, write new *axioms* and rules to generate new data. See [axioms doc.](axioms/README.md)
- edit the *build script* that drives the data ingestion process. Add new resources to ingest
- run sdaas agent
- query the resulting knowledge base

### debugging the build script with docker

the test of the build script require at least 2GB of ram available to the docker machine:

```
docker run -d -p 9999:8080 -v $PWD/.:/workspace --name kb linkeddatacenter/sdaas-ce
docker exec -ti kb bash
apk --no-cache add php7 php7-mbstring
# run build process
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

The resulting container will provide a read only distribution of the whole knowlede base in a stand-alone read-only graph database with a SPARQL interface.


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
