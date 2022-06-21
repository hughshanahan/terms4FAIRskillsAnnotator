<?php

    namespace App\Tests\Unit\Ontology;
    use PHPUnit\Framework\TestCase;
    use App\Entity\Ontology\OWLReader;

    /**
     * Tests the OWLReader Class.
     */
    class OWLReaderTest extends TestCase {

        private $xml;

        /**
         * Creates the XML object before each test is run.
         *
         * @return void
         */
        protected function setUp() : void {

            /*
                XML String explaination:
                The XML string has been written to provide all the combinations of namespaces, children and attributes.
                 - There are elements that dont have a namespace identifier (root and element3) 
                   and elements that do (a:element1 and b:element2)
                 - There are elements that have no children (element3), elements with one child (a:element1) 
                   and elements with multiple children (b:element2)
                 - There are elements that have no attributes (a:element1), elements with one attribute (element3) 
                   and elements with multiple attributes (b:element2)
                 - There are elements that have attributes without namespaces (attribute2 in b:element2) 
                   and elements that have namespaced attributes (a:attribute1 in b:element2 and b:attrbiute3 in element3)
            */
            $xmlString = '
                <root xmlns:a="http://www.example.com/a"
                        xmlns:b="https://www.example.com/b">
                    <a:element1>
                        <b:element2 a:attribute1="attribute1 value" attribute2="attribute2 value">
                            <element3 b:attribute2="value" />
                            <element3 b:attribute2="value" />
                            <element3 b:attribute2="value" />
                            <element3 b:attribute2="value" />
                        </b:element2>
                    </a:element1>
                </root>
            ';
            $this->xml = simplexml_load_string($xmlString);
        }


        /**
         * Tests the correct fully qualifed name is returned if the element doesn't have a namespace.
         * This is asserted by chcecking that calling getFullyQualifiedName on the root element returns the 
         * string "root".
         *
         * @return void
         */
        function testGetFullyQualifiedNameWithoutNamespace() : void {
            $this->assertEquals(
                "root",
                OWLReader::getFullyQualifiedName($this->xml),
                "The getFullyQualifiedName method did not return the expected name when the element doesn't have a namespace"
            );
        }

        /**
         * Tests that the correct fully qualified name is returned for an element with a namespace.
         * This is asserted by getting the first element that has the fully qualified name "a:element1" from the xml,
         * and asserting that when the SimpleXMLElement object for that element is passed to the getFullyQualifiedName method
         * the returned value is "a:element1". 
         * The namespace identifier is "a" and the element name is "element1".
         *
         * @return void
         */
        function testGetFullyQualifiedNameWithNamespace() : void {
            $element = $this->xml->xpath("//a:element1")[0]; // element that has a namespace identifier

            // check the method returns the correct fully qualified name
            $this->assertEquals(
                "a:element1",
                OWLReader::getFullyQualifiedName($element),
                "The getFullyQualifiedName method did not return the expected name when the element has a namespace"
            );
        }


        /**
         * Tests that the array of child elements is empty if the getChildren method is called on an element without any children.
         *
         * @return void
         */
        function testGetChildrenNoChildren() : void {
            $element = $this->xml->xpath("//a:element1/b:element2/element3")[0]; // element that does not have any child elements

            $this->assertEmpty(
                OWLReader::getChildren($element),
                "The array of child elements was not empty for an element with no children"
            );
        }

        /**
         * Tests that the correct list of child elements is returned when there is only one direct child element.
         * This is asserted by getting the SimpleXMLElement that corresponds to the only child of the root
         * element and placing it in an array and then calling the getChildren on the root element and asserting
         * if the array containing the single SimpleXMLElement object is returned.
         *
         * @return void
         */
        function testGetChildrenSingle() : void {
            // get the SimpleXMLObject for the only child of the root element
            $onlyChild = $this->xml->xpath("//a:element1")[0];

            $this->assertEquals(
                array($onlyChild), // expecting array containing the child element object
                OWLReader::getChildren($this->xml),
                "The getChildren method did not return the correct list of child elements when there is only one child element"
            );
            
        }


        /**
         * Tests that the correct list of child elements is returned when there are multiple children.
         *
         * @return void
         */
        function testGetChildrenMultiple() : void {
            $element = $this->xml->xpath("//a:element1/b:element2")[0]; // element with multiple children

            $this->assertEquals(
                $this->xml->xpath("//a:element1/b:element2/element3"), // the array should match this xpath query
                OWLReader::getChildren($element),
                "The getChildren method did not return the correct list of child elements when there are multiple children"
            );
        }


        /**
         * Tests that an empty array is returned when getAttributes is called on an element with no attributes.
         *
         * @return void
         */
        function testGetAttributesNoAttributes() : void {
            $element = $this->xml->xpath("//a:element1")[0]; // element with no attributes

            $this->assertEmpty(
                OWLReader::getAttributes($element),
                "The array of attributes was not empty for an element with no attributes"
            );
        }


        /**
         * Tests that the correct dictionary of attributes is returned.
         * This is asserted by getting the first element in the object that has a single attribute and asserting that
         * the correct array containing the key value pair is returned.
         *
         * @return void
         */
        function testGetAttributesSingle() : void {
            // get the first element that has a single attribute
            $element = $this->xml->xpath("//a:element1/b:element2/element3")[0]; 

            $this->assertEquals(
                array("b:attribute2" => "value"),
                OWLReader::getAttributes($element),
                "The getAttributes method did not return the correct list of attributes"
            );
        }


        /**
         * Tests that the correct array of key value pairs is returned for when an element has multiple attributes.
         *
         * @return void
         */
        function testGetAttributesMutliple() : void {
            // get the first element that has a multiple attributes
            $element = $this->xml->xpath("//a:element1/b:element2")[0]; 

            $this->assertEquals(
                array("a:attribute1" => "attribute1 value", "attribute2" => "attribute2 value"),
                OWLReader::getAttributes($element),
                "The getAttributes method did not return the correct list of attributes"
            );
        }




    }

?>