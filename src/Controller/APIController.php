<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    use App\Entity\API\JSONFormatter;

    use App\Entity\Ontology\Ontology;

    /**
     * Class to handle requests to the API
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

            // create the array to store the data that should be returned
            $data = array();

            // create the ontology object
            // currently from the t4fs.owl file in the tests directory
            $ontology = new Ontology($_SERVER["DOCUMENT_ROOT"] . "/../tests/Resources/t4fs.owl");

            // search the ontology
            $classes = $ontology->queryClasses($searchQuery);

            // process the classes into the data array
            $results = array();
            foreach ($classes as $class) {
                array_push(
                    $results, 
                    $class->getJSONArray()
                );
            }

            //set the data properties
            $data["search"] = $searchQuery;
            $data["results"] = $results;

            // return the response
            return new Response(
                JSONFormatter::arrayToString($data),
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

            // create the ontology object
            // currently from the t4fs.owl file in the tests directory
            $ontology = new Ontology($_SERVER["DOCUMENT_ROOT"] . "/../tests/Resources/t4fs.owl");

            // get the class matching the URI
            $class = $ontology->getClass($termURI);

            // create the array to store the data that should be returned
            $data = $class->getJSONArray();

            // return the response
            return new Response(
                JSONFormatter::arrayToString($data),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );

        }
    }

?>