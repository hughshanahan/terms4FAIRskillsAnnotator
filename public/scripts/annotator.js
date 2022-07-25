
/**
 * Class to contain the methods required for the annotator.
 */
class Annotator {

    // variable to store the ontology id that was loaded when the annotator started
    static setupOntologyID = "";

    /**
     * Sets up the annotator.
     */
    static show() {

        // log what the ontology was when the annotator was shown
        Annotator.setupOntologyID = Cookies.get("annotator-ontology-id");

        // show the loading spinner while the annotator is setup
        ViewManager.showLoadingSpinner();

        // show the form
        Annotator.showAnnotatorForm();
        Annotator.resetForm();


        const resourceID = Cookies.get("annotator-resource-id");
        if (!(resourceID === "")) {
            // there is a resource loaded - get the data and populate the inputs
            Annotator.getResourceDetails(resourceID);
        } else {
            // there is not a resource loaded
            // show the form
            ViewManager.showAnnotator();
        }

    }

    /**s
     * Hides the annotator and navigates back to the main menu.
     */
     static hide() {
        Cookies.delete("annotator-resource-id");
        MainMenu.show();
    }


    /**
     * Hides the form and shows the saving spinner.
     */
    static showSavingSpinner() {
        // hide the form and show the spinner
        console.log("Showing saving spinner");
        ViewManager.hideElement("annotator-form-container");
        ViewManager.showElement("form-saving-spinner-container");
    }

    /**
     * Hides the saving spinner and shows the annotator form.
     */
    static showAnnotatorForm() {
        // hide the spinner and show the form
        console.log("Showing annotator form");
        ViewManager.hideElement("form-saving-spinner-container");
        ViewManager.showElement("annotator-form-container");
    }


    /**
     * Resets the form elements.
     */
    static resetForm() {
        const inputs = [
            "identifier-input",
            "name-input",
            "author-firstname-input",
            "author-surname-input",
            "date-day-input",
            "date-month-input",
            "date-year-input",
            "selected-terms"
        ];
        inputs.forEach(id => {
            document.getElementById(id).value = "";
        });
        document.getElementById("search-box").value = "";
        Annotator.refreshDynamicUI();
    }


    /**
     * Updates the values in the form from the api.
     * 
     * @param {String} resourceID The ID of the resource to get
     */
    static getResourceDetails(resourceID) {
        fetch("/api/getResource?id=" + resourceID)
            .then(response => response.json())
            .then(data => {
                console.log(data);

                document.getElementById("identifier-input").value = data.identifier;
                document.getElementById("name-input").value = data.name;

                // process the author name into the firstname and surname inputs
                const author = data.author.split(", ");
                document.getElementById("author-firstname-input").value = author[1];
                document.getElementById("author-surname-input").value = author[0];

                // Process the date
                const date = T4FSAnnotator.timestampToYear(data.date).split("-");
                document.getElementById("date-day-input").value = date[2];
                document.getElementById("date-month-input").value = date[1];
                document.getElementById("date-year-input").value = date[0];

                // Process the terms
                const terms = data.terms.join(",");
                document.getElementById("selected-terms").value = terms;

                // refresh the UI
                Annotator.refreshDynamicUI();

                // the details of the form have been updated - ensure that the form is shown
                ViewManager.showAnnotator();

            })
            .catch(err => {
                ModalController.show(
                    "Error", 
                    "An error occured while getting the resoruce details: " + err 
                )
            });
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

        // for each term in the list of terms
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

        // check that the loaded ontology hasn't changed
        if (Annotator.setupOntologyID === Cookies.get("annotator-ontology-id")) {

            // hide the form and show the spinner
            Annotator.showSavingSpinner();

            // get the elements and data
            var form = document.getElementById("annotator-form");
            var data = T4FSAnnotator.formToJSON(form);

            // select the api endpoint to use depending on if the cookie has been set
            var url = "";
            if (Cookies.get("annotator-resource-id") === "") {
                // the resource id cookie has not been set, therefore create a new resource
                url = "/api/createResource";
            } else {
                // cookie has been set, therefore save the changes
                url = "/api/saveResource";
            }

            fetch(url,
                {
                    method: "POST",
                    body: data,
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    // store the resource id in the cookie - this is redundant for saving changes but is needed for the first save
                    Cookies.set("annotator-resource-id", data.resourceID);
                    // update the text for when the resource was last saved
                    document.getElementById("last-saved-at").innerHTML = T4FSAnnotator.timestampToString(data.savedAt) + " on " +  T4FSAnnotator.timestampToYear(data.savedAt);

                    // data saved, show the form again
                    Annotator.showAnnotatorForm();
                })
                .catch(err => {
                    ModalController.show(
                        "Error", 
                        "An error occured while saving the resource details: " + err 
                    )
                });

        } else {
            ModalController.show(
                "Error", 
                "<p>The loaded ontology has changed<br /><small>Annotator.sumbit()</small></p>"
            );
        }

        

    }



    // === Selected Terms UI Methods ===

    /**
     * Aggregates all the refresh dynamic UI calls into one call.
     * This ensures that all the dynamic UI elements are updated together.
     */
    static refreshDynamicUI() {
        Annotator.refreshSelectedUI();
        Annotator.refreshTermsCounter();

        // run the search again to update the add/remove buttons as they might have changed
        Annotator.search(
            document.getElementById("search-box").value
        );
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
                                selectedContainer.innerHTML += TermSearchResult.create(json, terms);
                            });
                    });
                })
                .catch(err => {
                    ModalController.show(
                        "Error", 
                        "An error occured while getting the term details: " + err 
                    );
                });
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

        if (!(searchTerm === "")) {
            // there is something to search for

            // check that the loaded ontology hasn't changed
            if (Annotator.setupOntologyID === Cookies.get("annotator-ontology-id")) {
                
                var selectedInput = document.getElementById("selected-terms");
                var selectedTerms = selectedInput.value.split(',');

                // fetch the data
                fetch("/api/searchTerms?search=" + searchTerm, { method: 'get' })
                    // then convert response to JSON object
                    .then(response => response.json()) 
                    // then process the data to get the list of fetch requests for fetching the relations
                    .then(data => {
                        // print the id of the first request response
                        document.getElementById("results-container").innerHTML = SearchResults.create(data, selectedTerms);
                        console.log("Updated results");
                    })
                    .catch(err => {
                        ModalController.show(
                            "Error", 
                            "An error occured while searching for terms: " + err 
                        )
                    });


            } else {
                ModalController.show(
                    "Error", 
                    "<p>The loaded ontology has changed<br /><small>Annotator.search()</small></p>"
                );
            }
        } else {
            // clear the container
            document.getElementById("results-container").innerHTML = "";
        }

        
    }

}