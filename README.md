# terms4FAIRskillsAnnotator
Tool for annotating materials with terms4FAIRskills ontology.



## Test Resources 
As part of the Unit testing for the application, a set of sample .owl files have been written. 
These are located in /tessts/Resources/ and have been named to match the Unit test that they have been written for. 
The absolute path for them in the Docker container is /var/www/tests/Resources/.
A copy of the full t4fs.owl file has also been included in this directory.

- OntologyTest.owl
This ontology is used to test the Ontology class and contains a full written owl:Ontology element and the minimun viable for each of the owl:AnnotationProperty, owl:ObjectProperty, owl:DatatypeProperty and owl:Class elements.