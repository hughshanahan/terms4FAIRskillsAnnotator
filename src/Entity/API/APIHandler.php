<?php

    namespace App\Entity\API;

    use App\Entity\Ontology\Ontology;
    use App\Entity\API\JSONFormatter;


    /**
     * Class to process requests made to the API controller.
     * 
     * The purpose of this class is to remove as much of the logic from the APIController class.
     */
    class APIHandler {

        /*
            ===== Class Methods =====

            These are methods called by the APIController.
            Their purpose is to convert a call to the constructor and 
            then to the correct method into one method call.
        */


        // === GET Methods ===

        /**
         * Returns an instance of the APIHandler class.
         *
         * @param String $ontologyID the Database ID of the ontology to use.
         * @return APIHandler the APIHandler object
         */
        private static function getHandler(String $ontologyID) : APIHandler {
            return new APIHandler($ontologyID);
        }


        /**
         * Returns a JSON String of the search results.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $searchQuery the search query
         * @return String the JSON string of the search results
         */
        public static function searchTerms(String $ontologyID, String $searchQuery) : String {
            return self::getHandler($ontologyID)->_searchTerms($searchQuery);
        }

        /**
         * Returns a JSON String of the term details.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $termURI the URI of the term to return
         * @return String the JSON string of the term details
         */
        public static function getTerm(String $ontologyID, String $termURI) : String {
            return self::getHandler($ontologyID)->_getTerm($termURI);
        }

        /**
         * Returns a JSON String of the object property details.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $propertyURI the URI of the object property to return
         * @return String the JSON string of the term details
         */
        public static function getObjectProperty(String $ontologyID, String $propertyURI) : String {
            return self::getHandler($ontologyID)->_getObjectProperty($propertyURI);
        }



        // === POST Methods ===

        /**
         * Saves the resource data to the database.
         *
         * @param String $ontologyID the ID of the ontology
         * @param String $resourceID the ID of the resource
         * @param array $resourceData an associative array of the resource data
         * @return String A JSON String of the status of the save
         */
        public static function saveResource(String $ontologyID, String $resourceID, array $resourceData) : String {
            return self::getHandler($ontologyID)->_saveResource($resourceID, $resourceData);
        }





        /*
            ===== Instance Methods =====

            These are the methods that are run by the class methods.

        */


        // === Instance Variables ===
        private $ontology;


        // === Constructor and related methods ====

        /**
         * Constructs an instance of the APIHandler class.
         * 
         * @param String $ontologyID the key of the ontology
         */
        private function __construct(String $ontologyID) {
            // create the ontology object
            // currently from the t4fs.owl file in the tests directory
            $this->ontology = $this->getOntology($ontologyID);
        }

        /**
         * Returns the ontology object from the database.
         *
         * @param String $ontologyID the key for the ontology
         * @return Ontology the ontology object
         */
        private function getOntology(String $ontologyID) : Ontology {
            if ($ontologyID == "test") {
                // if the ID is "test" return the Ontology object for the OntologyTest.owl file
                return new Ontology("tests/Resources/OntologyTest.owl");
            } else {
                // this will get the serialised ontology from the database and return the object
                // for now just return the stored ontology
                return new Ontology($_SERVER["DOCUMENT_ROOT"] . "/../tests/Resources/t4fs.owl");
            }
            
        }




        // === Operation methods ===
        // They are all prefixed with an underscore to differentiate them from the class method versions


        // === GET METHODS ===


        /**
         * Returns a JSON String of the search results.
         *
         * @param String $searchQuery the search query
         * @return String the JSON string of the search results
         */
        private function _searchTerms(String $searchQuery) : String {

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


        /**
         * Returns a JSON String of the term details.
         *
         * @param String $termURI the URI of the term to return
         * @return String the JSON string of the term details
         */
        private function _getTerm(String $termURI) : String {
            // get the class matching the URI
            $class = $this->ontology->getClass($termURI);

            // create the array to store the data that should be returned
            $data = $class->getJSONArray();

            // return the JSON String
            return JSONFormatter::arrayToString($data);
        }


        /**
         * Returns a JSON String of the object property details.
         *
         * @param String $propertyURI the URI of the object property to return
         * @return String the JSON string of the term details
         */
        private function _getObjectProperty(String $propertyURI) : String {
            // get the object property matching the URI
            $property = $this->ontology->getObjectProperty($propertyURI);

            // get the JSON data to return
            $data = array("about"=>$property->getAbout());

            // return the JSON String
            return JSONFormatter::arrayToString($data);
        }





        // === POST Methods ===


        /**
         * Saves the resource data to the database.
         *
         * @param String $resourceID the ID of the resource
         * @param array $resourceData an associative array of the resource data
         * @return String A JSON String of the status of the save
         */
        public function _saveResource(String $resourceID, array $resourceData) : String {
            // create the return string
            $data = array(
                "status"=>"ok", 
                "data"=>$resourceData
            );


            return JSONFormatter::arrayToString($data);

        }


    }

?>