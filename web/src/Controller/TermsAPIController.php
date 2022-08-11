<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\InputBag;

    use App\Controller\APIController;

    use App\Entity\API\APIHandler;

    /**
     * Class to handle requests to the Terms API endpoints.
     */
    class TermsAPIController extends APIController {

        /**
         * Returns the JSON for the terms matching the search term from the ontology.
         *
         * @param Request $request the HTTP request
         * @return Response the JSON string of the terms and their metadata
         */
        public function search(Request $request) : Response {
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
        public function get(Request $request) : Response {
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

    }

?>