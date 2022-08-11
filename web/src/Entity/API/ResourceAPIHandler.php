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
    class ResourceAPIHandler {

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
        public static function create(String $ontologyID, array $resourceData) : String {
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
        public static function save(String $resourceID, array $resourceData) : String {
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
        public static function get(String $resourceID) : String {
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
        public static function delete(String $resourceID) : String {
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
        public static function addTerm(String $resourceID, String $termURI) : String {
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
        public static function removeTerm(String $resourceID, String $termURI) : String {
            try {
                $database = new Database();
                $database->removeResourceTerm($resourceID, $termURI);
                $terms = $database->getResourceTerms($resourceID);
                return JSONFormatter::arrayToString($terms);
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


    }

?>