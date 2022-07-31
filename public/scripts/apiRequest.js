
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
        const encodedURL = encodeURI(url);
        APIRequest.getJSON(encodedURL)
            .then(data => {
                successCallback(data);
            })
            .catch(err => {
                // log the error to the console
                Debugger.log("Call to " + encodedURL + " retured an error:\n" + err);
                // run the failure callback
                failureCallback();
                // show the error in the modal view
                ModalController.showError(err);
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
            response.clone().json() // clone the response to convert to JSON again
                                    // if this is not done then JS produces an error
                .then(error => {
                    throw new Error(error.message);
                });
        }
        return response;
    }



    /**
     * Makes multiple requests to the API.
     * 
     * @param {array} urls An array of URLs to fetch from
     * @param {function} successCallback The callback function to run on each of the JSON data returned, takes one JSON parameter
     * @param {function} failureCallback The callback function to run if a request fails, takes no parameters
     * @param {function} afterCallBack The callback function to run when all of the requests have succeeded 
     */
    static fetchAll(
        urls,
        successCallback = (data) => {},
        failureCallback = () => {},
        afterCallBack = () => {}
    ) {
        // example used: https://stackoverflow.com/a/63370138

        // create the array of fetch requests
        let promiseArray = [];
        urls.forEach(url => {
            const encodedURL = encodeURI(url);
            promiseArray.push(
                APIRequest.getJSON(encodedURL)
            );
        })
        // process the promises
        Promise.all(promiseArray)
            .then(responses => {
                responses.map(data => {
                    successCallback(data); // call the success callback
                });          
            })
            .then(function() {
                afterCallBack(); // callback for after all responses have been processed
            })
            .catch(err => {
                ModalController.showError(err);
                failureCallback();
            });
    }




    /**
     * Gets the JSON response from the API.
     * 
     * @param {String} url the URL to get the JSON data from
     * @returns {Promise} the JSON data from the request
     */
    static getJSON(url) {
        return fetch(url)
            .then(APIRequest.checkStatus)
            .then(response => response.json())
            .then(data => {
                Debugger.log("Call to " + url + " returned:\n" + JSON.stringify(data, null, 4));
                return data;
            });
    }

}