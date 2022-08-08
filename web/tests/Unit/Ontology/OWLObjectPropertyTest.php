<?php

    namespace App\Tests\Unit\Ontology;
    use PHPUnit\Framework\TestCase;
    use App\Entity\Ontology\OWLObjectProperty;

    /**
     * Tests the OWLObjectProperty Class.
     */
    class OWLObjectPropertyTest extends TestCase {

        private $property;

        /**
         * Set Up method to create an instance of the OWLObjectProperty class before each test.
         *
         * @return void
         */
        protected function setUp() : void {

            $root = simplexml_load_string(file_get_contents("tests/Resources/OWLObjectPropertyTest.owl"));
            $propertyElement = $root->xpath("//owl:ObjectProperty")[0]; // get the first owl:ObjectProperty element from the file
            $this->property = new OWLObjectProperty($propertyElement);
        }


        /**
         * Tests that an expection is thrown if an OWLObjectProperty object is attempted to be created from an XML element 
         * that isn't an owl:ObjectProperty element.
         *
         * @return void
         */
        public function testInvalidElementType() : void {
            // tell the test case that the exception is going to be thrown
            $this->expectException(\Exception::class);
            // attempt to create the OWLObjectProperty object
            $xmlElement = simplexml_load_string("<invalidelement></invalidelement>");
            $object = new OWLObjectProperty($xmlElement);
        }

        /**
         * Tests that the object instantiated in the set up method is an OWLObjectProperty object.
         *
         * @return void
         */
        public function testCorrectlyInstantiated() : void {
            $this->assertInstanceOf(
                OWLObjectProperty::class, 
                $this->property,
                "The property was not an instance of OWLObjectProperty"
            );
        }


        /**
         * Tests that the object has correctly read the URI from the 
         * owl:ObjectProperty element's rdf:about attribute.
         *
         * @return void
         */
        public function testAboutProperty() : void {
            $this->assertEquals(
                "Property_1",
                $this->property->getAbout(),
                "The OWLAnnotationProperty object has not correctly read what the property is about"
            );
        }

    }

?>