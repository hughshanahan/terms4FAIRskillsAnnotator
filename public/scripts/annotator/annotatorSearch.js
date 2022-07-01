

class AnnotatorSearch extends TermsSearch {
    

    /**
     * Starts a search of the terms.
     * 
     * @param {String} searchTerm the term to search for
     */
    static search(searchTerm) {
        const searchEngine = new AnnotatorSearch();
        searchEngine.searchTerms(searchTerm);
    }
    




    /**
     * Creates the content for the term's container.
     * This overrides the method of the same name in the TermsSearch class.
     * 
     * @param {JSON} term the JSON object for the term
     * @returns {String} the HTML string for the term container's contents
     */
    createTermContainerContents(term) {
        var html = "";

        // start the grid
        html += '<div class="container">';
        html += '<div class="row">';

        // left column - this is the same as the terms search
        html += '<div class="col">';
        html += super.createTermContainerContents(term);
        html += '</div>';

        // right column - this contains the button to add the term to the annotation
        html += '<div class="col-3">';
        html += '<p>This is where the button will go</p>';
        html += '</div>';

        //close the grid
        html += '</div>';
        html += '</div>';

        return html;
    }


}