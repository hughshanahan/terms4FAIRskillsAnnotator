<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;

    /**
     * Class to handle requests for the documentation.
     */
    class DocumentationController {

        /**
         * Returnns the MIME type for the file.
         *
         * @param String $file the file path
         * @return String the MIME type
         */
        private function getMime(String $file) : String {
            $fileData = explode('.',$file);
            $extension = $fileData[count($fileData) - 1];
            $extension = strtolower($extension);
            if (array_key_exists($extension, self::$mime_types)) {
                return self::$mime_types[$extension];
            }
            return "text/plain";
        }


        /**
         * Returns the response object containing the generated documentation.
         *
         * @param String $from the relative URL in the docs directory
         * @param String $path the path from the URL
         * @return Response the response oobject containing the generated documentation
         */
        private function getResponse(String $from, String $path) : Response {
            if ($path == "") {
                $path = "index.html";
            }

            $file = $_SERVER["DOCUMENT_ROOT"] . "/../docs" . $from . $path;
            $mime = $this->getMime($file);

            return new Response(
                file_get_contents($file),
                Response::HTTP_OK,
                ['content-type' => $mime]
            );
        }


        /**
         * Returns the documentation for the backend src.
         *
         * @param String $path the path to get the backend src folder
         * @return Response the response oobject containing the generated documentation
         */
        public function backendsrc(String $path) : Response {
            return $this->getResponse("/backend/src/", $path);
        }

        /**
         * Returns the documentation for the backend tests.
         *
         * @param String $path the path to get the backend tests folder
         * @return Response the response oobject containing the generated documentation
         */
        public function backendtests(String $path) : Response {
            return $this->getResponse("/backend/tests/", $path);
        }

        /**
         * Returns the documentation for the frontend.
         *
         * @param String $path the path to get the frontend folder
         * @return Response the response oobject containing the generated documentation
         */
        public function frontend(String $path) : Response {
            return $this->getResponse("/frontend/", $path);
        }



        // array to store the file extension to mime type pairs
        private static $mime_types = array(

            // common text based
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

    }

?>