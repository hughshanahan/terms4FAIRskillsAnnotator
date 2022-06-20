<?php

    namespace App\Entity\Ontology;

    /**
     * Stores a class from the ontology.
     */
    class OWLClass {

        /**
         * Constructs a class object from an owl:class element.
         *
         * @param \SimpleXMLElement $element an owl:class element
         */
        public function __construct(\SimpleXMLElement $element) {
            // check that the element is an owl:class element
            if (!($this->getFullyQualifiedName($element) == "owl:class")) {
                throw new \Exception(
                    "Attempted to create OntologyClass object from an element that was not an owl:class element"
                );
            }

        }


        /**
         * Get the fully qualifed name for the given XML element.
         *
         * @param \SimpleXMLElement $element the XML element
         * @return String the given element's fully qualified name
         */
        private function getFullyQualifiedName(\SimpleXMLElement $element) : String {
            $namespaces = $element->getNamespaces();
            if (count($namespaces) == 0) {
                // there are no namespaces defined - return just the name
                return $element->getName();
            }
            // else return the namespace followed by the name
            return $element->getNamespaces()[0] . ":" . $element->getName();
        }

    }

?>