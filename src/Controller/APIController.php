<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    use App\Entity\API\APIHandler;

    use App\Entity\API\JSONFormatter;

    use App\Entity\Ontology\Ontology;

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

            // get the search string
            $searchQuery = $request->query->get("search");

            $handler = new APIHandler("ontologyID"); // replace with real ontology ID string when developed
            $jsonString = $handler->searchTerms($searchQuery);

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

            // get the search string
            $termURI = $request->query->get("term");

            $handler = new APIHandler("ontologyID"); // replace with real ontology ID string when developed
            $jsonString = $handler->getTerm($termURI);

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
            // get the property URI from the request
            $propertyURI = $request->query->get("property");

            $handler = new APIHandler("ontologyID"); // replace with real ontology ID string when developed
            $jsonString = $handler->getObjectProperty($propertyURI);

            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );

        }
    }

?>