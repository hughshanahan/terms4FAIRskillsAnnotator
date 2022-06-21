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
            $this->ontology = new Ontology("tests/Resources/t4fs.owl");
        }

        /**
         * Tests that the object is correctly instantiated from the filepath provided in the constructor.
         *
         * @return void
         */
        public function testCorrectlyInstantiated() : void {
            $this->assertInstanceOf(Ontology::class, $this->ontology);
        }

    }

?>
