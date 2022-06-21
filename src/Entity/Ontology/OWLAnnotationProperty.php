<?php

    namespace App\Entity\Ontology;

    /**
     * Stores an annotation property from the ontology.
     */
    class OWLAnnotationProperty {


        public function __construct(\SimpleXMLElement $element) {

            // check that the element is an owl:class element
            if (!(OWLReader::getFullyQualifiedName($element) == "owl:AnnotationProperty")) {
                throw new \Exception(
                    "Attempted to create OWLAnnotationProperty object from an element that was not an owl:AnnotationProperty element"
                );
            }



        }

    }


?>