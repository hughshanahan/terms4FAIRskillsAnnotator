
/**
 * Class to handle API requests.
 */
class APIRequest {

    /**
     * Makes a request to the API.
     * 
     * @param {String} url the URL to request
     * @param {function} successCallback the function to run on success, takes one argument - the JSON object returned from the request
     * @param {function} failureCallback the function to run on failure - takes no arguments
     */
    static fetch(
        url,
        successCallback = (data) => {},
        failureCallback = () => {}
    ) {
        fetch(url)
            .then(APIRequest.checkStatus)
            .then(response => response.json())
            .then(data => {
                console.log("Call to " + url + " returned:\n" + JSON.stringify(data, null, 4));
                successCallback(data);
            })
            .catch(err => {
                // log the error to the console
                console.log("Call to " + url + " retured an error:\n" + err);
                // run the failure callback
                failureCallback();
                // show the error in the modal view
                ModalController.showError(
                    "An error occured while loading the ontology: " + err 
                );
            });
    }


    /**
     * Checks the status of the response was ok and returns it, 
     * otherwise throws exception to trigger the fetch catch.
     * 
     * @param {Response} response the response from the fetch request
     * @returns {Response} the response
     * @throws {Error} if the status of the response was not ok
     */
     static checkStatus(response) {
        if (!response.ok) {
            throw new Error();
        }
        return response;
    }


}