
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
     * @param {String} termLabel The URI of the selected term
     */
    static addToSelectedTerms(termLabel) {
        var selectedInput = document.getElementById("selected-terms");
        if (selectedInput.value == "") {
            selectedInput.value += termLabel;
        } else {
            selectedInput.value += "," + termLabel;
        }

        
        Annotator.refreshDynamicUI();
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



    // === Selected Terms UI Methods ===

    /**
     * Aggregates all the refresh dynamic UI calls into one call.
     * This ensures that all the dynamic UI elements are updated together.
     */
    static refreshDynamicUI() {
        Annotator.refreshSelectedUI();
    }



    /**
     * Refreshes the list of terms that the user can see based on the list stored in the hidden input.
     * This ensures that the list that will be processed by the backend always matches what is shown to the user.
     * This needs to be run after any change to the selected terms input value.
     */
    static refreshSelectedUI() {
        var html = "";

        // get the terms
        var selectedInput = document.getElementById("selected-terms");
        var terms = selectedInput.value.split(',');

        // for each term
        terms.forEach(term => {
            html += Annotator.createSelectedTermContainer(term);
        });

        // update the inner HTML of the selected terms container
        var selectedContainer = document.getElementById("selected-terms-container");
        selectedContainer.innerHTML = html;

    }


    /**
     * Creates the container for selected term from the URI. 
     * 
     * @param {String} termURI The URI for the term
     * @returns {String} the HTML string for the term container
     */
    static createSelectedTermContainer(termURI) {
        var html = "";
        html += '<div class="container border border-secondary rounded p-3 m-0">';


        html += '<p>' + termURI + '</p>';


        html += '</div>';
        return html;
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
        var disabledAttribute = "";
        if (document.getElementById("selected-terms").value.split(",").includes(term.about)) {
            disabledAttribute = " disabled";
        }


        html += '<div class="col-3">';
        html += '<input id="addToSelectedButton" type="button" value="Add Term" '
            + 'onclick="Annotator.addToSelectedTerms(\'' + term.about + '\');" ' + disabledAttribute + '/>';
        html += '</div>';

        //close the grid
        html += '</div>';
        html += '</div>';

        return html;
    }


}