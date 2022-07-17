
/**
 * Class to contain the methods required for the annotator.
 * 
 * This class extends TermsSearch to allow it to use the same methods to call the API to search the terms.
 */
class Annotator extends TermsSearch {

    /**
     * Sets up the annotator.
     */
    static setup() {
        const ontologyID = T4FSAnnotator.getCookie("annotator-ontology-id");
        if (ontologyID === "") {
            // there is not an ontology stored in the cookie - redirect to the main menu
            window.location.replace("/");
        }
    }


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
        var data = T4FSAnnotator.formToJSON(form);

        fetch("/api/saveResource",
            {
                method: "POST",
                body: data
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                T4FSAnnotator.downloadObjectAsJson(data.material, "materials");
                document.getElementById("last-saved-at").innerHTML = T4FSAnnotator.timestampToString(data.savedAt);
            })
            .catch(err => console.log(err));

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
                                selectedContainer.innerHTML += TermSearchResult.create(json, "annotator", terms);
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




    // === Annotator Search Methods ===

    /**
     * Starts a search of the terms.
     * 
     * @param {String} searchTerm the term to search for
     */
    static search(searchTerm) {
        var selectedInput = document.getElementById("selected-terms");
        var selectedTerms = selectedInput.value.split(',');
        TermsSearch.searchTerms(searchTerm, "annotator", selectedTerms);
    }

}