<?php

    namespace App\Entity\Ontology;

    use App\Entity\Ontology\OWLReader;
    use App\Entity\Ontology\OWLClass;
    use App\Entity\Ontology\OWLObjectProperty;

    /**
     * Stores the ontology file and allows it to be queried.
     */
    class Ontology {

        private $url; // stores the URL that the ontology was loaded from

        private $about; // String storing what the ontology is about
        private $description; // string storing the description of the ontology
        private $license; // string storing the license of the ontology
        private $contributors; // array of the contributors names
        private $creators; // array of the creators names
        private $comments; // array of comments about the ontology

        private $classes; // stores a dictionary of strings to OWLClass objects 
        private $classesIndexed; // stores a dictionary of the class labels to class about properties

        private $objectProperties; // stores a dictionary of strings to OWLObjectProperty objects

        /**
         * Constructs an Ontology object.
         * 
         * @param String $url the URL (or filepath) of the ontology file (.owl)
         * @throws \Exception if the ontology URL is not valid
         */
        public function __construct(String $url) {

            $ontologyContents = @file_get_contents($url); 
                // the @ surpresses the warning if the URL is not valid
                // this means that the following check can handle the problem
            if ($ontologyContents === False) {
                throw new \Exception("Could not read ontology from '" . $url . "'");
            }

            $this->url = $url;

            $xml = simplexml_load_string($ontologyContents);

            // intialise the variables that are going to store the ontology
            $this->about = "";
            $this->description = "";
            $this->license = "";
            $this->contributors = array();
            $this->creators = array();
            $this->comments = array();

            $this->classes = array();
            $this->classesIndexed = array();

            $this->objectProperties = array();

            // for each direct child of the root element in the ontology
            foreach (OWLReader::getChildren($xml) as $element) {
                // get the fully qualifed name and handle the element depending on the name
                $elementName = OWLReader::getFullyQualifiedName($element);

                if ($elementName == "owl:Ontology") {
                    $this->processOntologyElement($element);
                } else if ($elementName == "owl:Class") {
                    $classElement = new OWLClass($element);
                    $this->classes[$classElement->getAbout()] = $classElement;
                } else if ($elementName == "owl:ObjectProperty") {
                    $propertyElement = new OWLObjectProperty($element);
                    $this->objectProperties[$propertyElement->getAbout()] = $propertyElement;
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
         * Returns a string containing the URL where the ontology was loaded from.
         *
         * @return String the URL where the ontology was loaded from
         */
        public function getURL() : String {
            return $this->url;
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
         * Returns the OWLClass object for the requested class.
         *
         * @param String $URI The URI of the class to return
         * @return OWLClass the OWLClass object
         */
        public function getClass(String $URI) : OWLClass {
            return $this->classes[$URI];
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


        /**
         * Returns the OWLObjectProperty object for the requested object property.
         *
         * @param String $URI The URI of the property to return
         * @return OWLObjectProperty the OWLObjectProperty object
         */
        public function getObjectProperty(String $URI) : OWLObjectProperty {
            return $this->objectProperties[$URI];
        }


        /**
         * Returns an array of the ontology metadata that can be converted to JSON.
         *
         * @return array the ontology metadata
         */
        public function getJSONArray() : array {
            $data = array();

            $data["url"] = $this->getURL();
            $data["about"] = $this->getAbout();
            $data["description"] = $this->getDescription();
            $data["license"] = $this->getLicense();
            $data["contributors"] = $this->getContributors();
            $data["creators"] = $this->getCreators();
            $data["comments"] = $this->getComments();

            return $data;
        }




        // Serialise and Unserialise methods
        // a serialised object is serialised, compressed and then base 64 encoded.

        /**
         * Serialises an Ontology object.
         *
         * @param Ontology $ontology the Ontology object to serialise
         * @return String the serialised object
         */
        public static function serialise(Ontology $ontology) : String {
            return base64_encode(
                gzcompress(
                    gzcompress(
                        serialize($ontology),
                        9
                    ),
                    9
                )
            );
        }

        /**
         * Unserialises an Ontology object.
         *
         * @param String $serial the serial string produced from the serialise method
         * @return Ontology the Ontology object
         */
        public static function unserialise(String $serial) : Ontology {
            return unserialize(
                gzuncompress(
                    gzuncompress(
                        base64_decode(
                            $serial
                        )
                    )
                )
            );
        }




    }


?>