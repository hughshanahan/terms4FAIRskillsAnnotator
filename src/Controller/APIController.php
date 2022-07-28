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
                $ontologyURL = $request->query->get("url");
                $json = APIHandler::loadOntology($ontologyURL);
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
            // get the search string from the request and get the results JSON String
            $ontologyID = $request->query->get("ontologyID");
            $searchQuery = $request->query->get("search");
            $json = APIHandler::searchTerms(
                $ontologyID,
                $searchQuery
            );
            return $this->successResponse($json);
        }


        /**
         * Returns the data for a term in JSON format.
         *
         * @param Request $request the HTTP request
         * @return Response the JSON string of the term's metadata
         */
        public function getTerm(Request $request) : Response {
            // get the term URI from the request and get the term's JSON String
            $ontologyID = $request->query->get("ontologyID");
            $termURI = $request->query->get("term");
            $json = APIHandler::getTerm(
                $ontologyID,
                $termURI
            );
            return $this->successResponse($json);
        }

        /**
         * Returns the JSON String with all the details about the ontology's resources.
         *
         * @param Request $request the request containing the ontology id
         * @return Response the response containing the JSON String
         */
        public function getOntologyResources(Request $request) : Response {
            // get the ontology id from the request
            $ontologyID = $request->query->get("ontology");
            // get the JSON String of the ontology resources
            $json = APIHandler::getOntologyResources($ontologyID);
            return $this->successResponse($json);
        }

        /**
         * Returns the JSON String for the export file.
         *
         * @param Request $request the request containing the ontology id
         * @return Response the response containing the JSON String
         */
        public function exportAnnotations(Request $request) : Response {
            // get the ontology id from the request
            $ontologyID = $request->query->get("ontology");
            // get the JSON String of all the annotations
            $json = APIHandler::exportAnnotations($ontologyID);
            return $this->successResponse($json);
        }





        // === Resource Methods ===


        /**
         * Creates an array of the resource details from the query string.
         *
         * @param InputBag $query the query from the request
         * @return array an array of the resource details provided in the query string
         */
        private function getResourceFromQuery(InputBag $query) : array {
            $resourceData = array();
            // the possible parameters
            $parameters = array("identifier", "name", "author", "date");
            // for each of the possible parameters
            foreach ($parameters as $parameter) {
                $value = $query->get($parameter, "");
                if (!($value === "")) {
                    $resourceData[$parameter] = $value;
                }
            }
            return $resourceData;
        }

        /**
         * Creates a new resource in the database and returns the ID in a JSON string.
         *
         * @param Request $request the HTTP Request
         * @return Response the HTTP Response containing the resource ID in a JSON String
         */
        public function createResource(Request $request) : Response {
            // get the ontology id from the request
            $ontologyID = $request->query->get("ontologyID");
            // get the resource data from the query
            $resourceData = $this->getResourceFromQuery($request->query);
            $json = APIHandler::createResource(
                $ontologyID, 
                $resourceData
            );
            return $this->successResponse($json);
        }


        /**
         * Saves the resource data to the database.
         *
         * @param Request $request the HTTP request
         * @return Response the response containing JSON data with status information about the saving of the resource
         */
        public function saveResource(Request $request) : Response {
            // get the resource ID from the query
            $resourceID = $request->query->get("resourceID");
            // get the resource data from the query
            $resourceData = $this->getResourceFromQuery($request->query);
            $json = APIHandler::saveResource(
                $resourceID,
                $resourceData
            );
            return $this->successResponse($json);
        }

        /**
         * Returns the data about the resource.
         *
         * @param Request $request the HTTP request
         * @return Response the HTTP response containing the resource data as a JSON String
         */
        public function getResource(Request $request) : Response {
            // get the resource ID from request
            $resourceID = $request->query->get("id");
            // get the data about the resource
            $json = APIHandler::getResource($resourceID);
            return $this->successResponse($json);
        }

        /**
         * Deletes a resource from the annotator.
         *
         * @param Request $request the request containing the id of the resource to delete
         * @return Response the status of the deletion
         */
        public function deleteResource(Request $request) : Response {
            // get the resource id from the request
            $resourceID = $request->query->get("id");
            // get the JSON String of the deletion status
            $json = APIHandler::deleteResource($resourceID);
            return $this->successResponse($json);
        }


        /**
         * Adds a term annotation to the resource and returns JSON containing all the annotated terms.
         *
         * @param Request $request the HTTP request containing the resource id and the term
         * @return Response the repsonse containing the JSON of the terms
         */
        public function addResourceTerm(Request $request) : Response {
            // get the resource id and term URI from the request
            $resourceID = $request->query->get("resourceID");
            $termURI = $request->query->get("term");
            // get the JSON String of the deletion status
            $json = APIHandler::addResourceTerm($resourceID, $termURI);
            return $this->successResponse($json);
        }

        /**
         * Removes a term annotation from the resource and returns JSON containing all the annotated terms.
         *
         * @param Request $request the HTTP request containing the resource id and the term
         * @return Response the repsonse containing the JSON of the terms
         */
        public function removeResourceTerm(Request $request) : Response {
            // get the resource id and term URI from the request
            $resourceID = $request->query->get("resourceID");
            $termURI = $request->query->get("term");
            // get the JSON String of the deletion status
            $json = APIHandler::removeResourceTerm($resourceID, $termURI);
            return $this->successResponse($json);
        }

    }

?>