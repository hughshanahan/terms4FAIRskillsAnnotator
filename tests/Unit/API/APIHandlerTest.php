<?php

    namespace App\Tests\Unit\API;

    use PHPUnit\Framework\TestCase;

    use App\Entity\API\APIHandler;

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
            $this->handler = new APIHandler();
        }

        /**
         * Tests that the object is correctly instantiated.
         *
         * @return void
         */
        public function testInstantiation() : void {
            $this->assertInstanceOf(APIHandler::class, $this->handler);
        }


    }

?>