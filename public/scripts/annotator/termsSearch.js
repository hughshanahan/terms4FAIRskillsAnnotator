/**
 * Class to store methods related to the Terms Search.
 */
class TermsSearch {

    /**
     * Starts a search of the terms.
     * 
     * @param {String} searchTerm the term to search for
     */
    static search(searchTerm) {
        TermsSearch.searchTerms(searchTerm); // just search for the term with the default settings
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