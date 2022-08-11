<?php

    namespace App\Entity\API;

    use App\Entity\API\APIHandler;
    use App\Entity\API\JSONFormatter;
    use App\Entity\API\Database;

    use App\Entity\API\OntologyAPIHandler;

    /**
     * Class to process requests made to the API controller.
     * 
     * The purpose of this class is to remove as much of the logic from the APIController class.
     */
    class UserAPIHandler {

        /**
         * Returns a JSON string containing the userID.
         *
         * @return String a JSON String containing the userID
         */
        public static function create() : String {
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
        public static function getOntologies(String $userID) : String {
            try {
                $database = new Database();
                $ontologyIDs = $database->getUserOntologies($userID);
                $data = array();
                foreach ($ontologyIDs as $ontologyID) {
                    array_push(
                        $data, 
                        JSONFormatter::StringToArray(
                            OntologyAPIHandler::getDetails($ontologyID)
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
        public static function delete(String $userID) : String {
            try {
                $database = new Database();
                $database->deleteUser($userID);
                return JSONFormatter::arrayToString(array("status"=>"ok"));
            } catch (\Exception $e) {
                throw $e;
            }
        }

    }

?>