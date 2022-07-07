<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    use App\Entity\API\APIHandler;

    use App\Entity\API\JSONFormatter;

    /**
     * Class to handle requests to the API.
     */
    class APIController {

        /**
         * Returns the JSON for the terms matching the search term from the ontology.
         *
         * @param Request $request the HTTP request
         * @return Response the JSON string of the terms and their metadata
         */
        public function searchTerms(Request $request) : Response {
            // get the search string from the request and get the results JSON String
            $searchQuery = $request->query->get("search");
            $jsonString = APIHandler::searchTerms("ontologyID", $searchQuery); 
                // replace with real ontology ID string when developed
            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }


        /**
         * Returns the data for a term in JSON format.
         *
         * @param Request $request the HTTP request
         * @return Response the JSON string of the term's metadata
         */
        public function getTerm(Request $request) : Response {
            // get the term URI from the request and get the term's JSON String
            $termURI = $request->query->get("term");
            $jsonString = APIHandler::getTerm("ontologyID", $termURI); 
                // replace with real ontology ID string when developed
            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }



        /**
         * Returns the data about the object property in JSON format
         *
         * @param Request $request the HTTP request containing the URI of the object property
         * @return Response the response containing the JSON data about the object property
         */
        public function getObjectProperty(Request $request) : Response {
            // get the property URI from the request and get the property's JSON string
            $propertyURI = $request->query->get("property");
            $jsonString = APIHandler::getObjectProperty("ontologyID", $propertyURI); 
                // replace with real ontology ID string when developed
            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }




        /**
         * Saves the resource data to the database.
         *
         * @param Request $request the HTTP request
         * @return Response the response containing JSON data with status information about the saving of the resource
         */
        public function saveResource(Request $request) : Response {

            // get the data about the resource as an associative array and pass it to the handler
            $resourceData = json_decode($request->getContent(), true);
            $jsonString = APIHandler::saveResource("ontologyID", "resourceID", $resourceData); 
                // replace with real ontology and resource IDs string when developed

            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }
    }

?>