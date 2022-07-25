
class TermSearchResult {


    /**
     * Creates the container for the term.
     * 
     * @param {JSON} term The JSON data of the term
     * @param {Array} selectedTerms The list of already selected terms
     * @returns {String} the HTML string for the term's container
     */
    static create(term, selectedTerms) {
        var html = "";

        html += '<div class="container border border-secondary rounded p-3 m-0">'; 
            // this needs to be a container or container-fluid for AnnotatorSearch's grid to work

        // start the grid
        html += '<div class="container">';
        html += '<div class="row">';

        // left column - this is the same as the terms search
        html += '<div class="col">';
        html += TermSearchResult.createTermContents(term);
        html += '</div>';

        // right column - this contains the button to add the term to the annotation
        html += '<div class="col-3 d-flex flex-column justify-content-center">';


        if (selectedTerms.includes(term.about)) {
            // the term is in the selected list - add the remove button
            html += '<button type="button" class="btn btn-danger" id="addToSelectedButton" '
                + 'onclick="Annotator.removeFromSelectedTerms(\'' + term.about + '\');" />Remove</button>';

        } else {
            // the term is not already in the selected list - add the add button
            html += '<button type="button" class="btn btn-success" id="removeFromSelectedButton" '
                + 'onclick="Annotator.addToSelectedTerms(\'' + term.about + '\');" />Add</button>';

        }

        html += '</div>';

        //close the grid
        html += '</div>';
        html += '</div>';
        
        html += '</div>';

        return html;
    }

    /**
     * Creates the content for the term's container.
     * 
     * @param {JSON} term The JSON data of the term
     * @returns {String} the HTML string for the term container's contents
     */
    static createTermContents(term) {
        var html = "";

        html += '<div class="d-flex flex-column justify-content-start">';

        html += '<p class="text-primary p-0 m-0"><b>' + term.label + '</b></p>';
        html += '<p class="text-secondary p-0 m-0"><i>(' + T4FSAnnotator.breakURL(term.about) + ')</i></p>';

        html += '<hr />';

        const moreOnClick = 'TermSearchResult.showDetails(\'' + term.label + '\', \'' + term.about + '\');';
        html += '<button type="button" class="btn btn-primary" onclick="' + moreOnClick + '">';
        html += 'More...';
        html += '</button>';

        html += '</div>';

        return html;
    }

    /**
     * Shows the modal view with extra details about the term.
     * 
     * @param {String} term 
     * @param {String} termURI 
     */
    static showDetails(term, termURI) {

        // show the modal view with the title and the loading spinner
        ModalController.showLoadingSpinner();
        ModalController.show(term, '');

        // fetch the term details and set the modal content
        fetch("/api/getTerm?term=" + termURI, { method: 'get' })
                // then convert response to JSON object
                .then(response => response.json())
                .then(data => {;
                    var html = "";
                    // create the container that justifies the content to the left edge
                    html += '<div class="d-flex flex-column justify-content-start w-100">';
                    // add the content
                    html += this.createModalBodyPair("URI", data.about);
                    html += this.createModalBodyPair("Description", data.description);
                    html += this.createModalBodyList("Relations", data.parents, TermSearchResult.processTermRelations);
                    html += this.createModalBodyList("Comments", data.comments);
                    // close the justification container
                    html += '</div>';
                    // show the content in the modal
                    ModalController.showContent(html);
                })
                .catch(err => console.log(err));

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
    static createModalBodyPair(title, value) {
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
    static createModalBodyList(title, list, elementProcessor = (value) => {return value}) {
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
    static processTermRelations(relation) {
        var str = ""
        
        str += relation.parent;

        return str;
    }


}