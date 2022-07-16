
/**
 * Class to contain all the methods related to the main menu.
 */
class MainMenu {

    /**
     * Sets up the main menu.
     */
    static setup() {
        const ontologyID = T4FSAnnotator.getCookie("annotator-ontology-id");
        if (ontologyID === "") {
            // there is not an ontology stored in the cookie - show the load ontology form
            T4FSAnnotator.showElement("load-ontology-container");
            T4FSAnnotator.hideElement("loading-spinner-container");
            T4FSAnnotator.hideElement("main-menu-container");
        } else {
            // there is an ontology loaded - show the main menu
            T4FSAnnotator.hideElement("load-ontology-container");
            T4FSAnnotator.hideElement("loading-spinner-container");
            T4FSAnnotator.showElement("main-menu-container");
            document.getElementById("ontology-name-span").innerHTML = ontologyID;
        }
        
    }

    /**
     * Reads the content from the select ontology form and sends it to the backend.
     */
    static loadOntology() {
        var form = document.getElementById("ontology-select-form");
        var data = T4FSAnnotator.formToJSON(form);

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


    static removeOntology() {
        T4FSAnnotator.deleteCookie("annotator-ontology-id");
        MainMenu.setup();
    }


}