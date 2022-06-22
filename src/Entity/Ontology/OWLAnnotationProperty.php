<?php

    namespace App\Entity\Ontology;

    /**
     * Stores an annotation property from the ontology.
     */
    class OWLAnnotationProperty {

        private $about; // String storing what the property is about
        private $label; // String storing the property's label


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


            // initialise properties that are going to have values set from the child elements
            $this->label = "";

            $this->processChildElements($element);
        }


        /**
         * Process the child elements of the element.
         * This method should only be called in the constructor after the variables that it uses have been initalised.
         *
         * @param \SimpleXMLElement $element the owl:AnnotationProperty element
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
                }
            }
        }




        /**
         * Returns what the annotation property is about.
         *
         * @return String what the property is about
         */
        public function getAbout() : String {
            return $this->about;
        }


        /**
         * Returns the property's label.
         *
         * @return String the property's label
         */
        public function getLabel() : String {
            return $this->label;
        }


    }


?>