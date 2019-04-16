APIs for g0v AGCOM visualizer
===========================================

These APIs query RDF graph database containing data according with [BubbleGraph Ontology](http://linkeddata.center/lodmap-bgo/v1) and produces
a json structure suitable to be used with  LODMAP2D Bubble Graph application.

## usage

### GET /accounts

returns all bubbles


### GET /account/{id}


returns data a bubble detail with history:


### GET /filter/{filtersid}

It calculates partitions total taking into account a filter in {filtersid}.

{filtersid}  is a string that is compressed with zlib an encoded with base64 that contains 
required filter.

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


