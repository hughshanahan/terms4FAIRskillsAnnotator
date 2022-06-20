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
            if (!($this->getFullyQualifiedName($element) == "owl:Class")) {
                throw new \Exception(
                    "Attempted to create OntologyClass object from an element that was not an owl:Class element"
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

    }

?>