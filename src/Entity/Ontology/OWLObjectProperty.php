<?php

    namespace App\Entity\Ontology;

    /**
     * Stores an object property from the ontology.
     */
    class OWLObjectProperty {

        /**
         * Constructs a OWLObjectProperty object from an owl:ObjectProperty element.
         *
         * @param \SimpleXMLElement $element an owl:ObjectProperty element
         */
        public function __construct(\SimpleXMLElement $element) {

            // check that the element is an owl:class element
            if (!(OWLReader::getFullyQualifiedName($element) == "owl:ObjectProperty")) {
                throw new \Exception(
                    "Attempted to create OWLAnnotationProperty object from an element that was not an owl:AnnotationProperty element"
                );
            }

        }


    }


?>