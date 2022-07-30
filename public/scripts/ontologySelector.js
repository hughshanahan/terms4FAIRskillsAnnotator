
/**
 * Class to contain methods related to loading ontologies into the annotator.
 */
class OntologySelector {


    // variable to store the userID of the user when the ontology selector was shown
    static setupUserID = "";

    /**
     * Shows the ontology selector.
     */
    static show() {
        // store the userID that was selected when the ontology selector was shown
        OntologySelector.setupUserID = Cookies.get("annotator-user-id");
        // set up the ontology selector
        ViewManager.showLoadingSpinner();
        OntologySelector.getUserOntologiesList();
        document.getElementById("ontology-url-input").value = "";
        ViewManager.showOntologySelector();
    }



    /**
     * Loads the ontology ID into the cookie and shows the main menu.
     * 
     * @param {String} id the Ontology ID 
     */
    static loadOntology(id) {
        // set the cookie for the ontology id
        Cookies.set("annotator-ontology-id", id, 7);
        // show the main menu
        MainMenu.show();
    }


    /**
     * Loads the Ontology from the terms4FAIRskills Github
     * (https://github.com/terms4fairskills/FAIRterminology).
     */
    static loadFromGitHub() {
        // set the url input to the raw github url
        Debugger.log("Loading the terms4FAIRskills ontology");
        document.getElementById("ontology-url-input").value = 
            "https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl";
        OntologySelector.loadFromURL();
    }

    /**
     * Loads the ontology from a URL.
     */
    static loadFromURL() {
        // show the loading spinner
        ViewManager.showLoadingSpinner();

        // get the ontology URL from the input
        const ontologyURL = document.getElementById("ontology-url-input").value;

        Debugger.log("Loading from " + ontologyURL);

        const userID = Cookies.get("annotator-user-id");

        // load the ontology
        APIRequest.fetch(
            "/api/loadOntology?userID=" + userID + "&url=" + ontologyURL,
            function(data) {
                OntologySelector.loadOntology(data.ontologyID);
            },
            function() {
                OntologySelector.show();
            }
        );
    }


    /**
     * Loads an ontology from its database ID.
     * 
     * @param {String} id the Ontology database ID
     */
    static loadFromID(id) {
        OntologySelector.loadOntology(id);
    }


    /**
     * Deletes an ontology.
     * 
     * @param {String} id the ontology's database id
     * @param {String} uri the ontologies URI
     * @param {String} url the URL the ontology was loaded from
     */
    static deleteOntology(id, uri, url) {

        // check that the loaded ontology hasn't changed
        if (OntologySelector.setupUserID === Cookies.get("annotator-user-id")) {

            var confirmed = confirm("Are you sure you want to delete " + uri + " (from " + url + ")?");
            if (confirmed) {
                // the user has confirmed that they want to delete the ontology

                APIRequest.fetch(
                    "/api/deleteOntology?ontologyID=" + id,
                    function(data) {
                        OntologySelector.getUserOntologiesList();
                    },
                    function() {
                        OntologySelector.show();
                    }
                );

            }
        } else {
            ModalController.showError(
                "<p>The loaded user has changed</p>"
            );
        }


        
    }





    /**
     * Hides the resources list and shows the loading resources spinner.
     */
     static showUserOntologiesSpinner() {
        // hide the list and show the spinner
        Debugger.log("Showing user ontologies spinner");
        ViewManager.hideElement("user-ontologies-container");
        ViewManager.showElement("user-ontologies-spinner-container");
    }

    /**
     * Hides the loading resources spinner and shows the resources list.
     */
    static showUserOntologiesList() {
        // hide the spinner and show the list
        Debugger.log("Showing user ontologies list form");
        ViewManager.hideElement("user-ontologies-spinner-container");
        ViewManager.showElement("user-ontologies-container");
    }


    /**
     * Gets the list of resources that have been annotated using the ontology and the shows the menu.
     */
    static getUserOntologiesList() {
        // check that the loaded user hasn't changed
        if (OntologySelector.setupUserID === Cookies.get("annotator-user-id")) {

            // hide the form and show the spinner
            OntologySelector.showUserOntologiesSpinner();

            document.getElementById("user-ontologies-container").innerHTML = "";
            const userID = Cookies.get("annotator-user-id");
            APIRequest.fetch(
                "/api/getUserOntologies?userID=" + userID,
                function(data) {
                    var html = '';
                    if (data.length > 0) {
                        // there are user ontologies
                        html += OntologySelector.createUserOntologiesTable(data);
                    } else {
                        html += "<p>You have not loaded any ontologies...</p>";
                    }
                    document.getElementById("user-ontologies-container").innerHTML = html;
                    OntologySelector.showUserOntologiesList();
                }
            );
        } else {
            ModalController.showError(
                "<p>The loaded user has changed</p>"
            );
        }
    }

    /**
     * Creates a table of the resource data.
     * 
     * @param {JSON} ontologies the ontologies data from the API
     */
    static createUserOntologiesTable(ontologies) {
        var html = '';

        // create the grid heading
        html += '<div class="container">';
        html += '<div class="row">'; // no margin on the header row
        html += '<div class="col align-self-center">';
        html += '<p><strong>URI</strong></p>';
        html += '</div>';
        html += '<div class="col align-self-center">';
        html += '<p><strong>Loaded From</strong></p>';
        html += '</div>';
        html += '<div class="col align-self-center">';
        html += '</div>';
        html += '</div>';

        // for each resource
        ontologies.forEach(ontology => {
            html += '<div class="row my-1">';
            html += '<div class="col align-self-center">';
            html += '<p>' + T4FSAnnotator.breakURL(ontology.about) + '</p>';
            html += '</div>';
            html += '<div class="col align-self-center">';
            html += '<p>' + T4FSAnnotator.breakURL(ontology.url) + '</p>';
            html += '</div>';

            html += '<div class="col align-self-center">';
            // create the container to hold the options buttons
            html += '<div class="container d-flex flex-row justify-content-center gap-3 p-0 w-100">';
            html += '<button type="button" class="btn btn-primary flex-fill" onclick="OntologySelector.loadFromID(\'' + ontology.id + '\')">Load</button>';
            const deleteOnClick = 'OntologySelector.deleteOntology(\'' + ontology.id + '\', \'' + ontology.about + '\', \'' + ontology.url + '\')';
            html += '<button type="button" class="btn btn-danger flex-fill" onclick="' + deleteOnClick + '">Delete</button>';
            html += '</div>';

            html += '</div>';
            html += '</div>';
        });
        
        // close the grid
        html += '</div>';

        return html;
    }

}