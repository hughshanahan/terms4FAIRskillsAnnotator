<?php

    namespace App\Entity\API;

    use App\Entity\Ontology\Ontology;


    /**
     * Class to process requests made to the API controller.
     */
    class APIHandler {

        private $ontology;

        /**
         * Constructs an instance of the APIHandler class.
         */
        public function __construct() {
            // create the ontology object
            // currently from the t4fs.owl file in the tests directory
            $this->ontology = new Ontology("tests/Resources/t4fs.owl");
        }






    }

?>