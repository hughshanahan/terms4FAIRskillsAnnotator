# terms4FAIRskillsAnnotator
Tool for annotating materials with terms4FAIRskills ontology.



## Test Resources 
As part of the Unit testing for the application, a set of sample .owl files have been written. 
These are located in /tessts/Resources/ and have been named to match the Unit test that they have been written for. 
The absolute path for them in the Docker container is /var/www/tests/Resources/.
A copy of the full t4fs.owl file has also been included in this directory.

- OntologyTest.owl *([link](tests/Resources/OntologyTest.owl))*

This ontology is used to test the Ontology class and contains a fully written owl:Ontology element and the minimun viable for each of the owl:AnnotationProperty, owl:ObjectProperty, owl:DatatypeProperty and owl:Class elements.


- OWLClassTest.owl *([link](tests/Resources/OWLClassTest.owl))*

This ontology is used to test the OWLClass class and contains a fully written owl:Class element and other minimal owl:Class elements to ensure that the subclass properties are maintained.


- OWLAnnotationPropertyTest.owl *([link](tests/Resources/OWLAnnotationPropertyTest.owl))*

This ontology is used to test the OWLAnnotation class and contains a fully written owl:AnnotationProperty element and other minimal elements to ensure that relationships are maintained.

- OWLReaderTest.owl *([link](tests/Resources/OWLReaderTest.owl))*

This XML file has been written like the ontology files but is written so that all the XML properties that the ontology files could exhibit are testable. More information is provided in a comment at the top of the [file](tests/Resources/OWLReaderTest.owl).