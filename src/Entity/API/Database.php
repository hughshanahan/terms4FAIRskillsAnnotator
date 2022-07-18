<?php

    namespace App\Entity\API;

    use App\Entity\Ontology\Ontology;

    /**
     * Class to wrap the connection to the database.
     */
    class Database {


        /*
            ===== Contents =====
            - Constructor and properties
            - SQL Methods
            - Ontology Methods
            - Resource Methods

        */


        /*
            === Constrcutor and Properties ===
            The class constructor and the class properties.
        */
        

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


        /*
            === SQL methods ===
            A method for each required SQL command.
        */


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
         * Selects data from the database.
         *
         * @param String $table the table to select data from
         * @param string $where the predicate to match against in the where clause
         * @return array an array of associative arrays storing the rows
         */
        private function select(String $table, String $where = "") : array {
            // create the SQL
            $sql = "SELECT * FROM " . $table;
            if (!($where == "")) {
                $sql .= " WHERE " . $where;
            }
            $sql .= ";";
            // query the database
            $result = $this->connection->query($sql);
            // create the array to store the results
            $data = array();
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $rowData = array();
                    foreach ($row as $col => $val) {
                        $rowData[$col] = $val;
                    }
                    array_push($data, $rowData);
                }
            }

            return array("sql"=>$sql, "rows"=>$data);
        }


        /*
            === Ontology Methods ===
            Methods that change the ontology table.
            This includes the method that updates the last time that the ontology was accessed.
        */


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


        /**
         * Gets the Ontology object from the database.
         *
         * @param String $ontologyID the database ID for the ontology
         * @return Ontology the ontology object
         */
        public function getOntology(String $ontologyID) : Ontology {
            $where = "id='" . $ontologyID . "'";
            $data = $this->select("ontology", $where);
            if (count($data["rows"]) == 0) {
                throw new \Exception("The ontology could not be found - " . $data["sql"]);
            }
            return Ontology::unserialise($data["rows"][0]["content"]);
        }


        /*
            === Resource Methods ===
            Methods that change the resources table.
        */


        public function createResource(
            String $ontologyID, 
            String $identifier,
            String $name,
            String $author,
            String $date,
            array $terms,
        ) : int {
            // create the id
            $id = rand();

            // create the values array
            $values = array(
                "id"=>$id,
                "ontologyID"=>$ontologyID,
                "identifier"=>$identifier,
                "name"=>$name,
                "author"=>$author,
                "date"=>$date
            );

            // insert into database
            $this->insert("resource", $values);

            // insert into the terms table
            foreach ($terms as $term) {
                $values = array(
                    "resourceID"=>$id,
                    "termURI"=>$term
                );
                $this->insert("term", $values);
            }

            return $id;
        }

    }

?>