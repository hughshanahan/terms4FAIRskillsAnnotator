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
            $jsonString = APIHandler::searchTerms(
                $request->cookies->get("annotator-ontology-id"), 
                $searchQuery
            );
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
            $jsonString = APIHandler::getTerm(
                $request->cookies->get("annotator-ontology-id"), 
                $termURI
            );
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
            $jsonString = APIHandler::getObjectProperty(
                $request->cookies->get("annotator-ontology-id"), 
                $propertyURI
            ); 
            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }



        /**
         * Loads the ontology into the database ready to use for annotating resources.
         *
         * @param Request $request the request containing the URL of the ontology to store.
         * @return Response The response containing JSON data with the database id of the ontology 
         */
        public function loadOntology(Request $request) : Response {
            // get the data as an associative array and pass it to the handler
            $requestData = json_decode($request->getContent(), true);
            $jsonString = APIHandler::loadOntology(
                $requestData["ontology-url-input"]
            );

            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
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
            $jsonString = APIHandler::getResource($resourceID);
            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }


        /**
         * Creates a new resource in the database and returns the ID in a JSON string.
         *
         * @param Request $request the HTTP Request
         * @return Response the HTTP Response containing the resource ID in a JSON String
         */
        public function createResource(Request $request) : Response {
            // get the ontology id from the cookies 
            $ontologyID = $request->cookies->get("annotator-ontology-id");

            // get the data from the content and create the resource
            $resourceData = json_decode($request->getContent(), true);
            $jsonString = APIHandler::createResource(
                $ontologyID, 
                $resourceData
            );

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
            // get the resource ID from the cookies
            $resourceID = $request->cookies->get("annotator-resource-id");
            // get the data about the resource as an associative array and pass it to the handler
            $resourceData = json_decode($request->getContent(), true);
            $jsonString = APIHandler::saveResource(
                $resourceID,
                $resourceData
            );

            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
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
            $jsonString = APIHandler::exportAnnotations($ontologyID);

            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
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
            $jsonString = APIHandler::getOntologyResources($ontologyID);

            // return the response
            return new Response(
                $jsonString,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }
    }

?>