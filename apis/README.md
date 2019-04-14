APIs for g0v AGCOM visualizer
===========================================

These APIs query an RDF graph database containing data according with [BubbleGraph Ontology](http://linkeddata.center/lodmap-bgo/v1) and produces
a json structure suitable to be used with a http://agcom.g0v.it/ application.

## usage

### GET /accounts

returns data in format suitable to be used with LODMAP2D Bubble Graph component:

```json

```



### GET /account/{id}


returns data in format suitable to be used with LODMAP2D Bubble AccountDetails component:

```json

```


### GET /filter/{filtersid}

It calculates partitions total taking into account a filter in {filtersid}.

{filtersid}  is a string that is compressed with zlib an encoded with base64 that contains required filter in the form of (here sintax) e.g. `here example` producing something like:

```json
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


