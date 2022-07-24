
/**
 * Class to contain methods that control what is shown to the user.
 */
class ViewManager {

    static ids = [
        "main-menu-container",
        "ontology-selector-container",
        "annotator-container",
        "loading-spinner-container"
    ]

    /**
     * Shows an element with display: flex;
     * 
     * @param {String} id the id of the element to show
     */
    static showElement(id) {
        var element = document.getElementById(id);
        element.classList.add("d-flex");
        element.classList.remove("d-none");
    }

    /**
     * Hides an element with display: none;
     * 
     * @param {String} id the id of the element to hide
     */
    static hideElement(id) {
        var element = document.getElementById(id);
        element.classList.add("d-none");
        element.classList.remove("d-flex");
    }



    /**
     * Hides all the elements of the annotator.
     */
    static hideAll() {
        ViewManager.ids.forEach(id => {
            ViewManager.hideElement(id);
        });
    }


    /**
     * Shows the annotator main menu.
     */
    static showMainMenu() {
        ViewManager.hideAll();
        ViewManager.showElement("main-menu-container");
    }

    /**
     * Shows the ontology selector.
     */
    static showOntologySelector() {
        ViewManager.hideAll();
        ViewManager.showElement("ontology-selector-container");
    }

    /**
     * Shows the annotator.
     */
    static showAnnotator() {
        ViewManager.hideAll();
        ViewManager.showElement("annotator-container");
    }




    /**
     * Shows the loading spinner.
     */
     static showLoadingSpinner() {
        ViewManager.hideAll();
        ViewManager.showElement("loading-spinner-container");
    }



}