
/**
 * Class to contain the methods required for the annotator.
 */
class Annotator {

    // variable to store the ontology id that was loaded when the annotator started
    static setupOntologyID = "";

    // variable to store the array of selected terms
    static selectedTerms = [];
    // variable to store the array of removed terms
    static removedTerms = [];

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
        Debugger.log("Showing saving spinner");
        ViewManager.hideElement("annotator-form-container");
        ViewManager.showElement("form-saving-spinner-container");
    }

    /**
     * Hides the saving spinner and shows the annotator form.
     */
    static showAnnotatorForm() {
        // hide the spinner and show the form
        Debugger.log("Showing annotator form");
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
            "date-year-input"
        ];
        inputs.forEach(id => {
            document.getElementById(id).value = "";
        });
        document.getElementById("search-box").value = "";

        document.getElementById("last-saved-at").innerHTML = "Never";

        Annotator.selectedTerms = [];
        Annotator.removedTerms = [];

        Annotator.refreshDynamicUI();
    }


    /**
     * Updates the values in the form from the api.
     * 
     * @param {String} resourceID The ID of the resource to get
     */
    static getResourceDetails(resourceID) {
        APIRequest.fetch(
            "/api/getResource?resourceID=" + resourceID,
            function(data) {
                Annotator.setInputs(data);
                // the details of the form have been updated - ensure that the form is shown
                ViewManager.showAnnotator();
            }  
        );
    }


    /**
     * Set the inputs to the annotator to the values from the API and refresh the UI.
     * 
     * @param {JSON} data the resource data from the API
     */
    static setInputs(data) {
        document.getElementById("identifier-input").value = data.identifier;
        document.getElementById("name-input").value = data.name;

        // process the author name into the firstname and surname inputs
        const author = data.author.split(", ");
        document.getElementById("author-firstname-input").value = author[1];
        document.getElementById("author-surname-input").value = author[0];

        // Process the date
        const date = data.date.split("-");
        document.getElementById("date-day-input").value = date[2];
        document.getElementById("date-month-input").value = date[1];
        document.getElementById("date-year-input").value = date[0];

        // update the saved at time
        Annotator.updateSavedAt(data.savedAt);

        // Process the terms
        Annotator.selectedTerms = data.terms;
        Annotator.removedTerms = [];

        // refresh the UI
        Annotator.refreshDynamicUI();
    }


    // === Annotator Form Methods ===

    /**
     * Adds a term to the list of selected terms.
     * 
     * @param {String} termToAdd The URI of the selected term
     */
    static addToSelectedTerms(termToAdd) {
        // filter the removed terms list for the term to add
        Annotator.removedTerms = Annotator.removedTerms.filter(function(x) {
            return x !== termToAdd;
        });
        // add the selected term to the list of terms to add
        Annotator.selectedTerms.push(termToAdd);
        // refresh the UI
        Annotator.refreshDynamicUI();
    }

    /**
     * Removes a term from the list of selected terms.
     * 
     * @param {String} termToRemove The URI of the selected term
     */
     static removeFromSelectedTerms(termToRemove) {
        // filter the selected terms list for the term to remove
        Annotator.selectedTerms = Annotator.selectedTerms.filter(function(x) {
            return x !== termToRemove;
        });
        // add the removed term to the list of terms to remove
        Annotator.removedTerms.push(termToRemove);
        // refresh the UI
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

            // select the api endpoint to use depending on if the cookie has been set
            // and the id for the ontology or resource depending on which the api needs
            var url = "";
            var idParameter = "";
            if (Cookies.get("annotator-resource-id") === "") {
                // the resource id cookie has not been set, therefore create a new resource
                url = "/api/createResource";
                // the resource doesn't already exist - give the ontology id
                idParameter = "ontologyID=" + Cookies.get("annotator-ontology-id");
            } else {
                // cookie has been set, therefore save the changes
                url = "/api/saveResource";
                // the resource already exists - give the resource ID
                idParameter = "resourceID=" + Cookies.get("annotator-resource-id");
            }

            // create the query string of the form data
            const queryString = Annotator.createQueryString();

            APIRequest.fetch(
                url + "?" + idParameter + "&" + queryString,
                function(data) {
                    const resourceID = data.resourceID;
                    // store the resource id in the cookie - this is redundant for saving changes but is needed for the first save
                    Cookies.set("annotator-resource-id", resourceID);

                    // Remove the unselected ones and add the terms to the resource
                    var resourceTermsURLs = [];
                    Annotator.removedTerms.forEach(term => {
                        resourceTermsURLs.push("/api/removeResourceTerm?resourceID=" + resourceID + "&term=" + term);
                    });
                    Annotator.selectedTerms.forEach(term => {
                        resourceTermsURLs.push("/api/addResourceTerm?resourceID=" + resourceID + "&term=" + term);
                    });

                    APIRequest.fetchAll(
                        resourceTermsURLs,
                        (data) => {Debugger.log(data);},
                        function() {},
                        function () {
                            // update the text for when the resource was last saved
                            Annotator.updateSavedAt(data.savedAt)
                            // data saved, show the form again
                            Annotator.showAnnotatorForm();
                        }
                    );
                }
            );       

        } else {
            ModalController.showError(
                "<p>The loaded ontology has changed<br /><small>Annotator.sumbit()</small></p>"
            );
        }
    }


    /**
     * Creates the query string of the annotator form data for the API.
     * This contains the identifier, name, author and the date.
     * 
     * @returns {String} The query string
     */
    static createQueryString() {
        var string = "";
        // add the identifier
        string += "identifier=" + document.getElementById("identifier-input").value;
        string += "&";
        // add the name
        string += "name=" + document.getElementById("name-input").value;
        string += "&";
        // add the author name
        string += "author=" + document.getElementById("author-surname-input").value;
        string += ", " + document.getElementById("author-firstname-input").value;
        string += "&";
        // add the date
        string += "date=" + document.getElementById("date-year-input").value;
        string += "-" + document.getElementById("date-month-input").value;
        string += "-" + document.getElementById("date-day-input").value;
        return string;
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

        var terms = Annotator.selectedTerms;

        // get the container to place the selected container in
        var selectedContainer = document.getElementById("selected-terms-container");


        if (terms.length === 0) {
            // there are no terms as the input string was empty - clear the container
            selectedContainer.innerHTML = "";
        } else {
            // there are terms - process them
            var urls = [];
            terms.forEach(term => {
                urls.push("/api/getTerm?ontologyID=" + Cookies.get("annotator-ontology-id") + "&term=" + term);
            })
            selectedContainer.innerHTML = "";
            APIRequest.fetchAll(
                urls,
                function(data) {
                    selectedContainer.innerHTML += TermSearchResult.create(data, terms);
                }
            );
        }

    }


    /**
     * Refreshes the counter for the number of terms that have been selected.
     */
    static refreshTermsCounter() {
        document.getElementById("terms-count").innerHTML = Annotator.selectedTerms.length;
    }



    /**
     * Updates the last saved at text.
     * 
     * @param {int} timestamp the Unix timestamp of the saved at time
     */
    static updateSavedAt(timestamp) {
        document.getElementById("last-saved-at").innerHTML = 
            T4FSAnnotator.timestampToString(timestamp) + " on " +  T4FSAnnotator.timestampToYear(timestamp);
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
                // fetch the terms that match the search
                APIRequest.fetch(
                    "/api/searchTerms?ontologyID=" + Cookies.get("annotator-ontology-id") + "&search=" + searchTerm,
                    function(data) {
                        document.getElementById("results-container").innerHTML = SearchResults.create(data, Annotator.selectedTerms);
                    }
                );

            } else {
                ModalController.showError(
                    "<p>The loaded ontology has changed<br /><small>Annotator.search()</small></p>"
                );
            }
        } else {
            // clear the container
            document.getElementById("results-container").innerHTML = "";
        }

        
    }

}