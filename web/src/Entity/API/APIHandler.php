<?php

    namespace App\Entity\API;

    use App\Entity\Ontology\Ontology;
    use App\Entity\API\Database;

    /**
     * Class to process requests made to the API controller.
     * 
     * The purpose of this class is to remove as much of the logic from the APIController class.
     */
    class APIHandler {

        // === private methods ===

        /**
         * Returns the ontology object from the database.
         *
         * @param String $ontologyID the key for the ontology
         * @return Ontology the ontology object
         * @throws \Exception if there was an error retrieving the ontology from the database
         */
        public static function getOntology(String $ontologyID) : Ontology {
            try {
                if ($ontologyID == "test") {
                    // if the ID is "test" return the Ontology object for the OntologyTest.owl file
                    return new Ontology("tests/Resources/OntologyTest.owl");
                } else {
                    // return the unserialised database object
                    $database = new Database();
                    return $database->getOntology($ontologyID);
                }    
            } catch (\Exception $e) {
                throw $e;
            }
            
        }

    }

?>