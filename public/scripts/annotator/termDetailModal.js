
/**
 * Class to create a Term Detail Modal.
 */
class TermDetailModal {

    /**
     * Creates the Term Detail Modal.
     * 
     * @param {JSON} term the JSON data for the term
     * @returns {String} The HTML string for the term detail modal
     */
    static createModal(term) {
        var modal = new TermDetailModal(term);
        return modal.create();
    }


    /**
     * Constructs a TermDetailModal object.
     * 
     * @param {JSON} term the JSON data for the term
     */
    constructor(term) {
        this.term = term;

        // the modals need a way to identify themselves - the URI of the term is the only value that can be unique
        // however this cannot be used because it may contain characters that cannot appear in an element ID.
        // Therefore take a hash of the URI as this is very likely to be unique within the possible list of URIs
        this.uriHash = T4FSAnnotator.hashString(this.term.about);
    }


    /**
     * Creates the term detail modal.
     * 
     * @returns {String} the HTML String for the Term Detail Modal
     */
    create() {
        var html = "";

        // create the button to trigger the modal
        html += '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#TermModal' + this.uriHash + '">';
        html += 'More...';
        html += '</button>';

        // create the modal containers
        html += '<div class="modal fade" id="TermModal' + this.uriHash + '" tabindex="-1" aria-labelledby="TermModal' + this.uriHash + 'Label" aria-hidden="true">';
        html += '<div class="modal-dialog modal-xl">';
        html += '<div class="modal-content">';

        // create the modal header
        html += '<div class="modal-header">';
        html += '<h5 class="modal-title text-primary" id="TermModal' + this.uriHash + 'Label">' + this.term.label + '</h5>';
        html += '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        html += '</div>';

        // create the modal content
        html += '<div class="modal-body">';
        html += this.createModalBody();
        html += '</div>';

        // create the modal footer
        html += '<div class="modal-footer">';
        html += '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
        html += '</div>';

        // close the modal containers
        html += '</div>';
        html += '</div>';
        html += '</div>';

        return html;
    }


    /**
     * Creates the content for the term details modal view.
     * 
     * @returns {String} the HTML string for the modal content
     */
    createModalBody() {
        var html = "";

        html += '<div class="d-flex flex-column justify-content-start">';

        html += this.createModalBodyPair("URI", this.term.about);

        html += this.createModalBodyList("Relations", this.term.parents, this.processTermRelations);

        html += this.createModalBodyList("Comments", this.term.comments);
        
        html += '</div>';

        return html;
    }

    /**
     * Creates an entry in the list of attributes. 
     * The title is in bold and followed by a hyphen.
     * The value followes this.
     * 
     * @param {String} title the title to give the pair
     * @param {String} value the value of the pair
     * @returns {String} the HTML string for the pair
     */
    createModalBodyPair(title, value) {
        return '<p><strong>' + title + ' - </strong>' + value + '</p>';
    }

    /**
     * Creates a list of the values from an array.
     * 
     * @param {String} title the title to give the list
     * @param {array} list the list of values
     * @param {function} elementProcessor the function to use to process the list element - defaults to a method returning the list element
     * @return {String} the HTML string for the list
     */
    createModalBodyList(title, list, elementProcessor = (value) => {return value}) {
        var html = "";
        // process the comments element
        if (list.length > 0) {
            // there are elements in the list
            html += this.createModalBodyPair(title, "");
            html += '<ul>';
            list.forEach(element => {
                html += "<li>" + elementProcessor(element) + '</li>';
            });
            html += '</ul>';
        } else {
            // there are no elements in the list
            html += this.createModalBodyPair(title, "<i>(none)</i>");
        }
        return html;
    }



    /**
     * Creates the HTML for a relation list item.
     * 
     * @param {JSON} relation the JSON object for the relation
     * @returns {String} the HTML for the relation list item
     */
    processTermRelations(relation) {
        var str = ""
        
        str += relation.parent;

        return str;
    }

}