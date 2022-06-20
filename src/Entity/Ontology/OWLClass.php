<?php

    namespace App\Entity\Ontology;

    /**
     * Stores a class from the ontology.
     */
    class OWLClass {

        private $about; // stores the URI for the class

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
         * Get the fully qualifed name for the given XML element.
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