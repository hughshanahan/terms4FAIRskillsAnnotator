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

        private $description;

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

            $this->description = "";

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
                    $this->getParentClassDetails($child, $childAttributes);
                } else if ($childName == "rdfs:comment") {
                    array_push($this->comments, strval($child));
                } else if ($childName == "obo:IAO_0000115") {
                    $this->description = strval($child);
                }
            }
        }


        /**
         * Process the rdfs:SubClassOf element to get the details of the parent class 
         * and the relationship between the classes.
         *
         * @param \SimpleXMLElement $subclassElement the rdfs:SubClassOf element
         * @return void
         */
        private function getParentClassDetails(\SimpleXMLElement $subclassElement) : void {
            $attributes = OWLReader::getAttributes($subclassElement);



            // if attributes contains the rdf:resource
            if (array_key_exists("rdf:resource", $attributes)) {
                // add to the parent classes array with no restriction
                array_push($this->parentClasses, array("parent"=>$attributes["rdf:resource"]));
            } else {
                // there is a restriction on the relation to the parent class

                // get the children of the restriction element 
                // that is the first child of the SubClassElement
                $restrictionProperties = OWLReader::getChildren(
                    OWLReader::getChildren($subclassElement)[0]
                );
                
                // read the data from the XML element
                $restrictionData = array();
                $parentClass = "";
                foreach ($restrictionProperties as $property) {
                    // get the property details
                    $propertyName = OWLReader::getFullyQualifiedName($property);
                    $propertyAttributes = OWLReader::getAttributes($property);

                    // process the property and add the value to the restriction data array
                    if ($propertyName == "owl:onProperty") {
                        $restrictionData["property"] = $propertyAttributes["rdf:resource"];
                    } else if ($propertyName == "owl:someValuesFrom") {
                        $restrictionData["relationship"] = "someValues";
                        $parentClass = $propertyAttributes["rdf:resource"];
                    } else if ($propertyName == "owl:allValuesFrom") {
                        $restrictionData["relationship"] = "allValues";
                        $parentClass = $propertyAttributes["rdf:resource"];
                    }
                }

                // add the parent class to the parent classes array with the restriction data
                array_push(
                    $this->parentClasses, 
                    array(
                        "parent"=>$parentClass,
                        "restriction"=>$restrictionData
                    )
                );
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


        /**
         * Returns the description of the class.
         *
         * @return String the class description
         */
        public function getDescription() : String {
            return $this->description;
        }



        /**
         * Returns an array that can then be converted into JSON by the JSONFormatter.
         *
         * @return array an array ready to be converted to JSON
         */
        public function getJSONArray() : array {
            $data = array();

            $data["label"] = $this->getLabel();
            $data["about"] = $this->getAbout();
            $data["parents"] = $this->getParentClasses();
            $data["comments"] = $this->getComments();
            $data["description"] = $this->getDescription();

            return $data;
        }

    }

?>