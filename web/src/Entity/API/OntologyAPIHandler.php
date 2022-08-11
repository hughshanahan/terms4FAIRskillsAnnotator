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
    class OntologyAPIHandler {


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
        public static function load(String $userID, String $ontologyURL) : String {
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
        public static function getDetails(String $ontologyID) : String {
            try {
                $ontology = APIHandler::getOntology($ontologyID);
                $data = $ontology->getJSONArray();
                $data["id"] = $ontologyID;
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
        public static function getResources(String $ontologyID) : String {
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
        public static function delete(String $ontologyID) : String {
            try {
                $database = new Database();
                $database->deleteOntology($ontologyID);
                return JSONFormatter::arrayToString(array("status"=>"ok"));
            } catch (\Exception $e) {
                throw $e;
            }
        }




    }

?>

