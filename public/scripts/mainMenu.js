
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

            fetch("/api/getOntologyResources?ontology=" + Cookies.get("annotator-ontology-id"))
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    var html = '';

                    // create the grid heading
                    html += '<div class="container">';
                    html += '<div class="row">';
                    html += '<div class="col">';
                    html += '<p><strong>Name</strong></p>';
                    html += '</div>';
                    html += '<div class="col">';
                    html += '<p><strong>Author</strong></p>';
                    html += '</div>';
                    html += '<div class="col">';
                    html += '<p><strong>Terms</strong></p>';
                    html += '</div>';
                    html += '<div class="col">';
                    html += '</div>';
                    html += '</div>';

                    // for each resource
                    data.forEach(resource => {
                        html += '<div class="row">';
                        html += '<div class="col">';
                        html += '<p>' + resource.name + '</p>';
                        html += '</div>';
                        html += '<div class="col">';
                        html += '<p>' + resource.author + '</p>';
                        html += '</div>';
                        html += '<div class="col">';
                        html += '<p>' + resource.terms.length + '</p>';
                        html += '</div>';
                        html += '<div class="col">';
                        html += '<button type="button" class="btn btn-primary btn-lg btn-block" onclick="MainMenu.annotateExisting(\'' + resource.id + '\')">Annotate</button>';
                        html += '</div>';
                        html += '</div>';
                    });
                    
                    // close the grid
                    html += '</div>';

                    document.getElementById("annotated-resources").innerHTML = html;

                    console.log("Showing main menu");
                    ViewManager.showMainMenu();

                })
                .catch(err => console.log(err));

        } else {
            alert("Error: The loaded ontology has changed");
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
                .catch(err => console.log(err));

        } else {
            alert("Error: The loaded ontology has changed");
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

}