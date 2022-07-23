
/**
 * Class to contain methods related to loading ontologies into the annotator.
 */
class OntologyLoader {

    /**
     * Loads the Ontology from the terms4FAIRskills Github
     * (https://github.com/terms4fairskills/FAIRterminology).
     */
    static loadFromGitHub() {
        // set the url input to the raw github url
        document.getElementById("ontology-url-input").value = 
            "https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl";
        OntologyLoader.loadFromURL();
    }

    /**
     * Loads the ontology from a URL.
     */
    static loadFromURL() {
        var data = "{\"ontology-url-input\": \"" + document.getElementById("ontology-url-input").value + "\"}"

        // hide the form and show the loading spinner
        T4FSAnnotator.hideElement("load-ontology-container");
        T4FSAnnotator.showElement("loading-spinner-container");

        fetch("/api/loadOntology",
            {
                method: "POST",
                body: data
            })
            .then(T4FSAnnotator.checkStatus)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                const ontologyID = data.ontologyID;

                T4FSAnnotator.hideElement("loading-spinner-container");
                T4FSAnnotator.showElement("main-menu-container");

                document.getElementById("ontology-name-span").innerHTML = ontologyID;
                T4FSAnnotator.setCookie("annotator-ontology-id", ontologyID, 7); 
                    // set the cookie for the ontology id
            })
            .catch(err => {
                console.log(err);
                MainMenu.setup(); // reset which elements are shown
                document.getElementById("owl-selection-error").innerHTML = err;
            });
    }

    /**
     * Loads the ontology from the uploaded file.
     */
    static loadFromFile() {

    }

}