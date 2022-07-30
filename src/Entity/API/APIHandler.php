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
         * @throws \Exception if there was an error retrieving the ontology from the database
         */
        private static function getOntology(String $ontologyID) : Ontology {
            try {
                if ($ontologyID == "test") {
                    // if the ID is "test" return the Ontology object for the OntologyTest.owl file
                    return new Ontology("tests/Resources/OntologyTest.owl");
                } else {
                    // return the unserialised database object
                    $database = new Database();
                    return $database->getOntology($ontologyID);
                }    
            } catch (\Exception $e) {
                throw $e;
            }
            
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




        // === User Methods ===

        /**
         * Returns a JSON string containing the userID.
         *
         * @return String a JSON String containing the userID
         */
        public static function createUser() : String {
            try {
                $database = new Database();
                $userID = $database->createUser();
                return JSONFormatter::arrayToString(
                    array("userID"=>$userID)
                );
            } catch (\Exception $e) {
                throw $e;
            } 
        }


        /**
         * Returns the user ontologies from the database.
         *
         * @param String $userID the user to get details for
         * @return String The JSON String containing user data
         */
        public static function getUserOntologies(String $userID) : String {
            try {
                $database = new Database();
                $ontologyIDs = $database->getUserOntologies($userID);
                $data = array();
                foreach ($ontologyIDs as $ontologyID) {
                    array_push(
                        $data, 
                        JSONFormatter::StringToArray(
                            self::getOntologyDetails($ontologyID)
                        )
                    );
                }
                return JSONFormatter::arrayToString($data);
            } catch (\Exception $e) {
                throw $e;
            }
        }


        /**
         * Deletes a user.
         *
         * @param String $userID the ID of the user to delete
         * @return String A JSON String containing a status ok message
         * @throws \Exception if the user couldn't be deleted
         */
        public static function deleteUser(String $userID) : String {
            try {
                $database = new Database();
                $database->deleteUser($userID);
                return JSONFormatter::arrayToString(array("status"=>"ok"));
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        


        // === Ontology Methods ===

        /**
         * Loads the ontology into the database.
         *
         * @param String $userID the current userID
         * @param String $ontologyURL the URL of the ontology to use
         * @return String the JSON String of the ontology details
         * @throws Exception if there was an error connecting to the database
         * @throws Exception if the ontology could not be loaded
         * @throws Exception if the ontology could not be stored in the database
         */
        public static function loadOntology(String $userID, String $ontologyURL) : String {
            try {
                $database = new Database();
                $ontology = new Ontology($ontologyURL);
                $ontologyID = $database->insertOntology($userID, $ontology);
                $data = array(
                    "userID"=>$userID,
                    "ontologyID"=>$ontologyID,
                );
                return JSONFormatter::arrayToString($data);
            } catch (\Exception $e) {
                throw $e;
            }
        }


        /**
         * Returns the ontology details.
         *
         * @param String $ontologyID the id of the ontology
         * @return String a JSON String containing the ontology metadata
         */
        public static function getOntologyDetails(String $ontologyID) : String {
            try {
                $ontology = self::getOntology($ontologyID);
                $data = $ontology->getJSONArray();
                $data["id"] = $ontologyID;
                return JSONFormatter::arrayToString($data);
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
         * @throws \Exception if the ontology couldn't be searched
         */
        public static function searchTerms(String $ontologyID, String $searchQuery) : String {
            try {
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
        public static function getTerm(String $ontologyID, String $termURI) : String {
            try {
                $ontology = self::getOntology($ontologyID);
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

        /**
         * Returns the JSON String with all the ontology's resources in. 
         *
         * @param String $ontologyID the id of the ontology to export 
         * @return String the JSON String of the resources
         * @throws \Exception if the database could not be accessed or the ontology id was invalid
         */
        public static function getOntologyResources(String $ontologyID) : String {
            try {
                $database = new Database();
                $resources = $database->getOntologyResources($ontologyID);
                return JSONFormatter::arrayToString($resources);
            } catch (\Exception $e) {
                throw $e;
            }
            
        }


        /**
         * Returns the JSON String for the export file.
         *
         * @param String $ontologyID the id of the ontology to export 
         * @return String the JSON String for the export file
         * @throws \Exception if the database could not be accessed
         */
        public static function exportAnnotations(String $ontologyID) : String {
            try {
                $database = new Database();
                $resources = $database->getOntologyResources($ontologyID);
                $annotations = array();
                foreach ($resources as $resource) {
                    $resourceAnnotation = array(
                        "doi" => $resource["identifier"],
                        "name" => $resource["name"],
                        "author" => $resource["author"],
                        "date" => $resource["date"],
                        "concept" => $resource["terms"]
                    );
                    array_push($annotations, $resourceAnnotation);
                }
                return JSONFormatter::arrayToString($annotations);
            } catch (\Exception $e) {
                throw $e;
            }
            
        }


        /**
         * Deletes an ontology.
         *
         * @param String $ontologyID the ID of the ontology to delete
         * @return String A JSON String containing a status ok message
         * @throws \Exception if the ontology couldn't be deleted
         */
        public static function deleteOntology(String $ontologyID) : String {
            try {
                $database = new Database();
                $database->deleteOntology($ontologyID);
                return JSONFormatter::arrayToString(array("status"=>"ok"));
            } catch (\Exception $e) {
                throw $e;
            }
        }






        // === Resource Methods ===


        /**
         * Creates an entry in the database for the resource. 
         *
         * @param String $ontologyID the ID of the ontology
         * @param array $resourceData an associative array of the resource data
         * @return String A JSON String of the status of the save
         * @throws \Exception if the ontologyID is not valid
         * @throws \Exception if any of the required parameters are missing (identifier, name, author, date)
         * @throws \Exception if the resource couldn't be stored in the database
         */
        public static function createResource(String $ontologyID, array $resourceData) : String {
            try {
                // create the entry in the database and get the resource id back
                $database = new Database();
                $resourceID = $database->createResource(
                    $ontologyID,
                    $resourceData
                );
                $resource = $database->getResource($resourceID);
                return self::createResourceReturnJSON($resourceID, $resource);
            } catch (\Exception $e) {
                throw $e;
            }
            
        }


        /**
         * Saves the resource data to the database.
         *
         * @param String $resourceID the ID of the resource
         * @param array $resourceData an associative array of the resource data
         * @return String A JSON String of the status of the save
         * @throws \Exception if the resoruceID is not valid
         * @throws \Exception if the resource couldn't be stored in the database
         */
        public static function saveResource(String $resourceID, array $resourceData) : String {
            try {
                // update the entry in the database
                $database = new Database();
                $database->saveResource(
                    $resourceID,
                    $resourceData
                );
                $resource = $database->getResource($resourceID);
                return self::createResourceReturnJSON($resourceID, $resource);
            } catch (\Exception $e) {
                throw $e;
            }
            
        }


        /**
         * Returns the resource data from the database.
         *
         * @param String $resourceID the resource 
         * @return String The JSON String of the resource details
         * @throws \Exception if the database couldn't be connected to
         * @throws \Exception if the resource ID isn't valid
         */
        public static function getResource(String $resourceID) : String {
            try {
                $database = new Database();
                $data = $database->getResource($resourceID);
                return JSONFormatter::arrayToString($data);
            } catch (\Exception $e) {
                throw $e;
            }
            
        }

        /**
         * Deletes a resource from the database.
         *
         * @param String $resourceID the resource to delete
         * @return String the JSON String containing the status information
         * @throws \Exception if the database couldn't be connected to
         */
        public static function deleteResource(String $resourceID) : String {
            try {
                $database = new Database();
                $database->deleteResource($resourceID);
                return JSONFormatter::arrayToString(array("status"=>"ok"));
            } catch (\Exception $e) {
                throw $e;
            }
        }

        /**
         * Adds a term annotation to the resource and returns JSON containing all the annotated terms.
         *
         * @param String $resourceID the id of the resource to add the term to
         * @param String $termURI the term to add
         * @return String the JSON String with all the terms for the resource
         * @throws \Exception if the database couldn't be connected to
         * @throws \Exception if the resourceID is invalid
         */
        public static function addResourceTerm(String $resourceID, String $termURI) : String {
            try {
                $database = new Database();
                $database->addResourceTerm($resourceID, $termURI);
                $terms = $database->getResourceTerms($resourceID);
                return JSONFormatter::arrayToString($terms);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        /**
         * Removes a term annotation from the resource and returns JSON containing all the annotated terms.
         *
         * @param String $resourceID the id of the resource to remove the term from
         * @param String $termURI the term to remove
         * @return String the JSON String with all the terms for the resource
         * @throws \Exception if the database couldn't be connected to
         * @throws \Exception if the resourceID is invalid
         */
        public static function removeResourceTerm(String $resourceID, String $termURI) : String {
            try {
                $database = new Database();
                $database->removeResourceTerm($resourceID, $termURI);
                $terms = $database->getResourceTerms($resourceID);
                return JSONFormatter::arrayToString($terms);
            } catch (\Exception $e) {
                throw $e;
            }
        }


    }

?>