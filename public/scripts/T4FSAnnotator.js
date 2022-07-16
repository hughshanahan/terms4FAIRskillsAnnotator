
/**
 * Class to hold general methods for the Annotator.
 */
class T4FSAnnotator {

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
     * @param {int} timestamp the unix timestamp to get as a string
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
     * Sets a cookie.
     * 
     * @param {String} name the name of the cookie
     * @param {String} value the value of the cookie
     * @param {int} days the number of days before the cookie expires, defaults to 7
     */
    static setCookie(name, value, days = 7) {
        const d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    /**
     * Deletes the cookie.
     * 
     * @param {String} name the name of the cookie
     */
    static deleteCookie(name) {
        T4FSAnnotator.setCookie(name, "");
    }

    /**
     * Returns the value of a cookie.
     * 
     * @param {String} name the name of the cookie
     * @returns {String} the cookie value
     */
    static getCookie(name) {
        let nameKey = name + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(nameKey) == 0) {
                return c.substring(nameKey.length, c.length);
            }
        }
        return "";
    }

}

