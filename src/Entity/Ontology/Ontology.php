<?php

    namespace App\Entity\Ontology;

    /**
     * Stores the data from the ontology file.
     * The ontology file stored is https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl
     */
    class Ontology {

        private $raw;
        private $xml;

        /**
         * Constructs an Ontology object.
         * The ontology is read from https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl and processed into data structures.
         */
        public function __construct() {
            // read the raw ontology into a string
            $this->raw = file_get_contents("https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl");
            // create a xml object from the string
            $this->xml = simplexml_load_string($this->raw);
        }

        /**
         * Returns the ontology as a string.
         *
         * @return String the ontology
         */
        public function getRaw() : String {
            return $this->raw;
        }


        /**
         * Returns an XML object that contains the ontology.
         *
         * @return \SimpleXMLElement the ontology as an XML object
         */
        public function getXML() : \SimpleXMLElement {
            return $this->xml;
        }

    }


?>