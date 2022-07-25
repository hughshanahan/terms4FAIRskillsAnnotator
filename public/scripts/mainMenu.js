
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
        // set the ontology name
        document.getElementById("ontology-name-span").innerHTML = MainMenu.setupOntologyID;
        MainMenu.getResourcesList();
    }


    /**
     * Hides the main menu and navigates back to the ontology selector.
     */
    static hide() {
        Cookies.delete("annotator-ontology-id");
        OntologySelector.show();
    }


    /**
     * Gets the list of resources that have been annotated using the ontology and the shows the menu.
     */
    static getResourcesList() {
        // check that the loaded ontology hasn't changed
        if (MainMenu.setupOntologyID === Cookies.get("annotator-ontology-id")) {

            document.getElementById("annotated-resources").innerHTML = "";

            fetch("/api/getOntologyResources?ontology=" + Cookies.get("annotator-ontology-id"))
                .then(response => response.json())
                .then(data => {
                    console.log(data);

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
                        html += '<button type="button" class="btn btn-primary flex-fill" onclick="MainMenu.annotateExisting(\'' + resource.id + '\')">Annotate</button>';
                        html += '<button type="button" class="btn btn-danger flex-fill" onclick="MainMenu.deleteResource(\'' + resource.id + '\', \'' + resource.name + '\')">Delete</button>';
                        html += '</div>';

                        html += '</div>';
                        html += '</div>';
                    });
                    
                    // close the grid
                    html += '</div>';

                    document.getElementById("annotated-resources").innerHTML = html;

                    console.log("Showing main menu");
                    ViewManager.showMainMenu();

                })
                .catch(err => {
                    ModalController.show(
                        "Error", 
                        "An error occured getting the annotated resources: " + err
                    );
                });

        } else {
            ModalController.show(
                "Error", 
                "<p>The loaded ontology has changed<br /><small>Annotator.sumbit()</small></p>"
            );
        }
    }

    /**
     * Exports the annotations in the format that the materials browser can import.
     */
    static export() {

        // check that the loaded ontology hasn't changed
        if (MainMenu.setupOntologyID === Cookies.get("annotator-ontology-id")) {

            fetch("/api/exportAnnotations?ontology=" + Cookies.get("annotator-ontology-id"))
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    T4FSAnnotator.downloadObjectAsJson(data, "materials");
                })
                .catch(err => {
                    ModalController.show(
                        "Error", 
                        "An error occured getting export file: " + err
                    );
                });

        } else {
            ModalController.show(
                "Error", 
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

                fetch("/api/deleteResource?id=" + resourceID)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        MainMenu.getResourcesList();
                    })
                    .catch(err => {
                        ModalController.show(
                            "Error", 
                            "An error occured while deleting the resource: " + err 
                        )
                    });

            }
        } else {
            ModalController.show(
                "Error", 
                "<p>The loaded ontology has changed<br /><small>Annotator.deleteResource()</small></p>"
            );
        }


        
    }

}