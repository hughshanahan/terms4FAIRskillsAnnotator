
/**
 * Class to contain all the methods related to the main menu.
 */
class MainMenu {

    // variable to store the ontology id that was loaded when the main menu was shown
    static setupOntologyID = "";

    /**
     * Shows the main menu.
     */
    static show() {
        // store the ontology ID when the main menu was shown
        MainMenu.setupOntologyID = Cookies.get("annotator-ontology-id");
        const ontologyID = MainMenu.setupOntologyID;
        // get the ontology details
        APIRequest.fetch(
            "/api/getOntologyDetails?ontologyID=" + ontologyID,
            function(data) {
                const html = "<h3>" + T4FSAnnotator.breakURL(data.about) + " Ontology</h3><p><i>(" + T4FSAnnotator.breakURL(data.url) + ")</i></p>";
                document.getElementById("ontology-details-container").innerHTML = html;
                MainMenu.getResourcesList();
                Debugger.log("Showing main menu");
                ViewManager.showMainMenu();
            }
        )
    }


    /**
     * Hides the main menu and navigates back to the ontology selector.
     */
    static hide() {
        Cookies.delete("annotator-ontology-id");
        OntologySelector.show();
    }


    /**
     * Shows the modal view with the details of the ontology.
     */
    static showOntologyDetails() {
        // show the modal view with the title and the loading spinner
        ModalController.showLoadingSpinner();
        ModalController.show("Ontology", '');

        // fetch the term details and set the modal content
        APIRequest.fetch(
            "/api/getOntologyDetails?ontologyID=" + Cookies.get("annotator-ontology-id"),
            function(data) {
                const html = MainMenu.createOntologyDetails(data);
                // show the content in the modal
                ModalController.showContent(html);
            }
        );
    }


    /**
     * Creates the contents of the ontology details container.
     * 
     * @param {JSON} ontology the ontology details JSON from the API
     * @returns {String} the HTML for the ontology details
     */
    static createOntologyDetails(ontology) {
        var html = '';
        html += '<div class="container d-flex flex-column justify-content-center p-0" id="ontology-details-container">';
        html += HTMLGenerator.createSingleValueDisplay("URI", ontology.about);
        html += HTMLGenerator.createSingleValueDisplay("Loaded from", ontology.url);
        html += HTMLGenerator.createSingleValueDisplay("Description", ontology.description);
        html += HTMLGenerator.createValueListDisplay("Creators", ontology.creators);
        html += HTMLGenerator.createValueListDisplay("Contributors", ontology.contributors);
        html += HTMLGenerator.createValueListDisplay("Comments", ontology.comments);
        html += HTMLGenerator.createSingleValueDisplay("License", ontology.license);
        html += '</div>';
        return html;
    }
    


    /**
     * Hides the resources list and shows the loading resources spinner.
     */
     static showLoadingResourcesSpinner() {
        // hide the list and show the spinner
        Debugger.log("Showing loading resources spinner");
        ViewManager.hideElement("annotated-resources-container");
        ViewManager.showElement("loading-resources-spinner-container");
    }

    /**
     * Hides the loading resources spinner and shows the resources list.
     */
    static showResourcesList() {
        // hide the spinner and show the list
        Debugger.log("Showing resources list form");
        ViewManager.hideElement("loading-resources-spinner-container");
        ViewManager.showElement("annotated-resources-container");
    }


    /**
     * Gets the list of resources that have been annotated using the ontology and the shows the menu.
     */
    static getResourcesList() {
        // check that the loaded ontology hasn't changed
        if (MainMenu.setupOntologyID === Cookies.get("annotator-ontology-id")) {


            // hide the form and show the spinner
            MainMenu.showLoadingResourcesSpinner();

            document.getElementById("annotated-resources-container").innerHTML = "";
            const ontologyID = Cookies.get("annotator-ontology-id");
            APIRequest.fetch(
                "/api/getOntologyResources?ontologyID=" + ontologyID,
                function(data) {
                    var html = '';
                    if (data.length === 0) {
                        // if there are no annotated resources
                        html += '<p class="text-center">No Annotated resources</p>';
                        html += '<button type="button" class="btn btn-primary btn-lg btn-block flex-fill" onclick="MainMenu.annotateNew()">Annotate New Resource</button>';
                    } else {
                        // there are annotated resources
                        html += MainMenu.createResourceTable(data);
                    }
                    document.getElementById("annotated-resources-container").innerHTML = html;
                    MainMenu.showResourcesList();
                }
            );
        } else {
            ModalController.showError(
                "<p>The loaded ontology has changed<br /><small>Annotator.sumbit()</small></p>"
            );
        }
    }

