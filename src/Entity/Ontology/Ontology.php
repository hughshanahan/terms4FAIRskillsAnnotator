<?php

    namespace App\Entity\Ontology;

    /**
     * Stores the ontology file and allows it to be queried.
     */
    class Ontology {

        private $contributors;

        /**
         * Constructs an Ontology object.
         * 
         * @param String $filepath the filepath or URL to the ontology file (.owl)
         */
        public function __construct(String $filepath) {
            $xml = simplexml_load_string(file_get_contents($filepath));


            // intialise the variables that are going to store the ontology
            $this->contributors = array();


            // for each direct child of the root element in the ontology
            foreach (OWLReader::getChildren($xml) as $element) {
                // get the fully qualifed name and handle the element depending on the name
                $elementName = OWLReader::getFullyQualifiedName($element);

                if ($elementName == "owl:Ontology") {
                    $this->processOntologyElement($element);
                }


            }
        }

        /**
         * Processes the owl:Ontology element that defines the properties for the ontology.
         * This should only be called from the constructor.
         *
         * @param \SimpleXMLElement $element the owl:Ontology element
         * @return void
         */
        private function processOntologyElement(\SimpleXMLElement $element) : void {
            // for each of the child elements of the owl:Ontology element
            foreach (OWLReader::getChildren($element) as $child) {

                // get the child element's fully qualifed name and process it accordingly
                $childName = OWLReader::getFullyQualifiedName($child);

                if ($childName == "dc:contributor") {
                    // add the contributor to the array of contributors
                    array_push($this->contributors, strval($child));
                }

            }
        }



        /**
         * Returns an array of the contributors names.
         *
         * @return array an array of the contributors
         */
        public function getContributors() : array {
            return $this->contributors;
        }

    }


?>