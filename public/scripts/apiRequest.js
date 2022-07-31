
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
            throw new Error();
        }
        return response;
    }




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