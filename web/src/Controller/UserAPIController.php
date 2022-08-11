<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\InputBag;

    use App\Controller\APIController;

    use App\Entity\API\UserAPIHandler;

    /**
     * Class to handle requests to the User API endpoints.
     */
    class UserAPIController extends APIController {


        /**
         * Creates a user in the database and returns the userID.
         *
         * @param Request $request the HTTP request
         * @return Response the response containing the JSON data with the userID  
         */
        public function create(Request $request) : Response {
            try {
                $json = UserAPIHandler::create();
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
        public function getOntologies(Request $request) : Response {
            try {
                $userID = $this->getRequiredParameter($request->query, "userID");
                $json = UserAPIHandler::getOntologies($userID);
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
        public function delete(Request $request) : Response {
            try {
                $userID = $this->getRequiredParameter($request->query, "userID");
                $json = UserAPIHandler::delete($userID);
                return $this->successResponse($json);
            } catch(\Exception $exception) {
                return $this->errorResponse($exception);
            }
        }


    }

?>