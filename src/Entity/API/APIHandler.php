<?php

    namespace App\Entity\API;

    use App\Entity\Ontology\Ontology;
    use App\Entity\API\JSONFormatter;
    use App\Entity\API\Database;

    /**
     * Class to process requests made to the API controller.
     * 
     * The purpose of this class is to remove as much of the logic from the APIController class.
     */
    class APIHandler {

        // === private methods ===

        /**
         * Returns the ontology object from the database.
         *
         * @param String $ontologyID the key for the ontology
         * @return Ontology the ontology object
         */
        private static function getOntology(String $ontologyID) : Ontology {
            if ($ontologyID == "test") {
                // if the ID is "test" return the Ontology object for the OntologyTest.owl file
                return new Ontology("tests/Resources/OntologyTest.owl");
            } else {
                // return the unserialised database object
                $database = new Database();
                return $database->getOntology($ontologyID);
            }
            
        }

        /**
         * Formats the annotator form data to be the format needed for the database.
         *
         * @param array $resourceData the form data
         * @return array the formatted data
         */
        private static function formatResourceData(array $resourceData) : array {
            $terms = explode(',', $resourceData["selected-terms"]);

            return array(
                "identifier" => $resourceData["identifier-input"],
                "name" => $resourceData["name-input"],
                "author" => $resourceData["author-surname-input"] . ", " . $resourceData["author-firstname-input"],
                "date" => strval(mktime(
                        0,0,0, //hours, minutes, seconds 
                        $resourceData["date-month-input"],
                        $resourceData["date-day-input"],
                        $resourceData["date-year-input"]
                    )),
                "terms" => $terms
            );
        }

        /**
         * Creates the JSON String that is returned to the frontend when the resource is saved successfully.
         *
         * @param String $resourceID the resource's database id
         * @param array $resourceData the array of data entered into the annotator form
         * @return String the JSON String to return to the frontend
         */
        private static function createResourceReturnJSON(String $resourceID, array $resourceData) : String {
            // create the return data
            $data = array(
                "status" => "ok", 
                "resourceID" => $resourceID,
                "savedAt" => time(),
                "data" => $resourceData
            );

            // return the JSON String
            return JSONFormatter::arrayToString($data);
        }



        
        


        // === Ontology Methods ===

        /**
         * Loads the ontology into the database.
         *
         * @param String $ontologyURL the URL of the ontology to use
         * @return String the JSON String of the ontology details
         * @throws Exception if there was an error connecting to the database
         * @throws Exception if the ontology could not be loaded
         * @throws Exception if the ontology could not be stored in the database
         */
        public static function loadOntology(String $ontologyURL) : String {
            try {
                $database = new Database();
                $ontology = new Ontology($ontologyURL);
                $ontologyID = $database->insertOntology($ontology);
                return "{\"ontologyID\":\"" . $ontologyID . "\"}";
            } catch (\Exception $e) {
                throw $e;
            }
        }


        /**
         * Returns a JSON String of the search results.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $searchQuery the search query
         * @return String the JSON string of the search results
         */
        public static function searchTerms(String $ontologyID, String $searchQuery) : String {
            $ontology = self::getOntology($ontologyID);
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
        }

        /**
         * Returns a JSON String of the term details.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $termURI the URI of the term to return
         * @return String the JSON string of the term details
         */
        public static function getTerm(String $ontologyID, String $termURI) : String {
            $ontology = self::getOntology($ontologyID);
            // get the class matching the URI
            $class = $ontology->getClass($termURI);
            // create the array to store the data that should be returned
            $data = $class->getJSONArray();
            // return the JSON String
            return JSONFormatter::arrayToString($data);
        }


        /**
         * Returns a JSON String of the object property details.
         *
         * @param String $ontologyID the key of the ontology
         * @param String $propertyURI the URI of the object property to return
         * @return String the JSON string of the term details
         */
        public static function getObjectProperty(String $ontologyID, String $propertyURI) : String {
            $ontology = self::getOntology($ontologyID);
            // get the object property matching the URI
            $property = $ontology->getObjectProperty($propertyURI);

            // get the JSON data to return
            $data = array("about"=>$property->getAbout());

            // return the JSON String
            return JSONFormatter::arrayToString($data);
        }


        /**
         * Returns the JSON String with all the ontology's resources in. 
         *
         * @param String $ontologyID the id of the ontology to export 
         * @return String the JSON String of the resources
         */
        public static function getOntologyResources(String $ontologyID) : String {
            $database = new Database();
            $resources = $database->getOntologyResources($ontologyID);
            return JSONFormatter::arrayToString($resources);
        }


        /**
         * Returns the JSON String for the export file.
         *
         * @param String $ontologyID the id of the ontology to export 
         * @return String the JSON String for the export file
         */
        public static function exportAnnotations(String $ontologyID) : String {
            $database = new Database();
            $resources = $database->getOntologyResources($ontologyID);
            $annotations = array();
            foreach ($resources as $resource) {
                $resourceAnnotation = array(
                    "doi" => $resource["identifier"],
                    "name" => $resource["name"],
                    "author" => $resource["author"],
                    "date" => date("Y-m-d", $resource["date"]),
                    "concept" => $resource["terms"]
                );
                array_push($annotations, $resourceAnnotation);
            }
            return JSONFormatter::arrayToString($annotations);
        }








        // === Resource Methods ===


        /**
         * Creates an entry in the database for the resource. 
         *
         * @param String $ontologyID the ID of the ontology
         * @param array $resourceData an associative array of the resource data
         * @return String A JSON String of the status of the save
         */
        public static function createResource(String $ontologyID, array $resourceData) : String {
            // format the resource data array values into data the database can store
            $formattedData = self::formatResourceData($resourceData);

            // create the entry in the database and get the resource id back
            $database = new Database();
            $resourceID = $database->createResource(
                $ontologyID,
                $formattedData["identifier"],
                $formattedData["name"],
                $formattedData["author"],
                $formattedData["date"],
                $formattedData["terms"]
            );

            return self::createResourceReturnJSON($resourceID, $resourceData);
        }


        /**
         * Saves the resource data to the database.
         *
         * @param String $resourceID the ID of the resource
         * @param array $resourceData an associative array of the resource data
         * @return String A JSON String of the status of the save
         */
        public static function saveResource(String $resourceID, array $resourceData) : String {
            // format the resource data array values into data the database can store
            $formattedData = self::formatResourceData($resourceData);

            // create the entry in the database and get the resource id back
            $database = new Database();
            $database->saveResource(
                $resourceID,
                $formattedData["identifier"],
                $formattedData["name"],
                $formattedData["author"],
                $formattedData["date"],
                $formattedData["terms"]
            );

            return self::createResourceReturnJSON($resourceID, $resourceData);
        }


        /**
         * Returns the resource data from the database.
         *
         * @param String $resourceID the resource 
         * @return String
         */
        public static function getResource(String $resourceID) : String {
            $database = new Database();
            $data = $database->getResource($resourceID);
            return JSONFormatter::arrayToString($data);
        }

        /**
         * Deletes a resource from the database.
         *
         * @param String $resourceID the resource to delete
         * @return String the JSON String containing the status information
         */
        public static function deleteResource(String $resourceID) : String {
            $database = new Database();
            $database->deleteResource($resourceID);
            return JSONFormatter::arrayToString(array("status"=>"ok"));
        }


    }

?>