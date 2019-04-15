//get filter

module.exports = (first, second, third, fourth, groupBy) => {
	return ({
		query : `
		PREFIX dct: <http://purl.org/dc/terms/> 
		PREFIX bgo: <http://linkeddata.center/lodmap-bgo/v1#>
		PREFIX accounts: <https://data.agcom.g0v.it/ldp/accounts#>
		PREFIX resource: <http://agcom.linkeddata.cloud/resource/>
		PREFIX skos: <http://www.w3.org/2004/02/skos/core#>

		SELECT ?${groupBy} (SUM (?account_amount) AS ?amount)
		WHERE {
		  {
		  	SELECT DISTINCT ?accountUri ?p1_soggetto ?p2_ruolo ?p3_emittente ?p4_editore ?account_amount 
		    WHERE  {
		      ?accountUri a bgo:Account ;
		                bgo:amount ?account_amount ;
		                bgo:partitionLabel ?p1_soggetto,?p2_ruolo ,?p3_emittente, ?p4_editore.
		  	
		      accounts:soggetto bgo:partition ?soggetto .
		      accounts:ruolo bgo:partition ?ruolo .
		      accounts:emittente bgo:partition ?emittente .
		      accounts:editore bgo:partition ?editore .

		      ?soggetto bgo:label ?p1_soggetto .
		      ?ruolo bgo:label ?p2_ruolo .
		      ?emittente bgo:label ?p3_emittente .
		      ?editore bgo:label ?p4_editore .
		    }
		  }
		  
		  FILTER regex(?p1_soggetto, "${first}")
		  FILTER regex(?p2_ruolo , "${second}")
		  FILTER regex(?p3_emittente , "${third}")
		  FILTER regex(?p4_editore , "${fourth}")
		} GROUP BY ?${groupBy} ORDER BY (?amount)
		`
})
}

