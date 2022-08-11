
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
        ModalController.show(term);

        // fetch the term details and set the modal content
        APIRequest.fetch(
            "/api/terms/get?ontologyID=" + Cookies.get("annotator-ontology-id") + "&term=" + termURI,
            function(data) {
                TermSearchResult.createModalContent(data);
            }
        );
    }


    /**
     * Creates the content for the term detail modal.
     * 
     * @param {JSON} data the JSON data from the API Request
     * @returns {String} the HTML for the modal content
     */
    static createModalContent(data) {
        // get the data in a way that it can be processed asynchronously
        const ontologyID = Cookies.get("annotator-ontology-id");
        const relatedTerms = TermSearchResult.getRelations(data.parents);
        var relationStrings = [];

        APIRequest.fetchAll(
            relatedTerms.map(relation => {return "/api/terms/get?ontologyID=" + ontologyID + "&term=" + relation}),
            function(data) {
                // when the individual fetch returns
                relationStrings.push(data.label + " (" + T4FSAnnotator.breakURL(data.about) + ")");
            },
            function() {
                // on individual fetch error
            },
            function() {
                // when all the fetches have been done
                var html = '';
                html += '<div class="d-flex flex-column justify-content-start w-100">';
                // fill in the values
                html += HTMLGenerator.createSingleValueDisplay("URI", data.about);
                html += HTMLGenerator.createSingleValueDisplay("Description", data.description);
                html += HTMLGenerator.createValueListDisplay("Relations", relationStrings);
                html += HTMLGenerator.createValueListDisplay("Comments", data.comments);
                // close the justification container
                html += '</div>';
                // show the content
                ModalController.showContent(html);
            }
        );
        
    }


    /**
     * Creates a list of related URIs from the relations structure.
     * 
     * @param {array} relations the structure containing details of the relations
     * @returns {array} the list of related URIs
     */
    static getRelations(relations) {
        var relatedTerms = [];
        relations.forEach(relation => {
            relatedTerms.push(
                relation.parent
            )
        });
        return relatedTerms;
    }



}