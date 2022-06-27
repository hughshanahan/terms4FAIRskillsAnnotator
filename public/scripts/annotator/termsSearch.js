/**
 * Class to store methods related to the Terms Search.
 */
class TermsSearch {
    
    
    /**
     * Searches for the given term in the current ontology.
     * 
     * @param {String} searchTerm the term to search for
     */
    static search(searchTerm) {
        document.getElementById("results-container").innerHTML = "<p>" + searchTerm + "</p>";
    }

}