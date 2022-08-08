<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\InputBag;

    use App\Entity\API\APIHandler;

    use App\Entity\API\JSONFormatter;

    /**
     * Class to handle requests to the API.
     */
    class APIController {


        // === Response Generators ===

        /**
         * Creates a response object containing JSON data for a successful request.
         *
         * @param String $json The JSON data to return
         * @return Response The Response object
         */
        private function successResponse(String $json) : Response {
            return new Response(
                $json,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }

        /**
         * Creates a response object containing the error message for a request that has thrown an exception.
         *
         * @param \Exception $exception the exception that was thrown
         * @return Response The Response object
         */
        private function errorResponse(\Exception $exception) : Response {
            $message = $exception->getMessage();
            // get the error message in a JSON object
            $json = JSONFormatter::arrayToString(
                array("message"=>$message)
            );
            // create the response
            return new Response(
                $json,
                Response::HTTP_INTERNAL_SERVER_ERROR, // there was an error - response code "500: internal server error"
                ['content-type' => 'application/json']
            );
        }



        // === Query Parameter Checker ===

        /**
         * Returns the value from the query string.
         *
         * @param InputBag $query the query from the query
         * @param String $parameter the parameter name
         * @return String the parameter value
         * @throws \Exception if the parameter is not present
         */
        private function getRequiredParameter(InputBag $query, String $parameter) : String {
            $value = $query->get($parameter, "");
            if ($value === "") {
                throw new \Exception("Parameter '" . $parameter . "' missing");
            }
            return $value;
        }



        // === User Methods ===


        /**
         * Creates a user in the database and returns the userID.
         *
         * @param Request $request the HTTP request
         * @return Response the response containing the JSON data with the userID  
         */
        public function createUser(Request $request) : Response {
            try {
                $json = APIHandler::createUser();
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }


        /**
         * Returns the user ontologys.
         *
         * @param Request $request the request containing the userID
         * @return Response the response containing JSON data with the user's ontologies
         */
        public function getUserOntologies(Request $request) : Response {
            try {
                $userID = $this->getRequiredParameter($request->query, "userID");
                $json = APIHandler::getUserOntologies($userID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }


        /**
         * Deletes a user.
         *
         * @param Request $request the request containing the id of the user to delete
         * @return Response the response containing the status of the deletion
         */
        public function deleteUser(Request $request) : Response {
            try {
                $userID = $this->getRequiredParameter($request->query, "userID");
                $json = APIHandler::deleteUser($userID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }




        // === Ontology Methods ===


        /**
         * Loads the ontology into the database ready to use for annotating resources.
         *
         * @param Request $request the request containing the URL of the ontology to store.
         * @return Response The response containing JSON data with the database id of the ontology 
         */
        public function loadOntology(Request $request) : Response {
            try {
                // get the data as an associative array and pass it to the handler
                $ontologyURL = $this->getRequiredParameter($request->query, "url");
                $userID = $request->query->get("userID", "");
                if ($userID === "") {
                    // a user ID wasn't provided - create a user
                    $userJSON = JSONFormatter::stringToArray(
                        APIHandler::createUser()
                    );
                    $userID = $userJSON["userID"];
                }
                // load the ontology
                $json = APIHandler::loadOntology($userID, $ontologyURL);        
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
        public function getOntologyDetails(Request $request) : Response {
            try {
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                $json = APIHandler::getOntologyDetails($ontologyID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }

        /**
         * Returns the JSON for the terms matching the search term from the ontology.
         *
         * @param Request $request the HTTP request
         * @return Response the JSON string of the terms and their metadata
         */
        public function searchTerms(Request $request) : Response {
            try {
                // get the search string from the request and get the results JSON String
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                $searchQuery = $this->getRequiredParameter($request->query, "search");
                $json = APIHandler::searchTerms(
                    $ontologyID,
                    $searchQuery
                );
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }


        /**
         * Returns the data for a term in JSON format.
         *
         * @param Request $request the HTTP request
         * @return Response the JSON string of the term's metadata
         */
        public function getTerm(Request $request) : Response {
            try {
                // get the term URI from the request and get the term's JSON String
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                $termURI = $this->getRequiredParameter($request->query, "term");
                $json = APIHandler::getTerm(
                    $ontologyID,
                    $termURI
                );
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
        public function getOntologyResources(Request $request) : Response {
            try {
                // get the ontology id from the request
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                // get the JSON String of the ontology resources
                $json = APIHandler::getOntologyResources($ontologyID);
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
                $json = APIHandler::exportAnnotations($ontologyID);
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
        public function deleteOntology(Request $request) : Response {
            try {
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                $json = APIHandler::deleteOntology($ontologyID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }




        // === Resource Methods ===

        /**
         * Creates a new resource in the database and returns the ID in a JSON string.
         *
         * @param Request $request the HTTP Request
         * @return Response the HTTP Response containing the resource ID in a JSON String
         */
        public function createResource(Request $request) : Response {
            try {
                // get the ontology id from the request
                $ontologyID = $this->getRequiredParameter($request->query, "ontologyID");
                // get the resource data from the query
                $resourceData = array(
                    "identifier" => $this->getRequiredParameter($request->query, "identifier"), 
                    "name" => $this->getRequiredParameter($request->query, "name"), 
                    "author" => $this->getRequiredParameter($request->query, "author"), 
                    "date" => $this->getRequiredParameter($request->query, "date")
                );
                // create the resource
                $json = APIHandler::createResource(
                    $ontologyID, 
                    $resourceData
                );
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
            
        }


        /**
         * Saves the resource data to the database.
         *
         * @param Request $request the HTTP request
         * @return Response the response containing JSON data with status information about the saving of the resource
         */
        public function saveResource(Request $request) : Response {
            try {
                // get the resource ID from the query
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                // get the resource data from the query
                $resourceData = array();
                // the possible parameters
                $parameters = array("identifier", "name", "author", "date");
                // for each of the possible parameters
                foreach ($parameters as $parameter) {
                    $value = $request->query->get($parameter, "");
                    if (!($value === "")) {
                        // if the value is not the default - save to the data array
                        $resourceData[$parameter] = $value;
                    }
                }
                // save the resource
                $json = APIHandler::saveResource(
                    $resourceID,
                    $resourceData
                );
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
            
        }

        /**
         * Returns the data about the resource.
         *
         * @param Request $request the HTTP request
         * @return Response the HTTP response containing the resource data as a JSON String
         */
        public function getResource(Request $request) : Response {
            try {
                // get the resource ID from request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                // get the data about the resource
                $json = APIHandler::getResource($resourceID);
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }

        /**
         * Deletes a resource from the annotator.
         *
         * @param Request $request the request containing the id of the resource to delete
         * @return Response the status of the deletion
         */
        public function deleteResource(Request $request) : Response {
            try {
                // get the resource id from the request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                // get the JSON String of the deletion status
                $json = APIHandler::deleteResource($resourceID);
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }


        /**
         * Adds a term annotation to the resource and returns JSON containing all the annotated terms.
         *
         * @param Request $request the HTTP request containing the resource id and the term
         * @return Response the repsonse containing the JSON of the terms
         */
        public function addResourceTerm(Request $request) : Response {
            try {
                // get the resource id and term URI from the request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                $termURI = $this->getRequiredParameter($request->query, "term");
                // get the JSON String of the deletion status
                $json = APIHandler::addResourceTerm($resourceID, $termURI);
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }

        /**
         * Removes a term annotation from the resource and returns JSON containing all the annotated terms.
         *
         * @param Request $request the HTTP request containing the resource id and the term
         * @return Response the repsonse containing the JSON of the terms
         */
        public function removeResourceTerm(Request $request) : Response {
            try {
                // get the resource id and term URI from the request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                $termURI = $this->getRequiredParameter($request->query, "term");
                // get the JSON String of the deletion status
                $json = APIHandler::removeResourceTerm($resourceID, $termURI);
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }

    }

?>