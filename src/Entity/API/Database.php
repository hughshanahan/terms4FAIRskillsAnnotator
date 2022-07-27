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
         * @throws \Exception if the connection fails
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
            $sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ");";

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


        /**
         * Runs a delete query on the database.
         *
         * @param String $table the table to delete from
         * @param String $where the predicate to match for deletion
         * @return void
         * @throws \Exception if an error occured during the deletion
         */
        private function delete(String $table, String $where) : void {
            $sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
            $success = $this->connection->query($sql);

            // check for an error during the delete and throw exception if so
            if (!($success === TRUE)) {
                throw new \Exception($conn->error);
            }

        }


        /**
         * Updates a record in the database.
         *
         * @param String $table the table to update the records in
         * @param array $columnValues the column/value pairs to update
         * @param String $where the where predicate
         * @return void
         */
        public function update(String $table, array $columnValues, String $where) : void {

            // process the columnValues array
            $values = "";
            $firstPair = true;
            foreach ($columnValues as $column => $value) {
                if (!($firstPair)) {
                    // if the pair is not the first pair, then add the commas
                    $values .= ", ";
                }
                // add the column/value pair
                $escapedValue = $this->connection->real_escape_string($value);
                $values .= $column . "='" . $escapedValue . "'";
                // set the first pair flag to false
                $firstPair = false;
            }

            $sql = "UPDATE " . $table . " SET " . $values .  " WHERE " . $where . ";";
            $success = $this->connection->query($sql);

            // check for an error during the delete and throw exception if so
            if (!($success === TRUE)) {
                throw new \Exception($conn->error);
            }
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
         * @throws Exception if the ontology could not be inserted into the database
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

            try {
                $this->insert("ontology", $values);
                return $id;
            } catch (\Exception $e) {
                // catch the exception and throw it on
                throw $e;
            }
            
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


        /**
         * Returns the resource data.
         *
         * @param String $resourceID the resource to get
         * @return array the resource data
         */
        public function getResource(String $resourceID) : array {
            $data = array();
            // get the resource data from the resource table
            $where = "id='" . $resourceID . "'";
            $resourceData = $this->select("resource", $where);
            if (count($resourceData["rows"]) == 0) {
                throw new \Exception("The resource could not be found - " . $resourceData["sql"]);
            }
            $data = $resourceData["rows"][0];

            // get the terms from the term table and add them to the data
            $terms = array();
            $where = "resourceID='" . $resourceID . "'";
            $termsData = $this->select("term", $where);
            foreach ($termsData["rows"] as $term) {
                array_push($terms, $term["termURI"]);
            }   
            $data["terms"] = $terms;

            // return the data
            return $data;
        } 


        /**
         * Create an entry in the database for a new resource.
         *
         * @param String $ontologyID the database id for the ontology
         * @param String $identifier the DOI identifier for the resource
         * @param String $name the name of the resource
         * @param String $author the author of the resoruce
         * @param String $date the date of the resource
         * @param array $terms the array of term URIs that have been selected
         * @return integer the database identifer for the resource
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
            $this->insertTerms($id, $terms);

            return $id;
        }


        /**
         * Saves the changes made to the resource in the database.
         *
         * @param String $resourceID the id of the resource to change
         * @param String $identifier the DOI identifier of the resource
         * @param String $name the name of the resource
         * @param String $author the author of the resource
         * @param String $date the date of the resource
         * @param array $terms an array of term URIs
         * @return void
         */
        public function saveResource(
            String $resourceID,
            String $identifier,
            String $name,
            String $author,
            String $date,
            array $terms,
        ) {
            // clear all the terms for the resource
            $this->delete("term", "resourceID='" . $resourceID . "'");

            // create the values array
            $values = array(
                "identifier"=>$identifier,
                "name"=>$name,
                "author"=>$author,
                "date"=>$date
            );

            $this->update("resource", $values, "id='" . $resourceID . "'");
            $this->insertTerms($resourceID, $terms);

        }


        /**
         * Returns an array of the resources that are for the given ontology.
         *
         * @param String $ontologyID the ontology database id
         * @return array the resources for the ontology
         */
        public function getOntologyResources(String $ontologyID) : array {
            $resources = $this->select("resource", "ontologyID='" . $ontologyID . "'")["rows"];
            $resourcesWithTerms = array();
            foreach ($resources as $resource) {
                $resourceWithTerms = array_merge(array(), $resource); //make a copy of the array
                $resourceID = $resourceWithTerms["id"];
                $resourceWithTerms["terms"] = $this->getTerms($resourceID);
                array_push($resourcesWithTerms, $resourceWithTerms);
            }
            return $resourcesWithTerms;
        }


        /**
         * Inserts an array of terms into the term table of the database.
         *
         * @param String $resourceID the id of the resource that the terms are related to
         * @param array $terms the array of term URIs
         * @return void
         */
        private function insertTerms(String $resourceID, array $terms) : void {
            // insert into the terms table
            foreach ($terms as $term) {
                $values = array(
                    "resourceID"=>$resourceID,
                    "termURI"=>$term
                );
                $this->insert("term", $values);
            }
        }


        /**
         * Returns an array of terms for a given resource.
         *
         * @param String $resourceID the resource
         * @return array the resource terms
         */
        private function getTerms(String $resourceID) : array {
            $terms = $this->select("term", "resourceID='" . $resourceID . "'")["rows"];
            $termURIs = array();
            foreach ($terms as $term) {
                array_push($termURIs, $term["termURI"]);
            }
            return $termURIs;
        }

        /**
         * Deletes a resource from the database.
         *
         * @param String $resourceID the resource to delete
         * @return void
         */
        public function deleteResource(String $resourceID) : void {
            // delete the terms, then delete the resource
            $this->delete("term", "resourceID='" . $resourceID . "'");
            $this->delete("resource", "id='" . $resourceID . "'");
        }

    }



?>