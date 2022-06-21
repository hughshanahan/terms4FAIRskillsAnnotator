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
         * Set Up method to create an instance of the Ontology class before each test.
         *
         * @return void
         */
        protected function setUp() : void {

            $simpleOWLFile = '<?xml version="1.0"?>
                <rdf:RDF xmlns="https://github.com/terms4fairskills/FAIRterminology#"
                    xml:base="https://github.com/terms4fairskills/FAIRterminology"
                    xmlns:dc="http://purl.org/dc/elements/1.1/"
                    xmlns:obo="http://purl.obolibrary.org/obo/"
                    xmlns:owl="http://www.w3.org/2002/07/owl#"
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:xml="http://www.w3.org/XML/1998/namespace"
                    xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
                    xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:terms="http://purl.org/dc/terms/"
                    xmlns:oboInOwl="http://www.geneontology.org/formats/oboInOwl#"
                    xmlns:FAIRterminology="https://github.com/terms4fairskills/FAIRterminology/">

                    <owl:Class rdf:about="https://github.com/terms4fairskills/FAIRterminology/T4FS_0000552">
                        <rdfs:subClassOf rdf:resource="https://github.com/terms4fairskills/FAIRterminology/T4FS_0000372"/>
                        <obo:IAO_0000115>Any information obtained by a person on the understanding that they will not disclose it to others, or obtained in circumstances where it is expected that they will not disclose it.</obo:IAO_0000115>
                        <obo:IAO_0000117>Peter McQuilton</obo:IAO_0000117>
                        <rdfs:comment>CASRAI</rdfs:comment>
                        <rdfs:label>Confidential information</rdfs:label>
                    </owl:Class>

                </rdf:RDF>
            ';
            $root = simplexml_load_string($simpleOWLFile);
            $classElement = $root->xpath("//owl:Class")[0];
            $this->class = new OWLClass($classElement);
        }




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


        /**
         * Tests that the object instantiated in the set up method is an OWLClass object.
         *
         * @return void
         */
        public function testCorrectlyInstantiated() : void {
            $this->assertInstanceOf(OWLClass::class, $this->class);
        }


        /**
         * Tests that the object has correctly read the URI from the owl:Class element's rdf:about attribute.
         *
         * @return void
         */
        public function testAboutProperty() : void {
            $this->assertEquals(
                "https://github.com/terms4fairskills/FAIRterminology/T4FS_0000552",
                $this->class->getAbout(),
                "The OWLClass object has not correctly read what the class is about"
            );
        }


        public function testParentClasses() : void {
            $this->assertEquals(
                array("https://github.com/terms4fairskills/FAIRterminology/T4FS_0000372"),
                $this->class->getParentClasses(),
                "The list of the class' parent classes was not the expected list"
            );
        }


    }

?>
