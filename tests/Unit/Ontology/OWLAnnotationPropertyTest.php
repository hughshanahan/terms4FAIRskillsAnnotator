<?php

    namespace App\Tests\Unit\Ontology;
    use PHPUnit\Framework\TestCase;
    use App\Entity\Ontology\OWLAnnotationProperty;

    /**
     * Tests the OWLClass Class.
     */
    class OWLAnnotationPropertyTest extends TestCase {

        private $property;

        /**
         * Set Up method to create an instance of the OWLAnnotationProperty class before each test.
         *
         * @return void
         */
        protected function setUp() : void {

            $root = simplexml_load_string(file_get_contents("tests/Resources/OWLAnnotationPropertyTest.owl"));
            $propertyElement = $root->xpath("//owl:AnnotationProperty")[0]; // get the first owl:AnnotationProperty element from the file
            $this->property = new OWLAnnotationProperty($propertyElement);
        }


        /**
         * Tests that an expection is thrown if an OWLAnnotation object is attempted to be created from an XML element 
         * that isn't an owl:AnnotationProperty element.
         *
         * @return void
         */
        public function testInvalidElementType() : void {
            // tell the test case that the exception is going to be thrown
            $this->expectException(\Exception::class);
            // attempt to create the OWLAnnotationProperty object
            $xmlElement = simplexml_load_string("<invalidelement></invalidelement>");
            $object = new OWLAnnotationProperty($xmlElement);
        }

    }

?>