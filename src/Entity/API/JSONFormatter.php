<?php

    namespace App\Entity\API;

    /**
     * Class to contain methods to get JSON data from other data structures.
     */
    class JSONFormatter {


        /**
         * Converts an array to a JSON string.
         *
         * @param array $array the array to convert to a JSON String
         * @return String the JSON String
         */
        public static function arrayToString(array $array) : String {
            $jsonObject = json_encode($array);
            return strval($jsonObject);
        }


    }

?>