

class SearchResults {

    /**
     * Creates the HTML for the term search results.
     * 
     * @param {JSON} response The JSON data of the results
     * @param {String} mode The mode to display the results - termSearch or annotator - defaults to termsSearch
     * @param {Array} selectedTerms The list of already selected terms - defaults to an empty array
     * @returns {String} the HTML string of the search results
     */
    static create(response, mode="termsSearch", selectedTerms=[]) {
        var searchResults = new SearchResults(response, mode, selectedTerms);
        return searchResults.createResults();
    }


    /**
     * Constructs a SearchTerms object.
     * 
     * @param {JSON} response The JSON data of the results
     * @param {String} mode The mode to display the results - termSearch or annotator - defaults to termsSearch
     * @param {Array} selectedTerms The list of already selected terms - defaults to an empty array 
     */
    constructor(response, mode, selectedTerms) {
        this.response = response;
        this.mode = mode;
        this.selectedTerms = selectedTerms;
    }


    /**
     * Returns the HTML string for the search results.
     * 
     * @returns {String} the HTML string of the search results
     */
    createResults() {
        // create the JSON object from the string and initalise the html variable
        var html = "";

        // container to hold the search and result details
        html += '<div class="container d-flex flex-column justify-content-left p-0 m-0" id="results-details">';

        html += '<p class="text-secondary p-0 m-0">Searched for: "' + this.response.search + '"</p>';        
        html += '<p class="text-secondary p-0 m-0">Results returned: ' + this.response.results.length + '</p>';

        html += '</div>';
        
        // container to hold the results
        html += '<div class="container d-flex flex-column justify-content-center gap-3 p-0 m-0" id="results">';

        // for each object in the JSON array
        this.response.results.forEach(term => {
            // create the container with the term's details
            html += TermSearchResult.create(term, this.mode, this.selectedTerms);
        });

        html += '</div>';

        return html;
    }

}