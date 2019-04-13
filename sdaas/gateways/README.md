# gateways

This directory contains the gateways that transform the raw data provided by AGCOM into linked data according agcom-data project specification:

Gateways are simple stand-alone php7 scripts that read a csv stream row by row from STDIN and 
write RDF turtle statements on STDOUT .


## stand alone gateways development and testing


Gateways can be tested stand alone just with any host providing php7; e.g.:

```
docker run --rm -ti -v $PWD/.:/app -w /app php bash
php agcom.php  < tests/data/agcom.csv
```

The gateways must generate valid RDF statements in turtle (n3) or any other RDF serialization format. Check the gateway results using an online service like http://rdf-translator.appspot.com/
 
