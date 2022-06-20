<?php

    namespace App\Tests\Unit;
    use PHPUnit\Framework\TestCase;
    use App\Entity\Ontology\Ontology;

    /**
     * Tests the Ontology Class.
     */
    class OntologyTest extends TestCase {

        private $ontology;

        /**
         * Set Up method to create an instance of the Ontology class before each test.
         *
         * @return void
         */
        protected function setUp() : void {
            $this->ontology = new Ontology();
            
        }

        /**
         * Tests that the ontology that is being stored as the raw value is the ontology that is required.
         *
         * @return void
         */
        public function testRawString() : void {
            $this->assertEquals(
                $this->ontology->getRaw(),
                file_get_contents("https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl"),
                "The raw contents of the ontology was not the expected contents"
            );
        }

        /**
         * Tests that the XML object can be returned correctly.
         *
         * @return void
         */
        public function testXML() : void {
            $expectedXML = simplexml_load_string(file_get_contents("https://raw.githubusercontent.com/terms4fairskills/FAIRterminology/master/development/t4fs.owl"));
            $this->assertEquals(
                $this->ontology->getXML(),
                $expectedXML,
                "The XML object for the ontology was not the exected object"
            );
        }


    }

?>
