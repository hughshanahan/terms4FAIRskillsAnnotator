<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\InputBag;

    use App\Controller\APIController;

    use App\Entity\API\ResourceAPIHandler;

    /**
     * Class to handle requests to the Resource API endpoints.
     */
    class ResourceAPIController extends APIController {

        /**
         * Creates a new resource in the database and returns the ID in a JSON string.
         *
         * @param Request $request the HTTP Request
         * @return Response the HTTP Response containing the resource ID in a JSON String
         */
        public function create(Request $request) : Response {
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
                $json = ResourceAPIHandler::create(
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
        public function save(Request $request) : Response {
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
                $json = ResourceAPIHandler::save(
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
        public function get(Request $request) : Response {
            try {
                // get the resource ID from request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                // get the data about the resource
                $json = ResourceAPIHandler::get($resourceID);
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
        public function delete(Request $request) : Response {
            try {
                // get the resource id from the request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                // get the JSON String of the deletion status
                $json = ResourceAPIHandler::delete($resourceID);
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
        public function addTerm(Request $request) : Response {
            try {
                // get the resource id and term URI from the request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                $termURI = $this->getRequiredParameter($request->query, "term");
                // get the JSON String of the deletion status
                $json = ResourceAPIHandler::addTerm($resourceID, $termURI);
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
        public function removeTerm(Request $request) : Response {
            try {
                // get the resource id and term URI from the request
                $resourceID = $this->getRequiredParameter($request->query, "resourceID");
                $termURI = $this->getRequiredParameter($request->query, "term");
                // get the JSON String of the deletion status
                $json = ResourceAPIHandler::removeTerm($resourceID, $termURI);
                return $this->successResponse($json);
            } catch (\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }

    }

?>