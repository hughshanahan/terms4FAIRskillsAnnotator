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
        protected function successResponse(String $json) : Response {
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
        protected function errorResponse(\Exception $exception) : Response {
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
        protected function getRequiredParameter(InputBag $query, String $parameter) : String {
            $value = $query->get($parameter, "");
            if ($value === "") {
                throw new \Exception("Parameter '" . $parameter . "' missing");
            }
            return $value;
        }

    }

?>