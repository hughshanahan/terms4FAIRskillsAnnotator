<?php

    namespace App\Entity\Ontology;

    /**
     * Stores an annotation property from the ontology.
     */
    class OWLAnnotationProperty {

        private $about; // String storing what the property is about


        public function __construct(\SimpleXMLElement $element) {

            // check that the element is an owl:class element
            if (!(OWLReader::getFullyQualifiedName($element) == "owl:AnnotationProperty")) {
                throw new \Exception(
                    "Attempted to create OWLAnnotationProperty object from an element that was not an owl:AnnotationProperty element"
                );
            }

            // get the element attributes and store what the property is about
            $attributes = OWLReader::getAttributes($element);
            $this->about = $attributes["rdf:about"];


        }


        /**
         * Returns what the annotation property is about.
         *
         * @return String what the property is about
         */
        public function getAbout() : String {
            return $this->about;
        }


    }


?>