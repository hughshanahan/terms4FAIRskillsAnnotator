
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

        if (content === "") {
            // if there was no content provided, show the loading spinner
            ModalController.showLoadingSpinner()
        } else {
            // else show the content
            ModalController.showContent(content);
        }

        // show the modal
        $("#modal-view").modal('show');
    }


    /**
     * Hides the modal view.
     */
    static hide() {
        // hide the modal
        $("#modal-view").modal('hide');
        // clear the old title and content
        document.getElementById("modal-title").innerHTML = '';
        document.getElementById("modal-content-container").innerHTML = '';
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


    /**
     * Shows the modal with an error message.
     * 
     * @param {String} error the error message
     */
    static showError(error) {

        var errorHTML = '';

        errorHTML += '<div class="d-flex flex-column justify-content-center w-100">';
        errorHTML += error;
        errorHTML += '</div>';

        // log the error to the console
        console.error(error);

        ModalController.show(
            "Error",
            errorHTML
        );

    }

}