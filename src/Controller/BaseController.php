<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;

    class BaseController {

        public function readme() : Response {

            return new Response(
                '<html><body><h1>terms4FAIRskillsAnnotator</h1><p>Tool for annotating materials with terms4FAIRskills ontology.</p></body></html>'
            );
        }
    }

?>