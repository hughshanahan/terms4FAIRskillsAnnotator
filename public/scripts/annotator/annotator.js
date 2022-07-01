
/**
 * Class to contain the methods required for the annotator.
 * 
 * This class extends TermsSearch to allow it to use the same methods to call the API to search the terms.
 */
class Annotator extends TermsSearch {
    

    // === Annotator Form Methods ===

    /**
     * Adds a term to the list of selected terms.
     * 
     * @param {String} termLabel The label of the term to add to the selected terms list
     */
    static addToSelectedTerms(termLabel) {
        var selectedContainer = document.getElementById("selected-terms-container");
        var selectedInput = document.getElementById("selected-terms");

        selectedContainer.innerHTML += "<p>" + termLabel + "</p>";

        selectedInput.value += termLabel + ",";
    }


    /**
     * Submits the form to create the output file.
     */
    static submit() {
        var form = document.getElementById("annotator-form");

        // for testing - print the terms that have been selected to the console
        var terms = document.getElementById("selected-terms").value.split(',');
        terms.forEach(term => {
            console.log(term);
        });

    }



    // === Annotator Search Methods ===

    /**
     * Starts a search of the terms.
     * 
     * @param {String} searchTerm the term to search for
     */
    static search(searchTerm) {
        const searchEngine = new Annotator();
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
        html += '<input id="addToSelectedButton" type="button" value="Add Term" onclick="Annotator.addToSelectedTerms(\'' + term.label + '\');" />';
        html += '</div>';

        //close the grid
        html += '</div>';
        html += '</div>';

        return html;
    }


}