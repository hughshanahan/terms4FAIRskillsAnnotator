<?php

    namespace App\Entity\Ontology;

    use App\Entity\Ontology\OWLReader;

    /**
     * Stores the ontology file and allows it to be queried.
     */
    class Ontology {

        private $about; // String storing what the ontology is about
        private $description; // string storing the description of the ontology
        private $contributors; // array of the contributors names
        private $creators; // array of the creators names

        /**
         * Constructs an Ontology object.
         * 
         * @param String $filepath the filepath or URL to the ontology file (.owl)
         */
        public function __construct(String $filepath) {
            $xml = simplexml_load_string(file_get_contents($filepath));

            // intialise the variables that are going to store the ontology
            $this->about = "";
            $this->description = "";
            $this->contributors = array();
            $this->creators = array();

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
             // retrive what the ontology is about from the elements attributes
             $attributes = OWLReader::getAttributes($element);
             $this->about = $attributes["rdf:about"];


            // for each of the child elements of the owl:Ontology element
            foreach (OWLReader::getChildren($element) as $child) {

                // get the child element's fully qualifed name and process it accordingly
                $childName = OWLReader::getFullyQualifiedName($child);

                if ($childName == "dc:contributor") {
                    // add the contributor to the array of contributors
                    array_push($this->contributors, strval($child));
                } else if ($childName == "dc:creator") {
                    // add the creator to the array of creators
                    array_push($this->creators, strval($child));
                } else if ($childName == "terms:description") {
                    // set the description
                    $this->description = strval($child);
                }

            }
        }


        /**
         * Returns a string containing what the ontology is about.
         *
         * @return String what the ontology is about
         */
        public function getAbout() : String {
            return $this->about;
        }

        /**
         * Returns a string containing the ontology description.
         *
         * @return String the ontology description
         */
        public function getDescription() : String {
            return $this->description;
        }


        /**
         * Returns an array of the contributors names.
         *
         * @return array an array of the contributors
         */
        public function getContributors() : array {
            return $this->contributors;
        }

        /**
         * Returns an array of the creators names.
         *
         * @return array an array of the creators
         */
        public function getCreators() : array {
            return $this->creators;
        }

    }


?>