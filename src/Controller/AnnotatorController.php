<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;

    /**
     * Class to control HTTP requests to '/' and '/annotator'.
     * These are used to display the form that is used to select the keywords from the ontology file and initiate the generation of the output file.
     */
    class AnnotatorController extends AbstractController {

        /**
         * Returns the page response for the annotator.
         *
         * @return Response The HTTP response that contains the annotator
         */
        public function main() : Response {
            return $this->render('annotator/annotator.html.twig');
        }


        /**
         * Returns the page response for the terms search.
         *
         * @return Response the HTTP response containing the terms search
         */
        public function termsSearch() : Response {
            return $this->render('annotator/termsSearch.html.twig');
        }

    }

?>
