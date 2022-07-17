/**
 * Class to store methods related to the Terms Search.
 */
class TermsSearch {

    // variable to store the ontology id that was loaded when the terms search started
    static setupOntologyID = "";

    /**
     * Sets up the TermsSearch.
     */
    static setup() {
        const ontologyID = T4FSAnnotator.getCookie("annotator-ontology-id");
        if (ontologyID === "") {
            // there is not an ontology stored in the cookie - redirect to the main menu
            window.location.replace("/");
        }

        // there is an ontology loaded - save the id into the setupOntologyID variable
        TermsSearch.setupOntologyID = ontologyID;
    }

    /**
     * Starts a search of the terms.
     * 
     * @param {String} searchTerm the term to search for
     */
    static search(searchTerm) {

        // check that the loaded ontology hasn't changed
        if (TermsSearch.setupOntologyID === T4FSAnnotator.getCookie("annotator-ontology-id")) {
            
            TermsSearch.searchTerms(searchTerm); // just search for the term with the default settings

        } else {
            alert("Error: The loaded ontology has changed");
        }

    }

    /**
     * Searches for the given term in the current ontology.
     * 
     * @param {String} searchTerm the term to search for
     * @param {String} mode the mode to search for results - either "termsSearch" or "annotator", defaults to "termSearch"
     * @param {Array} selectedTerms the list of already selected terms, defaults to empty array
     */
    static searchTerms(searchTerm, mode="termsSearch", selectedTerms=[]) {

        // fetch the data
        fetch("/api/searchTerms?search=" + searchTerm, { method: 'get' })
            // then convert response to JSON object
            .then(response => response.json()) 
            // then process the data to get the list of fetch requests for fetching the relations
            .then(data => {
                // print the id of the first request response
                document.getElementById("results-container").innerHTML = SearchResults.create(data, mode, selectedTerms);
                console.log("Updated results");
            })
            .catch(err => console.log(err));

    }

}