<?php

    namespace App\Entity\Ontology;

    use App\Entity\Ontology\OWLReader;

    /**
     * Stores a class from the ontology.
     */
    class OWLClass {

        private $about; // stores the URI for the class
        private $label; // stores the class' label
        private $parentClasses; // stores an array of the URIs for the classes that this class is a sub class of
        private $comments; // array of comments about the class

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
            $this->label = "";
            $this->parentClasses = array();
            $this->comments = array();

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
            // process each of the child elements
            foreach (OWLReader::getChildren($element) as $child) {

                // get the name and attributes of the child element
                $childName = OWLReader::getFullyQualifiedName($child);
                $childAttributes = OWLReader::getAttributes($child);

                // process the child element based on the kind of element that it is
                if ($childName == "rdfs:label") {
                    $this->label = strval($child);
                } else if ($childName == "rdfs:subClassOf") {
                    // the child element stores a URI of another class that this class is a sub class of
                    // add the rdf:resource value to the parent classes array
                    array_push($this->parentClasses, $childAttributes["rdf:resource"]);
                } else if ($childName == "rdfs:comment") {
                    array_push($this->comments, strval($child));
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
         * Returns the class' label.
         *
         * @return String the class' label
         */
        public function getLabel() : String {
            return $this->label;
        }


        /**
         * Returns the list of URIs that represent the class' parent classes.
         *
         * @return array an array of URIs that represent the parent classes
         */
        public function getParentClasses() : array {
            return $this->parentClasses;
        }

        /**
         * Returns an array of the comments about the class.
         *
         * @return array the array of comments
         */
        public function getComments() : array {
            return $this->comments;
        }

    }

?>