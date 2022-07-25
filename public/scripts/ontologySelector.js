
/**
 * Class to contain methods related to loading ontologies into the annotator.
 */
class OntologySelector {


    /**
     * Shows the ontology selector.
     */
    static show() {
        ViewManager.showLoadingSpinner();
        ViewManager.showOntologySelector();
    }


    /**
     * Loads the Ontology from the terms4FAIRskills Github
     * (https://github.com/terms4fairskills/FAIRterminology).
     */
    static loadFromGitHub() {
        // set the url input to the raw github url
        document.getElementById("ontology-url-input").value = 
            "https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl";
        OntologySelector.loadFromURL();
    }

    /**
     * Loads the ontology from a URL.
     */
    static loadFromURL() {
        var data = "{\"ontology-url-input\": \"" + document.getElementById("ontology-url-input").value + "\"}"

        // show the loading spinner
        ViewManager.showLoadingSpinner();

        fetch("/api/loadOntology",
            {
                method: "POST",
                body: data
            })
            .then(T4FSAnnotator.checkStatus)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                // set the cookie for the ontology id
                Cookies.set("annotator-ontology-id", data.ontologyID, 7);
                // show the main menu
                MainMenu.show();
                    
            })
            .catch(err => {
                ModalController.show(
                    "Error", 
                    "An error occured while loading the ontology: " + err 
                )
            });
    }

}