<?php

    namespace App\Entity\API;

    use App\Entity\Ontology\Ontology;
    use App\Entity\API\JSONFormatter;


    /**
     * Class to process requests made to the API controller.
     */
    class APIHandler {

        private $ontology;

        /**
         * Constructs an instance of the APIHandler class.
         * 
         * @param String $ontologyID the key of the ontology
         */
        public function __construct(String $ontologyID) {
            // create the ontology object
            // currently from the t4fs.owl file in the tests directory
            $this->ontology = $this->getOntology($ontologyID);
        }


        private function getOntology(String $ontologyID) : Ontology {
            if ($ontologyID == "test") {
                return new Ontology("tests/Resources/t4fs.owl");
            } else {
                // this will get the serialised ontology from the database and return the object
                // for now just return the stored ontology
                return new Ontology($_SERVER["DOCUMENT_ROOT"] . "/../tests/Resources/t4fs.owl");
            }
            
        }


        /**
         * Retuns a JSON String of the search results.
         *
         * @param String $searchQuery the search query
         * @return String the JSON string of the search results
         */
        public function searchTerms(String $searchQuery) : String {

            // search the ontology
            $classes = $this->ontology->queryClasses($searchQuery);

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
        }



    }

?>