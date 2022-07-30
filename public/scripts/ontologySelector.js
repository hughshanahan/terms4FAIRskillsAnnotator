
/**
 * Class to contain methods related to loading ontologies into the annotator.
 */
class OntologySelector {


    /**
     * Shows the ontology selector.
     */
    static show() {
        ViewManager.showLoadingSpinner();
        document.getElementById("ontology-url-input").value = "";
        ViewManager.showOntologySelector();
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

        // load the ontology
        APIRequest.fetch(
            "/api/loadOntology?url=" + ontologyURL,
            function(data) {
                // set the cookie for the ontology id
                Cookies.set("annotator-ontology-id", data.ontologyID, 7);
                // show the main menu
                MainMenu.show();
            },
            function() {
                OntologySelector.show();
            }
        );
    }

}