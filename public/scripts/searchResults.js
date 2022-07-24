

class SearchResults {

    /**
     * Creates the HTML for the term search results.
     * 
     * @param {JSON} response The JSON data of the results
     * @param {Array} selectedTerms The list of already selected terms - defaults to an empty array
     * @returns {String} the HTML string of the search results
     */
    static create(response, selectedTerms) {
        // create the JSON object from the string and initalise the html variable
        var html = "";

        // container to hold the search and result details
        html += '<div class="container d-flex flex-column justify-content-left p-0 m-0" id="results-details">';

        html += '<p class="text-secondary p-0 m-0">Searched for: "' + response.search + '"</p>';        
        html += '<p class="text-secondary p-0 m-0">Results returned: ' + response.results.length + '</p>';

        html += '</div>';
        
        // container to hold the results
        html += '<div class="container d-flex flex-column justify-content-center gap-3 p-0 m-0" id="results">';

        // for each object in the JSON array
        response.results.forEach(term => {
            // create the container with the term's details
            html += TermSearchResult.create(term, selectedTerms);
        });

        html += '</div>';

        return html;
    }

}