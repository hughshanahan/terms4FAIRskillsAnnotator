
/**
 * Class to manage debugging output.
 */
class Debugger {

    static enabled = false;

    /**
     * Turns the debugging output on.
     */
    static enable() {
        Debugger.enabled = true;
        console.log("Debugger enabled");
    }

    /**
     * Turns the debugging output off.
     */
    static disable() {
        Debugger.enabled = false;
        console.log("Debugger disabled");
    }

    /**
     * Log to the console, if the debugger is enabled
     * 
     * @param {String} output the content to log
     */
    static log(output) {
        if (Debugger.enabled) {
            console.log("Debugger:\n" + output);
        }
    }


}