/**
 * Class to store methods related to the Terms Search.
 */
class TermsSearch {
    
    
    /**
     * Searches for the given term in the current ontology.
     * 
     * @param {String} searchTerm the term to search for
     */
    static search(searchTerm) {

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("results-container").innerHTML = TermsSearch.createContainers(this.responseText);
            }
        };
        xhttp.open("GET", "/api/searchTerms?search=" + searchTerm, true);
        xhttp.send();

    }


    /**
     * Formats the data returned from the API into HTML Containers.
     * 
     * @param {String} jsonString the JSONString from the API
     * @returns {String} the HTML for the response from the API
     */
    static createContainers(jsonString) {
        // create the JSON object from the string and initalise the html variable
        const jsonData = JSON.parse(jsonString);
        var html = "";

        // container to hold the search and result details
        html += '<div class="container d-flex flex-column justify-content-left p-0 m-0" id="results-details">';

        html += '<p class="text-secondary p-0 m-0">Searched for: "' + jsonData.search + '"</p>';        
        html += '<p class="text-secondary p-0 m-0">Results returned: ' + jsonData.results.length + '</p>';

        html += '</div>';
        
        // container to hold the results
        html += '<div class="container d-flex flex-column justify-content-center gap-3 p-0 m-0" id="results">';

        // for each object in the JSON array
        const results = jsonData.results;
        for (let i = 0; i < results.length; ++i) {
            const term = results[i];

            // create the container with the term's details
            html += TermsSearch.createTermContainer(term)
        }

        html += '</div>';

        return html;
    }


    /**
     * Creates the container for the term.
     * 
     * @param {JSON} term the JSON object for the term
     * @returns {String} the HTML string for the term's container
     */
    static createTermContainer(term) {
        var html = "";

        html += '<div class="container border border-secondary rounded p-3 m-0 d-flex flex-row justify-content-between">';

        html += '<p class="text-primary p-0 m-0"><b>' + term.label + '</b></p>';
        html += '<p class="text-secondary p-0 m-0"><i>(' + term.about + ')</i></p>';
    
        html += '</div>';

        return html;
    }

}