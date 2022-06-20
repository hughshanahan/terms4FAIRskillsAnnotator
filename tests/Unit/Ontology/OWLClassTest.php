<?php

    namespace App\Tests\Unit;
    use PHPUnit\Framework\TestCase;
    use App\Entity\Ontology\OWLClass;

    /**
     * Tests the OWLClass Class.
     */
    class OWLClassTest extends TestCase {







        /**
         * Tests that an expection is thrown if a Ontology\Class object is attempted to be created from an XML element that isn't an owl:class element.
         *
         * @return void
         */
        public function testInvalidElementType() : void {
            // tell the test case that the exception is going to be thrown
            $this->expectException(\Exception::class);
            // attempt to create the OntologyClass object
            $xmlElement = simplexml_load_string("<invalidelement></invalidelement>");
            $object = new OWLClass($xmlElement);
        }


    }

?>
