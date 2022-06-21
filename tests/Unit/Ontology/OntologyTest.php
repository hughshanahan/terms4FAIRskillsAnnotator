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

    }

?>
