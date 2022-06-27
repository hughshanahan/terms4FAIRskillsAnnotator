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
         * Returns the front end for testing the searching of classes and the search classes API.
         *
         * @param Request $request the HTTP request
         * @return Response the 
         */
        public function searchClasses(Request $request) : Response {

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
            foreach ($classes as $class) {
                array_push(
                    $data, 
                    array(
                        "label"=>$class->getLabel(),
                        "about"=>$class->getAbout()
                    )
                );
            }

            // return the response
            return new Response(
                JSONFormatter::arrayToString($data),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }
    }

?>