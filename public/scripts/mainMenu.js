
/**
 * Class to contain all the methods related to the main menu.
 */
class MainMenu {


    /**
     * Shows the main menu.
     */
    static show() {
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


}