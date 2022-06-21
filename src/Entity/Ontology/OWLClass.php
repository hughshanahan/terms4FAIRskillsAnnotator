<?php

    namespace App\Entity\Ontology;

    use App\Entity\Ontology\OWLReader;

    /**
     * Stores a class from the ontology.
     */
    class OWLClass {

        private $about; // stores the URI for the class
        private $parentClasses; // stores an array of the URIs for the classes that this class is a sub class of

        /**
         * Constructs a class object from an owl:class element.
         *
         * @param \SimpleXMLElement $element an owl:class element
         */
        public function __construct(\SimpleXMLElement $element) {
            // check that the element is an owl:class element
            if (!(OWLReader::getFullyQualifiedName($element) == "owl:Class")) {
                throw new \Exception(
                    "Attempted to create OWLClass object from an element that was not an owl:Class element"
                );
            }

            // get the element attributes and store what the class is about
            $attributes = OWLReader::getAttributes($element);
            $this->about = $attributes["rdf:about"];

            // initialise properties that are going to have values set from the child elements
            $this->parentClasses = array();

            $this->processChildElements($element);
        }

        /**
         * Process the child elements of the element.
         * This method should only be called in the constructor after the variables that it uses have been initalised.
         *
         * @param \SimpleXMLElement $element the owl:Class element
         * @return void
         */
        private function processChildElements(\SimpleXMLElement $element) : void {
            // get an array of the child elements and process them
            $children = OWLReader::getChildren($element);
            foreach ($children as $childElement) {
                // get the attributes of the child element
                $childElementAttributes = OWLReader::getAttributes($childElement);
                // process the child element based on the kind of element that it is
                if (OWLReader::getFullyQualifiedName($childElement) == "rdfs:subClassOf") {
                    // the child element stores a URI of another class that this class is a sub class of
                    // add the rdf:resource value to the parent classes array
                    array_push($this->parentClasses, $childElementAttributes["rdf:resource"]);
                }
            }
        }


        /**
         * Returns the URI that the class represents.
         *
         * @return String
         */
        public function getAbout() : String {
            return $this->about;
        }

        /**
         * Returns the list of URIs that represent the class' parent classes.
         *
         * @return array an array of URIs that represent the parent classes
         */
        public function getParentClasses() : array {
            return $this->parentClasses;
        }

    }

?>