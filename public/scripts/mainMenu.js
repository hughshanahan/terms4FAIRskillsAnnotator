
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
     * Clears the currently loaded ontology.
     */
    static removeOntology() {
        T4FSAnnotator.deleteCookie("annotator-ontology-id");
        MainMenu.setup();
    }


    /**
     * Redirects to the terms search.
     */
    static goToTermsSearch() {
        window.location.href = "/terms-search";
    }

    /**
     * Redirects to the annotator.
     */
    static goToAnnotator() {
        window.location.href = "/annotator";
    }

    /**
     * Redirects to the annotated resources list.
     */
    static goToResourcesList() {
        window.location.href = "/resources";
    }


}