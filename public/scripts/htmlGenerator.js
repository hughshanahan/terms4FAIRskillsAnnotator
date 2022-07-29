
/**
 * Class to generate HTML Content.
 */
class HTMLGenerator {

    /**
     * Returns the HTML for a single title/value pair.
     * 
     * @param {String} title the title for the single value
     * @param {String} value the value
     * @param {function} valueProcessor the function to use to process the value - defaults to a method returning the list element
     * @returns {String} the HTML for the single value display
     */
    static createSingleValueDisplay(title, value, valueProcessor = (value) => {return value}) {
        return '<p><strong>' + title + ' - </strong>' + valueProcessor(value) + '</p>';
    }

    /**
     * Returns the HTML for a list of values with a title.
     * 
     * @param {String} title the title
     * @param {String} value the value
     * @param {function} valueProcessor the function to use to process each value - defaults to a method returning the list element
     * @returns {String} the HTML for the list display
     */
    static createValueListDisplay(title, values, valueProcessor = (value) => {return value}) {
        var html = "";
        // process the comments element
        if (values.length > 0) {
            // there are elements in the list
            html += this.createSingleValueDisplay(title, "");
            html += '<ul>';
            values.forEach(value => {
                html += "<li>" + valueProcessor(value) + '</li>';
            });
            html += '</ul>';
        } else {
            // there are no elements in the list
            html += this.createSingleValueDisplay(title, "<i>(none)</i>");
        }
        return html;
    }




}