    /**
     * Creates a table of the resource data.
     * 
     * @param {JSON} data the resource data from the API
     */
    static createResourceTable(data) {
        var html = '';

        // create the grid heading
        html += '<div class="container">';
        html += '<div class="row">'; // no margin on the header row
        html += '<div class="col align-self-center">';
        html += '<p><strong>Name</strong></p>';
        html += '</div>';
        html += '<div class="col align-self-center">';
        html += '<p><strong>Author</strong></p>';
        html += '</div>';
        html += '<div class="col align-self-center">';
        html += '<p><strong>Terms</strong></p>';
        html += '</div>';
        html += '<div class="col align-self-center">';
        html += '</div>';
        html += '</div>';

        // for each resource
        data.forEach(resource => {
            html += '<div class="row my-1">';
            html += '<div class="col align-self-center">';
            html += '<p>' + resource.name + '</p>';
            html += '</div>';
            html += '<div class="col align-self-center">';
            html += '<p>' + resource.author + '</p>';
            html += '</div>';
            html += '<div class="col align-self-center">';
            html += '<p>' + resource.terms.length + '</p>';
            html += '</div>';
            html += '<div class="col align-self-center">';

            // create the container to hold the options buttons
            html += '<div class="container d-flex flex-row justify-content-center gap-3 p-0 w-100">';
            html += '<button type="button" class="btn btn-primary flex-fill" onclick="MainMenu.annotateExisting(\'' + resource.id + '\')">Edit</button>';
            html += '<button type="button" class="btn btn-danger flex-fill" onclick="MainMenu.deleteResource(\'' + resource.id + '\', \'' + resource.name + '\')">Delete</button>';
            html += '</div>';

            html += '</div>';
            html += '</div>';
        });
        
        // close the grid
        html += '</div>';

        return html;
    }


    /**
     * Exports the annotations in the format that the materials browser can import.
     */
    static export() {

        // check that the loaded ontology hasn't changed
        if (MainMenu.setupOntologyID === Cookies.get("annotator-ontology-id")) {

            APIRequest.fetch(
                "/api/exportAnnotations?ontologyID=" + Cookies.get("annotator-ontology-id"),
                function(data) {
                    T4FSAnnotator.downloadObjectAsJson(data, "materials");
                }
            );
        } else {
            ModalController.showError( 
                "<p>The loaded ontology has changed<br /><small>Annotator.export()</small></p>"
            );
        }
    }


    /**
     * Starts the annotator with an existing resource.
     * 
     * @param {String} resourceID the ID of the resource to annotate
     */
    static annotateExisting(resourceID) {
        Cookies.set("annotator-resource-id", resourceID);
        Annotator.show();
    }


    /**
     * Starts the annotator for a new resource.
     */
    static annotateNew() {
        Cookies.delete("annotator-resource-id");
        Annotator.show();
    }


    /**
     * Deletes a resource from the annotator.
     * 
     * @param {String} resourceID the ID of the resource to delete
     * @param {String} resourceName the name of the resource to delete
     */
    static deleteResource(resourceID, resourceName) {

        // check that the loaded ontology hasn't changed
        if (MainMenu.setupOntologyID === Cookies.get("annotator-ontology-id")) {

            var confirmed = confirm("Are you sure you want to delete " + resourceName + "?");
            if (confirmed) {
                // the user has confirmed that they want to delete the resource

                APIRequest.fetch(
                    "/api/deleteResource?resourceID=" + resourceID,
                    function(data) {
                        MainMenu.getResourcesList();
                    }
                );
            }
        } else {
            ModalController.showError(
                "<p>The loaded ontology has changed<br /><small>Annotator.deleteResource()</small></p>"
            );
        }


        
    }

}