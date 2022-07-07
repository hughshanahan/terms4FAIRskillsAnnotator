
class TermSearchResult {


    /**
     * Creates the container for the term.
     * 
     * @param {JSON} term The JSON data of the term
     * @param {String} mode The mode to display the results - termSearch or annotator - defaults to termsSearch
     * @param {Array} selectedTerms The list of already selected terms - defaults to an empty array
     * @returns 
     */
    static create(term, mode="termsSearch", selectedTerms=[]) {
        var termsSearchResult = new TermSearchResult(term, mode, selectedTerms);
        return termsSearchResult.createContainer();
    }


    /**
     * Constructs a TermSearchResult object.
     * 
     * @param {JSON} term The JSON data of the term
     * @param {String} mode The mode to display the results - termSearch or annotator
     * @param {Array} selectedTerms The list of already selected terms
     */
    constructor(term, mode, selectedTerms) {
        this.term = term;
        this.mode = mode;
        this.selectedTerms = selectedTerms;
    }



    /**
     * Creates the container for the term.
     * 
     * @returns {String} the HTML string for the term's container
     */
    createContainer() {
        var html = "";

        html += '<div class="container border border-secondary rounded p-3 m-0">'; 
            // this needs to be a container or container-fluid for AnnotatorSearch's grid to work

        if (this.mode === "termsSearch") {
            // if for the terms search - just add the basic contents
            html += this.createTermContainerContents();
        } else {
            // if for the annotator - add the button grid

            // start the grid
            html += '<div class="container">';
            html += '<div class="row">';

            // left column - this is the same as the terms search
            html += '<div class="col">';
            html += this.createTermContainerContents();
            html += '</div>';

            // right column - this contains the button to add the term to the annotation
            html += '<div class="col-3 d-flex flex-column justify-content-center">';
    
    
            if (this.selectedTerms.includes(this.term.about)) {
                // the term is in the selected list - add the remove button
                html += '<button type="button" class="btn btn-danger" id="addToSelectedButton" '
                    + 'onclick="Annotator.removeFromSelectedTerms(\'' + this.term.about + '\');" />Remove</button>';
    
            } else {
                // the term is not already in the selected list - add the add button
                html += '<button type="button" class="btn btn-success" id="removeFromSelectedButton" '
                    + 'onclick="Annotator.addToSelectedTerms(\'' + this.term.about + '\');" />Add</button>';
    
            }
    
            html += '</div>';
    
            //close the grid
            html += '</div>';
            html += '</div>';
        }

        
        html += '</div>';

        return html;
    }



    /**
     * Creates the content for the term's container.
     * 
     * @returns {String} the HTML string for the term container's contents
     */
    createTermContainerContents() {
        var html = "";

        html += '<div class="d-flex flex-column justify-content-start">';

        html += '<p class="text-primary p-0 m-0"><b>' + this.term.label + '</b></p>';
        html += '<p class="text-secondary p-0 m-0"><i>(' + T4FSAnnotator.breakURL(this.term.about) + ')</i></p>';

        html += '<hr />';

        html += TermDetailModal.createModal(this.term);

        html += '</div>';

        return html;
    }



}