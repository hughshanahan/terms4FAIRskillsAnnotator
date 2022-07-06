<?php

    namespace App\Tests\Unit\API;

    use PHPUnit\Framework\TestCase;

    use App\Entity\API\APIHandler;
    use App\Entity\API\JSONFormatter;

    /**
     * Tests the APIHandler Class.
     */
    class APIHandlerTest extends TestCase {

        private $handler;

        /**
         * Set Up method to create an instance of the APIHandler class before each test.
         *
         * @return void
         */
        protected function setUp() : void {
            $this->handler = new APIHandler("test");
        }

        /**
         * Tests that the object is correctly instantiated.
         *
         * @return void
         */
        public function testInstantiation() : void {
            $this->assertInstanceOf(APIHandler::class, $this->handler);
        }


        /**
         * Tests that the JSON String returned from the searchTerms method is in the correct format.
         * There is no need to check that the contents of the search are correct as this is tested by 
         * testQueryClasses in OntologyTest.
         *
         * @return void
         */
        public function testSearchTerms() : void {
            // get the JSON String as an associative array
            $string = $this->handler->searchTerms("metadata");
            $array = JSONFormatter::stringToArray($string);

            // check for the expected keys
            $hasSearchQuery = array_key_exists("search", $array);
            $hasResults = array_key_exists("results", $array);

            // assert that all the checks were true
            $this->assertTrue(
                ($hasSearchQuery && $hasResults),
                "The returned JSON doesn't contain the required keys"
            );
        }



        /**
         * Test that the JSON data returned is for the term that was requested.
         *
         * @return void
         */
        public function testGetTerm() : void {
            // get the json as an associative array
            $string = $this->handler->getTerm("Resource_1");
            $array = JSONFormatter::stringToArray($string);

            // test that the URI of the term returned is the requested URI 
            $this->assertEquals(
                "Resource_1",
                $array["about"],
                "The URI of the returned term is not the requested URI"
            );
        }


    }

?>