
/**
 * Class to hold general methods for the Annotator.
 */
class T4FSAnnotator {

    /**
     * Initalises the annotator.
     */
    static initialise() {
        Debugger.log("Initialising annotator");

        // show the loading spinner while initialising the annotator
        ViewManager.showLoadingSpinner();

        // enable debugging if the debug paramter is present
        if (T4FSAnnotator.urlHasParameter("debug")) {
            Debugger.enable();
        }

        // create the user if not already set
        if (Cookies.get("annotator-user-id") === "") {
            // create the user
            APIRequest.fetch(
                "/api/user/create",
                function(data) {
                    const userID = data.userID;
                    Cookies.set("annotator-user-id", userID);
                    Debugger.log("User ID set - " + userID);
                    T4FSAnnotator.showPage();
                },
                function() {
                    // there was an issue creating the user - refresh the page
                    location.reload();
                }
            )
        } else {
            // user id cookie already set - show the correct page
            Debugger.log("UserID already set, showing correct page");
            T4FSAnnotator.showPage();
        }
        

        
    }


    /**
     * Shows the correct page after the annotator has been intialised.
     */
    static showPage() {
        // load the correct part of the annotator depending on the cookies set
        if (Cookies.get("annotator-ontology-id") === "") {
            // there is not an ontology loaded - show the ontology selector
            Debugger.log("No ontology loaded - loading ontology selector");
            OntologySelector.show();
        } else if (Cookies.get("annotator-resource-id") === "") {
            // there is an ontology loaded but no resource selected
            Debugger.log("No resource selected - loading main menu");
            MainMenu.show();
        } else {
            // there is an ontology loaded and a resource selected
            Debugger.log("Loading annotator");
            Annotator.show();
        }
    }

    /**
     * Adds word breaks into URLs to prevent overflow issues with longer URLs.
     * 
     * @param {String} url the URL to insert word breaks into
     * @returns the URL with the word breaks
     */
    static breakURL(url) {
        // Split the URL into an array to distinguish double slashes from single slashes
        var doubleSlash = url.split('//');
    
        // Format the strings on either side of double slashes separately
        var formatted = doubleSlash.map(str =>
            // Insert a word break opportunity after a colon
            str.replace(/(?<after>:)/giu, '$1<wbr>')
            // Before a single slash, tilde, period, comma, hyphen, underline, question mark, number sign, or percent symbol
            .replace(/(?<before>[/~.,\-_?#%])/giu, '<wbr>$1')
            // Before and after an equals sign or ampersand
            .replace(/(?<beforeAndAfter>[=&])/giu, '<wbr>$1<wbr>')
            // Reconnect the strings with word break opportunities after double slashes
        ).join('//<wbr>');
    
        return formatted;
    }


    /**
     * Checks if a given paramter is present in the URL query string.
     * 
     * @param {String} parameter the parameter name to check for
     * @returns {boolean} returns true if the paramter is present, false otherwise
     */
    static urlHasParameter(parameter) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        return urlParams.has(parameter)
    }



    /**
     * Creates a 32 bit hash of the given string.
     * 
     * 
     * @param {String} string the string to hash
     * @returns {String} the hash of the string
     */
    static hashString(string) {
        var hash = 0;
          
        if (string.length == 0) {
            // if the string is empty - the hash is 0
            return hash;
        }
          
        for (let i = 0; i < string.length; i++) {
            // for each character in the string
            const char = string.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }

        if (hash < 0) {
            // if the hash is negative, make it positive
            hash *= -1;
        }
          
        return hash;
    }



    /**
     * Generates a hexidecimal string of given length.
     * 
     * @param {int} size the length of the hex string, defaults to 10
     * @returns {String} the hex string
     */
    static generateHexString(size = 10) {
        return [...Array(size)].map(
            () => Math.floor(Math.random() * 16).toString(16)
        ).join('');
    }



    /**
     * Convert a HTML form to a JSON object.
     * 
     * @param {HTMLElement} form the form HTML object
     * @returns {JSON} a JSON object storing the form data
     */
    static formToJSON(form) {
        let output = {};
        new FormData(form).forEach((value, key) => {
            // Check if property already exist in the JSON data
            if ( Object.prototype.hasOwnProperty.call(output, key)) {
                let current = output[key];
                if (!(Array.isArray(current))) {
                    // If it's not an array, convert it to an array.
                    current = output[key] = [current];
                }
                current.push(value); // Add the new value to the array.
            } else {
                output[key] = value;
            }
        });
        return JSON.stringify(output);
    }




    /**
     * Downlaods a JSON object as a .json file.
     * 
     * @param {JSON} exportObj the JSON object to download
     * @param {String} exportName the filename of the download
     */
    static downloadObjectAsJson(exportObj, exportName) {
        // https://stackoverflow.com/a/30800715
        var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportObj));
        var downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href",     dataStr);
        downloadAnchorNode.setAttribute("download", exportName + ".json");
        document.body.appendChild(downloadAnchorNode); // required for firefox
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
    }



    // === Timestamp to Clock conversion ===
    
    /**
     * Converts a unix timestamp to hh:mm format.
     * 
     * @param {String} timestamp the unix timestamp to get as a string
     * @returns {String} the timestamp in hh:mm format
     */
    static timestampToString(timestamp) {
        // https://stackoverflow.com/a/847196

        // Create a new JavaScript Date object based on the timestamp
        // multiplied by 1000 so that the argument is in milliseconds, not seconds.
        var date = new Date(timestamp * 1000);
        // Hours part from the timestamp
        var hours = ("0" + date.getHours()).slice(-2); // get the last two digits
        // Minutes part from the timestamp
        var minutes = ("0" + date.getMinutes()).slice(-2); // get the last two digits

        return hours + ':' + minutes;

    }


    /**
     * Converts a timestamp to YYYY-MM-DD format.
     * 
     * @param {String} timestamp the unix timestamp to convert
     * @returns {String} the date in YYYY-MM-DD format
     */
    static timestampToYear(timestamp) {
        // Create a new JavaScript Date object based on the timestamp
        // multiplied by 1000 so that the argument is in milliseconds, not seconds.
        var date = new Date(timestamp * 1000);
        // day part from the timestamp
        var day = ("0" + date.getDate()).slice(-2); // get the last two digits of the day number
        // day part from the timestamp
        var month = ("0" + (date.getMonth() + 1)).slice(-2); // get the last two digits of the month
        // day part from the timestamp
        var year = date.getFullYear();

        return year + "-" + month + "-" + day;
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
            throw new Error(response.statusText);
        }
        return response;
    }


}

