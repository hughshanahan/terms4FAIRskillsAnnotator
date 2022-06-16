<?php

    namespace App\Entity;

    /**
     * Stores the data from the ontology file.
     * The ontology file stored is https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl
     */
    class Ontology {

        private $raw;

        /**
         * Constructs an Ontology object.
         * The ontology is read from https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl and processed into data structures.
         */
        public function __construct() {
            // read the raw ontology into a string
            $this->raw = file_get_contents("https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl");
        }

        /**
         * Returns the ontology as a string.
         *
         * @return String the ontology
         */
        public function getRaw() : String {
            return $this->raw;
        }


    }


?>