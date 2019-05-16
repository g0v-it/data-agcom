APIs for g0v AGCOM visualizer
===========================================

These APIs query RDF graph database containing data according with [BubbleGraph Ontology](http://linkeddata.center/lodmap-bgo/v1) and produces
a json structure suitable to be used with  LODMAP2D Bubble Graph application.

## usage

### GET /accounts

returns all bubbles

e.g. https://data.agcom.g0v.it/ldp/accounts

```json
{  
   "title":"Politica in TV dal 2019-04-29 al 2019-05-05",
   "description":"La dimensione di ogni bolla è proporzionale al numero di frasi ascoltate giornalmente dagli spettatori di uno specifico programma televisivo e pronunciate da persone con un ruolo politico o istituzionale.",
   "source":"http://agcom.linkeddata.cloud/resource/266b4f7eece85b6fef9d382fc62590eb",
   "um":"TV impressions",
   "partitionOrderedList":[  
      "default",
      "p1_soggetto",
      "p2_ruolo",
      "p3_emittente",
      "p4_editore"
   ],
   "partitionScheme":{  
      "default":{  
         "title":"tutto",
         "partitions":[  

         ]
      },
      "p4_editore":{  
         "title":"editore",
         "partitions":[  
            ....
            {  
               "label":"Mediaset",
               "partitionAmount":1170599958.8285716
            },
            {  
               "label":"Rai",
               "partitionAmount":1252260570.3142858
            }
         ]
      },
      "p3_emittente":{  
         "title":"emittente",
         "partitions":[  
            ...
            {  
               "label":"Rai 2",
               "partitionAmount":152003951.31428573
            },
            {  
               "label":"Canale 5",
               "partitionAmount":813579856.9428571
            }
         ]
      },
      "p2_ruolo":{  
         "title":"ruolo",
         "partitions":[
            ...
            {  
               "label":"Partito_Comunista",
               "partitionAmount":30457391.057142857
            },
            {  
               "label":"+Europa",
               "partitionAmount":47971611.571428575
            }
         ]
      },
      "p1_soggetto":{  
         "title":"soggetto",
         "partitions":[
            ...
            {  
               "label":"Sergio Mattarella",
               "partitionAmount":203737501.4857143
            },
            {  
               "label":"Nicola Zingaretti",
               "partitionAmount":375534924.7142858
            }
         ]
      }
   },
   "accounts":[  
      ....
      {  
         "code":"0fa5d2ef6e58270313c9d4f082871b65",
         "title":"Nicola Zingaretti (Partito Democratico)",
         "amount":5625974.8,
         "previousValue":4298497.6,
         "subject":"STUDIOAPERTO_programma - 2019-05-06",
         "background":"https://upload.wikimedia.org/wikipedia/commons/thumb/b/b2/Nicola_Zingaretti_2012_crop.jpg/255px-Nicola_Zingaretti_2012_crop.jpg",
         "partitionLabel":[  
            "Italia 1",
            "Mediaset",
            "Nicola Zingaretti",
            "Partito Democratico"
         ]
      }
   ]
}
```

### GET /account/{id}


returns data a bubble detail with history:

e.g. https://data.agcom.g0v.it/ldp/account/0fa5d2ef6e58270313c9d4f082871b65

```json
{  
   "code":"0fa5d2ef6e58270313c9d4f082871b65",
   "amount":5625974.8,
   "previousValue":4298497.6,
   "background":"https://upload.wikimedia.org/wikipedia/commons/thumb/b/b2/Nicola_Zingaretti_2012_crop.jpg/255px-Nicola_Zingaretti_2012_crop.jpg",
   "version":"2019-05-06",
   "source":"http://agcom.linkeddata.cloud/resource/266b4f7eece85b6fef9d382fc62590eb_p87_r8",
   "title":"Nicola Zingaretti (Partito Democratico)",
   "subject":"STUDIOAPERTO_programma - 2019-05-06",
   "description":"Rilevazione AGCOM del tempo di parola di ZINGARETTI NICOLA nel ruolo di PARTITO DEMOCRATICO su STUDIOAPERTO nel periodo 2019-04-29T00:00:00/P7D",
   "partitionLabel":[  
      "Italia 1",
      "Mediaset",
      "Nicola Zingaretti",
      "Partito Democratico"
   ],
   "isVersionOf":[  
      {  
         "version":"2019-04-08",
         "amount":3476726
      },
      {  
         "version":"2019-04-01",
         "amount":7522370.8
      },
      {  
         "version":"2019-04-29",
         "amount":4298497.6
      },
      {  
         "version":"2019-04-22",
         "amount":3160660
      },
      {  
         "version":"2019-03-01",
         "amount":3784890.35
      }
   ],
   "hasPart":[  

   ],
   "um":"TV impressions"
}
```

### GET /filter/{filtersid}

It calculates partitions total taking into account a filter in {filtersid}.

{filtersid}  is a string that is compressed with zlib an encoded with base64 that contains 
required filter.

e.g.: https://data.agcom.g0v.it/ldp/filter/N4IgDgjA%2Bgzg9gcwQUwC6riAXAbRAYQEMAnYtVQqAETgFtkA7ASwGNMBdAGnACYpiArnAA2mXF3ABmKMlpN0jVMmw4JYACwyAJvLhkV7AL5A 

```json
{  
   "p1_soggetto":{  
      "Carretta_Domenico":58283.2
   },
   "p2_ruolo":{  
      "Partito Democratico":58283.2
   },
   "p3_emittente":{  
      "SKYTG24 (emittente)":58283.2
   },
   "p4_editore":{  
      "Sky Italia":58283.2
   }
}
```
### GET /

Returns some internal information about this interface (not to be used)


## Test

To test api server you need:

- run the data store and api server provider 
- populate the graph database with example data (see how in [sdaas component](../sdaas/README.md))
- test api using a browser or any api client pointing it to:


```
curl http://localhost:8080/
```

Roadmap
-------

This interface will soon be migrated to  [Linked Data Platform](https://www.w3.org/TR/ldp-primer/) standard interface compatible with  [Solid standards](https://github.com/solid/solid#standards-used)


