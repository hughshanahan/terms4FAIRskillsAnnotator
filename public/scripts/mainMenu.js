
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
        console.log("Showing main menu");
        ViewManager.showMainMenu();
    }


    /**
     * Hides the main menu and navigates back to the ontology selector.
     */
    static hide() {
        OntologySelector.show();
    }

    /**
     * Clears the currently loaded ontology.
     */
    static changeOntology() {
        Cookies.delete("annotator-ontology-id");
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


}