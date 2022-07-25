
/**
 * Class to control the modal view.
 */
class ModalController {

    /**
     * Shows the modal view.
     * 
     * @param {String} title the title for the modal
     * @param {String} content the HTML content for the modal, defaults to an empty string
     */
    static show(title, content = "") {

        // hide anty exisiting modal that is showing
        ModalController.hide();

        // set the title and content
        document.getElementById("modal-title").innerHTML = title;
        document.getElementById("modal-content-container").innerHTML = content;

        if (content === "") {
            // if there was no content provided, show the loading spinner
            ModalController.showLoadingSpinner()
        } else {
            // else show the content
            ModalController.showContent();
        }

        // show the modal
        $("#modal-view").modal('show');
    }


    /**
     * Hides the modal view.
     */
    static hide() {
        $("#modal-view").modal('hide');
    }


    /**
     * Shows the loading spinner and hides the content.
     */
    static showLoadingSpinner() {
        ViewManager.hideElement("modal-content-container");
        ViewManager.showElement("modal-loading-spinner-container");
    }

    /**
     * Shows the modal content and hides the loading spinner.
     * 
     * @param {String} content the HTML content for the modal
     */
    static showContent(content) {
        document.getElementById("modal-content-container").innerHTML = content;
        ViewManager.hideElement("modal-loading-spinner-container");
        ViewManager.showElement("modal-content-container");
    }


}