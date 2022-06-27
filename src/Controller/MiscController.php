<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;

    /**
     * Class to handle requests to routes that do not need a dedicated controller of their own.
     */
    class MiscController extends AbstractController {

        /**
         * Returns the basic outline of the README.
         *
         * @return Response the HTTP response containing the outline of the README.
         */
        public function readme() : Response {

            $parsedown = new \Parsedown();
            $content = $parsedown->text(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../README.md"));

            return $this->render('misc.html.twig', [
                "title" => "README",
                "content" => $content,
            ]);
        }
    }

?>