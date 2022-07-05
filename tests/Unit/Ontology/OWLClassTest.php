<?php

    namespace App\Tests\Unit\Ontology;
    use PHPUnit\Framework\TestCase;
    use App\Entity\Ontology\OWLClass;

    /**
     * Tests the OWLClass Class.
     */
    class OWLClassTest extends TestCase {


        private $class;

        /**
         * Set Up method to create an instance of the OWLClass class before each test.
         *
         * @return void
         */
        protected function setUp() : void {

            $root = simplexml_load_string(file_get_contents("tests/Resources/OWLClassTest.owl"));
            $classElement = $root->xpath("//owl:Class")[0]; // get the first owl:Class element from the file
            $this->class = new OWLClass($classElement);
        }




        /**
         * Tests that an expection is thrown if an OWLClass object is attempted to be created from an XML element that isn't an owl:Class element.
         *
         * @return void
         */
        public function testInvalidElementType() : void {
            // tell the test case that the exception is going to be thrown
            $this->expectException(\Exception::class);
            // attempt to create the OWLClass object
            $xmlElement = simplexml_load_string("<invalidelement></invalidelement>");
            $object = new OWLClass($xmlElement);
        }


        /**
         * Tests that the object instantiated in the set up method is an OWLClass object.
         *
         * @return void
         */
        public function testCorrectlyInstantiated() : void {
            $this->assertInstanceOf(
                OWLClass::class, 
                $this->class,
                "The class was not an instance of OWLClass"
            );
        }


        /**
         * Tests that the object has correctly read the URI from the owl:Class element's rdf:about attribute.
         *
         * @return void
         */
        public function testAboutProperty() : void {
            $this->assertEquals(
                "Resource_1",
                $this->class->getAbout(),
                "The OWLClass object has not correctly read what the class is about"
            );
        }


        /**
         * Tests that the label is the expected label.
         *
         * @return void
         */
        public function testGetLabel() : void {
            $this->assertEquals(
                "Resource 1 Label",
                $this->class->getLabel(),
                "The label returned was not the expected label"
            );
        }


        /**
         * Tests that the class' parent classes is the expected list.
         *
         * @return void
         */
        public function testGetParentClasses() : void {
            $this->assertEquals(
                array(
                    array("parent"=>"Resource_2"),
                    array(
                        "parent"=>"Resource_3", 
                        "restriction"=>array(
                            "property"=>"Property_1", 
                            "relationship"=>"someValues"
                        )
                    )
                ),
                $this->class->getParentClasses(),
                "The list of the class' parent classes was not the expected list"
            );
        }


        /**
         * Tests that the class' comments can be correctly returned.
         *
         * @return void
         */
        public function testGetComments() : void {
            $this->assertEquals(
                array("Resource 1 Comment"),
                $this->class->getComments(),
                "The list of comments was not the expected list"
            );
        }



        /**
         * Tests that the array that is returned by the getJSONArray method matches the expected properties from the ontology.
         *
         * @return void
         */
        public function testGetJSONArray() : void {
            $this->assertEquals(
                array(
                    "label"=>"Resource 1 Label",
                    "about"=>"Resource_1",
                    "parents"=>array(
                        array("parent"=>"Resource_2"),
                        array(
                            "parent"=>"Resource_3",
                            "restriction"=>
                                array("property"=>"Property_1", "relationship"=>"someValues")
                        )
                    ),
                    "comments"=> array("Resource 1 Comment")
                ),
                $this->class->getJSONArray(),
                "The JSON array was not the expected array"
            );
        }


    }

?>
