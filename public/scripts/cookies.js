
/**
 * Class to manage the setting, getting and deleting of cookies.
 */
class Cookies {

    /**
     * Sets a cookie.
     * 
     * @param {String} name the name of the cookie
     * @param {String} value the value of the cookie
     * @param {int} days the number of days before the cookie expires, defaults to 7
     */
    static set(name, value, days = 7) {
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
    static delete(name) {
        Cookies.setCookie(name, "");
    }

    /**
     * Returns the value of a cookie.
     * 
     * @param {String} name the name of the cookie
     * @returns {String} the cookie value
     */
    static get(name) {
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