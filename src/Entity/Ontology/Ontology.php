<?php

    namespace App\Entity\Ontology;

    use App\Entity\Ontology\OWLReader;

    /**
     * Stores the ontology file and allows it to be queried.
     */
    class Ontology {

        private $about; // String storing what the ontology is about
        private $description; // string storing the description of the ontology
        private $license; // string storing the license of the ontology
        private $contributors; // array of the contributors names
        private $creators; // array of the creators names
        private $comments; // array of comments about the ontology

        private $classes; // stores a dictionary of strings to OWLClass objects 
        private $classesIndexed; // stores a dictionary of the class labels to class about properties

        /**
         * Constructs an Ontology object.
         * 
         * @param String $filepath the filepath or URL to the ontology file (.owl)
         */
        public function __construct(String $filepath) {
            $xml = simplexml_load_string(file_get_contents($filepath));

            // intialise the variables that are going to store the ontology
            $this->about = "";
            $this->description = "";
            $this->license = "";
            $this->contributors = array();
            $this->creators = array();
            $this->comments = array();

            $this->classes = array();
            $this->classesIndexed = array();

            // for each direct child of the root element in the ontology
            foreach (OWLReader::getChildren($xml) as $element) {
                // get the fully qualifed name and handle the element depending on the name
                $elementName = OWLReader::getFullyQualifiedName($element);

                if ($elementName == "owl:Ontology") {
                    $this->processOntologyElement($element);
                } else if ($elementName == "owl:Class") {
                    $classElement = new OWLClass($element);
                    $this->classes[$classElement->getAbout()] = $classElement;
                }

            }


            // create a secondary index of the class labels to the about
            foreach ($this->classes as $classAbout => $classElement) {
                $this->classesIndexed[$classElement->getLabel()] = $classAbout;
            }

        }

        /**
         * Processes the owl:Ontology element that defines the properties for the ontology.
         * This should only be called from the constructor.
         *
         * @param \SimpleXMLElement $element the owl:Ontology element
         * @return void
         */
        private function processOntologyElement(\SimpleXMLElement $element) : void {
             // retrive what the ontology is about from the elements attributes
             $attributes = OWLReader::getAttributes($element);
             $this->about = $attributes["rdf:about"];


            // for each of the child elements of the owl:Ontology element
            foreach (OWLReader::getChildren($element) as $child) {

                // get the child element's fully qualifed name and process it accordingly
                $childName = OWLReader::getFullyQualifiedName($child);

                if ($childName == "terms:description") {
                    // set the description
                    $this->description = strval($child);
                } else if ($childName == "terms:license") {
                    // set the license
                    $this->license = strval($child);
                } else if ($childName == "dc:contributor") {
                    // add the contributor to the array of contributors
                    array_push($this->contributors, strval($child));
                } else if ($childName == "dc:creator") {
                    // add the creator to the array of creators
                    array_push($this->creators, strval($child));
                } else if ($childName == "rdfs:comment") {
                    // add the comment to the comments array
                    array_push($this->comments, strval($child));
                }

            }
        }


        /**
         * Returns a string containing what the ontology is about.
         *
         * @return String what the ontology is about
         */
        public function getAbout() : String {
            return $this->about;
        }

        /**
         * Returns a string containing the ontology description.
         *
         * @return String the ontology description
         */
        public function getDescription() : String {
            return $this->description;
        }


        /**
         * Returns a string containing the ontology license.
         *
         * @return String the ontology license
         */
        public function getLicense() : String {
            return $this->license;
        }


        /**
         * Returns an array of the contributors names.
         *
         * @return array an array of the contributors
         */
        public function getContributors() : array {
            return $this->contributors;
        }

        /**
         * Returns an array of the creators names.
         *
         * @return array an array of the creators
         */
        public function getCreators() : array {
            return $this->creators;
        }

        /**
         * Returns an array of the comments in the ontology.
         *
         * @return array the array of comments
         */
        public function getComments() : array {
            return $this->comments;
        }


        /**
         * Get the array of OWLClass objects that have a label matching the given query.
         * This is a case insensitive search.
         *
         * @param String $query the query to match
         * @return array array of OWLClass objects with labels matching the query
         */
        public function queryClasses(String $query) : array {
            // get the keys that match the query from the classIndexed dictionary
            $keys = array_keys($this->classesIndexed);
            $matchingKeys = preg_grep("/" . $query . "/i", $keys); // the query needs to be converted to regex
            // create an array of OWLClass objects from the matching keys
            $results = array();
            foreach ($matchingKeys as $matchingKey) {
                array_push($results, $this->classes[$this->classesIndexed[$matchingKey]]);
            }
            return $results;
        }


    }


?>