<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;

    /**
     * Class to handle requests to routes that are related to the annotator.
     */
    class AnnotatorController extends AbstractController {

        /**
         * Returns the page response for the terms search.
         *
         * @return Response the HTTP response containing the terms search
         */
        public function termsSearch() : Response {
            return $this->render('termssearch.html.twig');
        }

    }

?>