<?php

    namespace App\Entity\Ontology;

    /**
     * Stores an object property from the ontology.
     */
    class OWLObjectProperty {

        private $about; // String storing what the object property is about

        /**
         * Constructs a OWLObjectProperty object from an owl:ObjectProperty element.
         *
         * @param \SimpleXMLElement $element an owl:ObjectProperty element
         */
        public function __construct(\SimpleXMLElement $element) {

            // check that the element is an owl:ObjectProperty element
            if (!(OWLReader::getFullyQualifiedName($element) == "owl:ObjectProperty")) {
                throw new \Exception(
                    "Attempted to create OWLAnnotationProperty object from an element that was not an owl:AnnotationProperty element"
                );
            }

            // get the element attributes and store what the property is about
            $attributes = OWLReader::getAttributes($element);
            $this->about = $attributes["rdf:about"];

        }


        /**
         * Returns what the OWLObjectProperty is about.
         *
         * @return String what the Object Property is about
         */
        public function getAbout() : String {
            return $this->about;
        }


    }


?>