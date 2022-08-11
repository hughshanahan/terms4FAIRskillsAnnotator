<?php

    namespace App\Entity\API;

    use App\Entity\API\APIHandler;
    use App\Entity\Ontology\Ontology;
    use App\Entity\API\JSONFormatter;
    use App\Entity\API\Database;

    /**
     * Class to process requests made to the API controller.
     * 
     * The purpose of this class is to remove as much of the logic from the APIController class.
     */
    class TermsAPIHandler {

        /**
         * Returns a JSON String of the search results.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $searchQuery the search query
         * @return String the JSON string of the search results
         * @throws \Exception if the ontology couldn't be searched
         */
        public static function search(String $ontologyID, String $searchQuery) : String {
            try {
                $ontology = APIHandler::getOntology($ontologyID);
                // search the ontology
                $classes = $ontology->queryClasses($searchQuery);
                // process the classes into the data array
                $results = array();
                foreach ($classes as $class) {
                    array_push(
                        $results, 
                        $class->getJSONArray()
                    );
                }
                //set the data properties
                $data["search"] = $searchQuery;
                $data["results"] = $results;

                // return the JSON String
                return JSONFormatter::arrayToString($data);
            } catch (\Exception $e) {
                throw $e;
            }
            
        }

        /**
         * Returns a JSON String of the term details.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $termURI the URI of the term to return
         * @return String the JSON string of the term details
         * @throws \Exception If the ontology could be loaded, or the ontology class could not be found
         */
        public static function get(String $ontologyID, String $termURI) : String {
            try {
                $ontology = APIHandler::getOntology($ontologyID);
                // get the class matching the URI
                $class = $ontology->getClass($termURI);
                // create the array to store the data that should be returned
                $data = $class->getJSONArray();
                // return the JSON String
                return JSONFormatter::arrayToString($data);
            } catch (\Exception $e) {
                throw $e;
            }
        }

    }

?>