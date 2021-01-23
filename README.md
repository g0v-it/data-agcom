![copernicani](doc/copernicani-logo.png)

----------------
>
> **WARNING: this project is now pending. AGCOM decided to publish pluralism data with an OPEN DATA LICENSE**
> see the [press release](https://www.agcom.it/documentazione/documento?p_p_auth=fLw7zRht&p_p_id=101_INSTANCE_FnOw5lVOIXoE&p_p_lifecycle=0&p_p_col_id=column-1&p_p_col_count=1&_101_INSTANCE_FnOw5lVOIXoE_struts_action=%2Fasset_publisher%2Fview_content&_101_INSTANCE_FnOw5lVOIXoE_assetEntryId=21435119&_101_INSTANCE_FnOw5lVOIXoE_type=document)
> ** The the g0v team and LinkedData.Center are trying to learn more about the new data organization. Stay tuned.
>
-----------------

# g0v data-agcom

A *Smart Data Management Platform* to store information about
pluralism on television based on the data published by AGCOM (Agenzia per le Garanzie nelle Comunicazioni).

The knowledge base is compliant with RDF and Semantic Web Specification.

Applications can access the knowledge graph through a SPARQL interface.

**Reference implementations:**

- **SPARQL endpoint**: https://data.agcom.g0v.it/sparql
- **Linked Data browser**: http://data.agcom.g0v.it/welcome 
- **API endpoint**: https://data.agcom.g0v.it/ldp
- **Example of a data consumer**: https://agcom.g0v.it/

## Development

The project contains the two "core" logical components:

- **sdaas** (smart data as a service):  the data management platform providing an RDF store, a [SPARQL endpoint](https://www.w3.org/TR/sparql11-overview), a data ingestion engine, a set of gateways to transform raw data in linked data and a build script that populates the RDF store. See files and docs in [sdaas directory](sdaas)
- a set of **APIs** that query the SPARQL endpoint and produce json data with a schema suitable to be used with the LODMAP-2D BubbleGraph Component. See files and docs in [apis directory](apis)

Beside the core, two additional optional components may be needed to complete a production system:

- **LODVIEW** server: a linked data web browser to dereferencing URIS and navigate the Knowledge Graph;
- a **router** that provides a single access point to all services with firewall, caching and SSL features.

This picture shows the components interactions:

![architecture](doc/architecture.png)


The platform is shipped with a [Docker](https://docker.com) setup that makes it easy to get a containerized development environment up and running. 
If you do not already have Docker on your computer, 
[it's the right time to install it](https://docs.docker.com/install/).

To deploy the platform, a stack of some services is required:

![stack](doc/stack.png)

To start core services using [docker Compose](https://docs.docker.com/compose/) type: 

```
docker-compose build
docker-compose up -d
```

This starts locally the following services:


| Name        | Description                                                   | Port 
| ----------- | ------------------------------------------------------------- | ------- 
| sdaas       | a server that manages the datastore and the ingestion engine  | 29341    
| api         | a server that manages the web-budget API                      | 29342 

Try http://localhost:29341/sdaas to access blazegraph workbench
Try http://localhost:29342/ to test API endpoint

The first time you start the containers, Docker downloads and builds images for you. 
It will take some time, but don't worry this is done only once. 
Starting servers will then be lightning fast.



To shut down the platform type: 

```
docker-compose down
```

## Support

For answers, you may not find in here or in the Wiki, avoid posting issues. Feel free to ask for support on the [Slack](https://copernicani.slack.com/) general room. Make sure to mention **@enrico** so he is notified


## Credits

- the Smart Data Management Platform was developed by [LinkedData.Center](http://LinkedData.Center/)
- the ontologies were developed by Enrico Fagnoni @ [LinkedData.Center](http://LinkedData.Center/)
- the RDF data store and the SPARQL endpoint is based on the [Blazegraph community edition](https://www.blazegraph.com/)
- The URI dereferencing platform is derived from the [LODView project](https://github.com/dvcama/LodView)
- TV Audience data analysis was developed thanks to **Studio Frasi** (@StudioFrasi)

The online service and the reference implementation is hosted by the [Copernicani community](https://copernicani.it/).


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
