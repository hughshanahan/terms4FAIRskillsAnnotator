
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
     * @param {String} termToAdd The URI of the selected term
     */
    static addToSelectedTerms(termToAdd) {
        var selectedInput = document.getElementById("selected-terms");

        if (!(selectedInput.value.split(",").includes(termToAdd))) {
            // the term is not already in the list
            if (selectedInput.value == "") {
                selectedInput.value += termToAdd;
            } else {
                selectedInput.value += "," + termToAdd;
            }

            Annotator.refreshDynamicUI(); // only update the UI if something has changed
        }
    }

    /**
     * Removes a term from the list of selected terms.
     * 
     * @param {String} termToRemove The URI of the selected term
     */
     static removeFromSelectedTerms(termToRemove) {
        var terms = document.getElementById("selected-terms").value.split(',');
        var newTerms = [];

        // for each term
        terms.forEach(term => {
            if (!(term === termToRemove)) {
                // the term is not the term to remove - add it to the new terms list
                newTerms.push(term);
            }
        });

        // clear the existing list
        document.getElementById("selected-terms").value = "";

        // add the new list
        document.getElementById("selected-terms").value = newTerms.toString();
        
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
        Annotator.refreshTermsCounter();
    }



    /**
     * Refreshes the list of terms that the user can see based on the list stored in the hidden input.
     * This ensures that the list that will be processed by the backend always matches what is shown to the user.
     * This needs to be run after any change to the selected terms input value.
     */
    static refreshSelectedUI() {
        // example used: https://stackoverflow.com/a/63370138

        // get the terms
        var selectedInput = document.getElementById("selected-terms");
        var terms = selectedInput.value.split(',');

        // get the container to place the selected container in
        var selectedContainer = document.getElementById("selected-terms-container");

        if (terms[0] === "") {
            // there are no terms as the input string was empty - clear the container
            selectedContainer.innerHTML = "";
        } else {
            // there are terms - process them
            let promiseArray = [];
            for(let i=0; i<terms.length; i++){
                promiseArray.push(fetch("/api/getTerm?term=" + terms[i]));
            }

            // proces the promises
            Promise.all(promiseArray)
                .then(responses => {
                    // process the responses
                    selectedContainer.innerHTML = "";
                    responses.map(response => {
                        // convert the response to JSON object and process it
                        response.json()
                            .then(json => {
                                selectedContainer.innerHTML += Annotator.createSelectedTermContainer(json);
                            });
                    });
                })
                .catch(err => console.log(err));
        }

    }


    /**
     * Refreshes the counter for the number of terms that have been selected.
     */
    static refreshTermsCounter() {
        var selectedInput = document.getElementById("selected-terms");
        var terms = selectedInput.value.split(',');

        var count = "0"; // default to no terms

        if (!(terms[0] === "")) {
            // the input contains terms - update with the length
            count = terms.length;
        }

        document.getElementById("terms-count").innerHTML = count;
        
    }


    /**
     * Creates the container for selected term from the Term JSON. 
     * 
     * @param {JSON} term The JSON data for the term
     * @returns {String} the HTML string for the term container
     */
    static createSelectedTermContainer(term) {
        var html = "";
        html += '<div class="container border border-secondary rounded p-3 m-0">';

        // start the grid
        html += '<div class="container">';
        html += '<div class="row">';

        // left column - this is the same as the terms search
        html += '<div class="col">';
        html += '<p>' + term.label + '</p>';
        html += '</div>';

        // right column - this contains the button to add the term to the annotation
        html += '<div class="col-3 d-flex flex-column justify-content-center">';
        html += '<button type="button" class="btn btn-danger" id="selectedRemoveFromSelectedButton" '
            + 'onclick="Annotator.removeFromSelectedTerms(\'' + term.about + '\');" />Remove</button>';
        html += '</div>';

        //close the grid
        html += '</div>';
        html += '</div>';

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
        html += '<div class="col-3 d-flex flex-column justify-content-center">';


        var selectedTerms = document.getElementById("selected-terms").value.split(",");
        if (selectedTerms.includes(term.about)) {
            // the term is in the selected list - add the remove button
            html += '<button type="button" class="btn btn-danger" id="addToSelectedButton" '
                + 'onclick="Annotator.removeFromSelectedTerms(\'' + term.about + '\');" />Remove</button>';

        } else {
            // the term is not already in the selected list - add the add button
            html += '<button type="button" class="btn btn-success" id="removeFromSelectedButton" '
                + 'onclick="Annotator.addToSelectedTerms(\'' + term.about + '\');" />Add</button>';

        }

        html += '</div>';

        //close the grid
        html += '</div>';
        html += '</div>';

        return html;
    }


}