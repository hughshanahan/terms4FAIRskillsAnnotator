<?php

    namespace App\Entity\API;

    use App\Entity\Ontology\Ontology;

    /**
     * Class to wrap the connection to the database.
     */
    class Database {

        private $connection; // holds the database connection

        /**
         * Connects to the database in the terms4FAIRskills_annotator_db container.
         * @throws Exception if the connection fails
         */
        public function __construct() {
            try {
                $this->connection = new \mysqli(
                    "terms4FAIRskills_annotator_db",    // server
                    $_ENV["MYSQL_USER"],                // user
                    $_ENV["MYSQL_PASSWORD"],            // password
                    $_ENV["MYSQL_DATABASE"]             // database
                );
            } catch(\Exception $e) {
                // if there is an exception when connecting to the database, 
                // throw the exception back to the calling method
                throw $e;
            }
        }


        /**
         * Inserts data into the database.
         *
         * @param String $table tne name of the table to insert the data into
         * @param array $columnValues an associative array of column name and value pairs
         * @return void
         * @throws \Exception if the data could not be inserted
         */
        private function insert(String $table, array $columnValues) {

            // process the columnValues array
            $columns = "";
            $values = "";
            $firstPair = true;
            foreach ($columnValues as $column => $value) {
                if (!($firstPair)) {
                    // if the pair is not the first pair, then add the commas
                    $columns .= ", ";
                    $values .= ", ";
                }
                // add the column and value
                $columns .= $column;
                $escapedValue = $this->connection->real_escape_string($value);
                $values .= "'" . $escapedValue . "'";
                // set the first pair flag to false
                $firstPair = false;
            }

            // create the SQL query
            $sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";

            // complete the insertion
            $success = $this->connection->query($sql);

            // check for an error during the insert and throw exception if so
            if (!($success === TRUE)) {
                throw new \Exception($conn->error);
            }


        }


        /**
         * Inserts an ontology object into the database.
         *
         * @param Ontology $ontology the ontology object
         * @return int the key of the ontology in the database
         */
        public function insertOntology(Ontology $ontology) : int {
            // serialise the ontology object
            $ontologySerialised = Ontology::serialise($ontology);

            // create the id
            $id = rand();

            // create the values array
            // the id is a random number, 
            // the content is the serialised content of the ontology object
            // the accessed value is the current time stamp
            $values = array(
                "id"=>$id,
                "content"=>$ontologySerialised,
                "accessed"=>time()
            );

            $this->insert("ontology", $values);

            return $id;

        }

    }

?>