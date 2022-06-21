<?php

    namespace App\Entity\Ontology;

    /**
     * Stores the ontology file and allows it to be queried.
     */
    class Ontology {

        /**
         * Constructs an Ontology object.
         * 
         * @param String $filepath the filepath or URL to the ontology file (.owl)
         */
        public function __construct(String $filepath) {
            $xml = simplexml_load_string(file_get_contents($filepath));
        }

    }


?>