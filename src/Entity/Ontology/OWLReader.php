<?php

    namespace App\Entity\Ontology;

    /**
     * Class to process data from the ontology XML.
     * These methods are used by the Ontology class and the classes that hold the ontology data.
     */
    class OWLReader {

        /**
         * Get the fully qualifed name for the given XML element.
         * This is in the format namespaceID:name.
         *
         * @param \SimpleXMLElement $element the XML element
         * @return String the given element's fully qualified name
         */
        public static function getFullyQualifiedName(\SimpleXMLElement $element) : String {
            // get the namespace identifier via DOMNode object
            $node = dom_import_simplexml($element);
            $nsID = $node->prefix;
            return self::formatNSID($nsID) . $element->getName();
        }


        /**
         * Returns an array of all the child elements for the given element.
         *
         * @param \SimpleXMLElement $element the ekement to get the children for
         * @return array an array of SimpleXMLElement objects that are the child elements for the given element
         */
        public static function getChildren(\SimpleXMLElement $element) : array {
            $children = array();

            // get the children not in a namespace
            foreach ($element->children() as $child) {
                array_push($children, $child);
            }

            // get the children in each of the explict namespaces
            foreach ($element->getDocNamespaces() as $ns => $url) {
                foreach ($element->children($url) as $child) {
                    array_push($children, $child);
                }
            }
            
            return $children;
        }


        /**
         * Returns an dictionary containing all the attributes of the element.
         *
         * @param \SimpleXMLElement $element the element to get the attributes for
         * @return array the array of attributes in key-value pairs
         */
        public static function getAttributes(\SimpleXMLElement $element) : array {
            $attributes = array();

            // get each of the attributes that are not in a namespace
            foreach ($element->attributes() as $key => $value) {
                $attributes[$key] = strval($value);
            }

            // get each of the elements that are in an explicit namespace
            foreach ($element->getDocNamespaces() as $ns => $url) {
                foreach ($element->attributes($url) as $key => $value) {
                    $attributes[self::formatNSID($ns) . $key] = strval($value);
                }
            }

            return $attributes;
        }


        /**
         * Returns the given namespace identifier formatted so that it can be placed infront of the element or attribute name.
         * If there is a namespace identifier a colon is placed after the identifier to seperate it and the element/attribute name.
         * If there is not a namespace identifier, an empty string is returned so the element name doesn't begin with a colon.
         *
         * @param String $nsID the namespace identifier to format
         * @return String a string containing the formatted namespace identifier
         */
        private static function formatNSID(String $nsID) : String {
            if ($nsID == "") {
                return "";
            } 
            return $nsID . ":";
        }

    }

?>