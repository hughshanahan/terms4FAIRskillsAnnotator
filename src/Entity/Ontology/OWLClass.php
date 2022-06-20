<?php

    namespace App\Entity\Ontology;

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
            if (!($this->getFullyQualifiedName($element) == "owl:Class")) {
                throw new \Exception(
                    "Attempted to create OntologyClass object from an element that was not an owl:Class element"
                );
            }

            // get the element attributes and store what the class is about
            $attributes = $this->getAttributes($element);
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
            $children = $this->getChildren($element);
            foreach ($children as $childElement) {
                // get the attributes of the child element
                $childElementAttributes = $this->getAttributes($childElement);
                // process the child element based on the kind of element that it is
                if ($this->getFullyQualifiedName($childElement) == "rdfs:subClassOf") {
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


        /**
         * Get the fully qualifed name for the given XML element.
         * This is in the format namespaceID:name.
         *
         * @param \SimpleXMLElement $element the XML element
         * @return String the given element's fully qualified name
         */
        private function getFullyQualifiedName(\SimpleXMLElement $element) : String {
            // get the namespace identifier via DOMNode object
            $node = dom_import_simplexml($element);
            $nsID = $node->prefix;
            if ($nsID == "") {
                // the element doesn't have a namespace, return the element name
                return $element->getName();
            }
            // the element has a namespace - return the fully qualifed name
            return $nsID . ":" . $element->getName();
        }


        /**
         * Returns an array of all the child elements for the given element.
         *
         * @param \SimpleXMLElement $element the ekement to get the children for
         * @return array an array of SimpleXMLElement objects that are the child elements for the given element
         */
        private function getChildren(\SimpleXMLElement $element) : array {
            $children = array();
            foreach ($element->getDocNamespaces() as $ns => $url) {
                foreach ($element->children($url) as $child) {
                    array_push($children, $child);
                }
            }
            return $children;
        }


        /**
         * Returns an dictionary containing all the attributes of the element.
         *
         * @param \SimpleXMLElement $element the element to get the attributes for
         * @return array the array of attributes in key-value pairs
         */
        private function getAttributes(\SimpleXMLElement $element) : array {
            $attributes = array();
            foreach ($element->getDocNamespaces() as $ns => $url) {
                foreach ($element->attributes($url) as $key => $value) {
                    $attributes[$ns . ":" . $key] = $value;
                }
            }
            return $attributes;
        }

    }

?>