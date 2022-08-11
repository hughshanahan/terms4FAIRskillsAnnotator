<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\InputBag;

    use App\Controller\APIController;

    use App\Entity\API\OntologyAPIHandler;

    /**
     * Class to handle requests to the Ontology API endpoints.
     */
    class OntologyAPIController extends APIController {


        /**
         * Loads the ontology into the database ready to use for annotating resources.
         *
         * @param Request $request the request containing the URL of the ontology to store.
         * @return Response The response containing JSON data with the database id of the ontology 
         */
        public function load(Request $request) : Response {
            try {
                // get the data as an associative array and pass it to the handler
                $ontologyURL = $this->getRequiredParameter($request->query, "url");
                $userID = $request->query->get("userID", "");
                if ($userID === "") {
                    // a user ID wasn't provided - create a user
                    $userJSON = JSONFormatter::stringToArray(
                        UserAPIHandler::create()
                    );
                    $userID = $userJSON["userID"];
                }
                // load the ontology
                $json = OntologyAPIHandler::load($userID, $ontologyURL);        
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
            
        }


        /**
         * Returns the ontology details.
         *
         * @param Request $request the request containing the ontology id
         * @return Response the response containing the JSON string of ontology details
         */
        public function getDetails(Request $request) : Response {
            try {
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                $json = OntologyAPIHandler::getDetails($ontologyID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }

        
        /**
         * Returns the JSON String with all the details about the ontology's resources.
         *
         * @param Request $request the request containing the ontology id
         * @return Response the response containing the JSON String
         */
        public function getResources(Request $request) : Response {
            try {
                // get the ontology id from the request
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                // get the JSON String of the ontology resources
                $json = OntologyAPIHandler::getResources($ontologyID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }

        /**
         * Returns the JSON String for the export file.
         *
         * @param Request $request the request containing the ontology id
         * @return Response the response containing the JSON String
         */
        public function exportAnnotations(Request $request) : Response {
            try {
                // get the ontology id from the request
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                // get the JSON String of all the annotations
                $json = OntologyAPIHandler::exportAnnotations($ontologyID);
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
            
        }


        /**
         * Deletes an ontology.
         *
         * @param Request $request the request containing the id of the ontology to delete
         * @return Response the response containing the status of the deletion
         */
        public function delete(Request $request) : Response {
            try {
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                $json = OntologyAPIHandler::delete($ontologyID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }





    }

?>