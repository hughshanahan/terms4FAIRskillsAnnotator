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
         * Renders the HTML template for the annotator.
         *
         * @return Response The HTTP response that contains the annotator HTML
         */
        public function main() : Response {
            return $this->render('annotator/annotator.html.twig');
        }
    }

?>