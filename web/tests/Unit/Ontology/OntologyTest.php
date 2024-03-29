<?php

    namespace App\Tests\Unit\Ontology;
    use PHPUnit\Framework\TestCase;
    use App\Entity\Ontology\Ontology;

    /**
     * Tests the Ontology Class.
     */
    class OntologyTest extends TestCase {

        private $ontology;

        /**
         * Set Up method to create an instance of the Ontology class before each test.
         * The ontology files are loaded from tests/Resources/. 
         * The absolute path of this directory in the Docker container is /var/www/tests/Resources/.
         *
         * @return void
         */
        protected function setUp() : void {
            $this->ontology = new Ontology("tests/Resources/OntologyTest.owl");
        }

        /**
         * Tests that the object is correctly instantiated from the filepath provided in the constructor.
         *
         * @return void
         */
        public function testCorrectlyInstantiated() : void {
            $this->assertInstanceOf(Ontology::class, $this->ontology);
        }


        /**
         * Tests that the Ontology class can return what the ontology is about.
         *
         * @return void
         */
        public function testGetAbout() : void {
            $this->assertEquals(
                "Ontology",
                $this->ontology->getAbout(),
                "The getAbout method did not return the expected value for what the ontology is about"
            );
        }


        /**
         * Tests that the Ontology class can return the description of the ontology.
         *
         * @return void
         */
        public function testGetDescription() : void {
            $this->assertEquals(
                "Ontology description",
                $this->ontology->getDescription(),
                "The ontology description was not the expected value"
            );
        }


        public function testGetLicense() : void {
            $this->assertEquals(
                "License URL",
                $this->ontology->getLicense(),
                "The returned license was not the expected license"
            );
        }


        /**
         * Tests that the correct list of contributors is returned.
         *
         * @return void
         */
        public function testGetContributors() : void {
            $this->assertEquals(
                array("Contributor 1", "Contributor 2"),
                $this->ontology->getContributors(),
                "The returned contributors array was not the expected array for the getContributors method"
            );
        }

        /**
         * Tests that the correct list of creators is returned.
         *
         * @return void
         */
        public function testGetCreators() : void {
            $this->assertEquals(
                array("Creator 1", "Creator 2"),
                $this->ontology->getCreators(),
                "The returned creators array was not the expected array for the getCreators method"
            );
        }


        /**
         * Tests that the correct array of comments is returned.
         *
         * @return void
         */
        public function testGetComments() : void {
            $this->assertEquals(
                array("Comment 1", "Comment 2"),
                $this->ontology->getComments(),
                "The returned array of comments was not the expected array of comments from the ontology"
            );
        }


        /**
         * Tests that the URI of the class returned from getClass is the URI that was requested.
         *
         * @return void
         */
        public function testGetClass() : void {
            $class = $this->ontology->getClass("Resource_1");
            $this->assertEquals(
                "Resource_1",
                $class->getAbout(),
                "The URI of the return class did not match the requested URI"
            );
        }


        /**
         * Tests that the correct classes are returned when querying the ontology classes.
         * The test should return the classes that have the Resource in the label, this should therefore
         * not return the classes that don't have the word resource in the label, such as the Alternative classes.
         *
         * @return void
         */
        public function testQueryClasses() : void {
            // get the classes
            $classes = $this->ontology->queryClasses("Resource");
            $labels = array();
            foreach ($classes as $class) {
                array_push($labels, $class->getLabel());
            }

            // assert the labels array is the correct array
            $this->assertEquals(
                array("Resource 1 Label", "Resource 2 Label"),
                $labels,
                "The selected labels were not the correct labels"
            );
        }



        /**
         * Tests that the URI of the object property returned from getObjectProperty is the URI that was requested.
         *
         * @return void
         */
        public function testGetObjectProperty() : void {
            $property = $this->ontology->getObjectProperty("Property_1");
            $this->assertEquals(
                "Property_1",
                $property->getAbout(),
                "The URI of the returned object property did not match the requested URI"
            );
        }

        /**
         * Tests that the serialise and unserialise methods can return the same object.
         *
         * @return void
         */
        public function testSerialisation() : void {
            $serial = Ontology::serialise($this->ontology);
            $object = Ontology::unserialise($serial);
            $this->assertEquals(
                $this->ontology,
                $object,
                "The unserialised object did not match the object that was serialised"
            );
        }

    }

?>
