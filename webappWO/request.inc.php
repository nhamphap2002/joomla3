<?php

    /**
     * Base class to make HTTP requests using cURL
     *
     * Brief example of use:
     *
     * <code>
     * // Get the instance
     * //
     * $objRequest = new CURL_Request();
     * $objRequest->SetMethod( "GET" );
     * $objRequest->SetURL( "http://www.example.com" );
     *
     * // Set request parameters
     * //
     * $objRequest->AddArgument( "keyName1", "value1" );
     * $objRequest->AddArgument( "keyName2", "value2" );
     * $objRequest->AddArgument( "keyName3", "value3" );
     *
     * // Send request
     * //
     * $varOutput = $objRequest->SendRequest();
     *
     * var_dump( $varOutput );
     * </code>
     *
     * @uses PEAR Service_JSON package (if available)
     */
    class CURL_Request
    {
        protected $strMethod    = "POST";
        protected $aArguments   = array();
        protected $aCurlOptions = array();
        protected $aCurlHeaders = array();
        protected $aMethods     = array( "GET", "POST" );
        protected $strUserAgent = "Mozilla/4.0 (compatible;)";
        protected $CookiesFile  = "/tmp/tmp_curl_cookies";
        protected $strURL       = "";
        protected $strError     = "";
        protected $aCurlInfo    = "";

        /**
         * Constructor method
         * Sets request URL,
         * Loads predefined CURL options
         *
         * @param mixed $strURL the url to make the request.
         *
         */
        function __construct( $strURL = "" )
        {
            // Check cURL exists
            // 
            if ( !function_exists( "curl_init" ))
            {
                die("Error: PHP's cURL support was not found!");
            }
            // Set request URLs
            //
            $this->SetURL( $strURL );

            // Load cURL options
            //
            $this->LoadCurlOptions();
        }

        /**
         * Sets request Method, POST, GET
         *
         * @param string strMethod should contain any of (POST, GET)
         * @return true if the method exists in predefined method list, false otherwise.
         */
        public function SetMethod( $strMethod )
        {
            $bOutput = false;
            if ( in_array( strtoupper( $strMethod ), $this->aMethods ) )
            {
                $this->strMethod = strtoupper( $strMethod );
                $bOutput = true;
            }

            return $bOutput;
        }

        /**
         * Sets request URL
         *
         */
        public function SetURL( $strURL = "" )
        {
            $this->strURL = $strURL;
            return true;
        }

        /**
         * Gets request URL
         *
         * @return string the full request URL
         */
        public function GetURL()
        {
            return $this->strURL;
        }

        /**
         * Gets option for CURL requests
         *
         */
        protected function GetCurlOptions()
        {
            return $this->aCurlOptions;
        }

        /**
         * Sets option for CURL requests
         *
         * @param  integer  $optName   e.g: CURLOPT_FOLLOWLOCATION
         * @param  integer  $optValue  e.g: 1 / true
         *
         */
        public function SetCurlOption( $optName, $optValue )
        {
            $this->aCurlOptions[ $optName ] = $optValue;
        }

        /**
         * Loads predefined cURL options for requests
         * Note: These options could be overriden using $this->SetCurlOption() method
         */
        protected function LoadCurlOptions()
        {
            $aOptions = array( CURLOPT_HEADER         => false,
                               CURLOPT_RETURNTRANSFER => true,
                               CURLOPT_USERAGENT      => "Mozilla/4.0 (compatible;)",
                               CURLOPT_COOKIEJAR      => "/tmp/tmp_curl_cookies",
                               CURLOPT_COOKIEFILE     => "/tmp/tmp_curl_cookies",
                               CURLOPT_URL            => $this->GetURL() );
            $this->aCurlOptions = $aOptions;
        }

        /**
         * Gets header list for CURL requests
         *
         */
        protected function GetCurlHeaders()
        {
            return $this->aCurlHeaders;
        }

        /**
         * Sets header for CURL requests
         *
         * @param  mixed  $headerValue  e.g: "<Header name>: <Header value>"
         *
         */
        public function SetCurlHeader( $headerValue )
        {
            $this->aCurlHeaders[] = $headerValue;
        }

        /**
         * Sets proxy settings for CURL requests
         *
         * @param  mixed  $proxyURL
         * @param  mixed  $proxyPort
         *
         */
        public function SetProxy( $proxyURL, $proxyPort )
        {
            $this->SetCurlOption(CURLOPT_HTTPPROXYTUNNEL, true);
            $this->SetCurlOption(CURLOPT_PROXY,      $proxyURL);
            $this->SetCurlOption(CURLOPT_PROXYPORT, $proxyPort);
        }

        /**
         * Adds the full path of the file in argument list which should be uploaded
         * Sets the request method to POST
         * it is equal to:
         * <form action="<request type>" method="post" enctype="multipart/form-data">
         * <input name="frmFile" type="file">
         * </form>
         *
         * @access public
         *
         * @param string $frmName Name of variable where you can get the content from $_FILES["file_var"]
         * @param string $strFileFullPath full path of the file to be uploaded
         *
         */
        public function AddUploadFile( $frmName, $strFileFullPath )
        {
            $bOutput = false;
            if ( is_readable( $strFileFullPath ) )
            {
                $this->SetMethod( "POST" );
                $this->aArguments[ $frmName ] = "@$strFileFullPath";
                $bOutput = true;
            }
            return $bOutput;
        }

        /**
         * Adds arguments in key=value mode which should be sent along with request to server
         *
         * @access public
         * @param string $strName name of the argument
         * @param mixed $varValue value of the argument
         *
         * @return true on success false otherwise
         */
        public function AddArgument( $strName, $varValue )
        {
            $bOutput = false;
            if ( "" != $strName )
            {
                $this->aArguments[ $strName ] = $varValue;
                $bOutput = true;
            }
            return $bOutput;
        }

        /**
         * Sets list of arguments which should be sent along with request to the server
         *
         * @access public
         * @param array $aArguments list of arguments in array(name1 => value1,name2 => value2)
         *
         * @return boolean true on success false otherwise
         */
        public function SetArguments( $aArguments )
        {
            $bOutput = false;
            if ( count( $aArguments ) > 0 )
            {
                $this->aArguments = $aArguments;
                $bOutput = true;
            }
            return $bOutput;
        }

        /**
         * Returns list of arguments which should be sent along with request to the Server
         *
         * @access public
         *
         * @return array list of all arguments
         */
        public function GetArguments()
        {
            return $this->aArguments;
        }

        /**
         * Resets list of arguments,
         * it can be used once you want to send a second request with different arguments within the same session
         *
         * @access public
         *
         * @return boolean true
         */
        public function ClearArguments( )
        {
            $this->aArguments = array();
            return true;
        }

        /**
         * Returns cURL error message occured during a request
         *
         * @access public
         *
         */
        public function GetError()
        {
            return $this->strError;
        }

        /**
         * Returns cURL info
         *
         * @access public
         *
         */
        public function GetCurlInfo()
        {
            return $this->aCurlInfo;
        }

        /**
         * Sends a GET/POST request with list of arguments 
         *
         * @access public
         *
         * @return string output of server response on success false otherwise
         */
        public function sendRequest()
        {
            $varOutput = false;

            switch ( strtoupper( $this->strMethod ))
            {
                case "POST":
                {
                    $varOutput = $this->sendPOST();
                }break;

                case "GET":
                {
                    $varOutput = $this->sendGET();
                }break;
            }

            return $varOutput;
        }

        /**
         * Sends a POST request
         *
         * @access protected
         *
         * @return string output of server response on success false otherwise
         */
        protected function sendPOST()
        {
            $varOutput = false;

            $hCurl    = curl_init();
            $aPDATA   = $this->GetArguments();
            $aHeaders = $this->GetCurlHeaders();

            // Set headers
            //
            if ( count( $aHeaders ) > 0 )
            {
                $this->SetCurlOption(CURLOPT_HTTPHEADER,$aHeaders);
            }

            // Set options
            //
            $this->SetCurlOption( CURLOPT_POST,       true   );
            $this->SetCurlOption( CURLOPT_POSTFIELDS, $aPDATA);
            curl_setopt_array($hCurl, $this->GetCurlOptions());

            // Execute request
            //
            $varResponse     = curl_exec($hCurl);
            $this->aCurlInfo = curl_getinfo($hCurl);

            if($varResponse === false)
            {
                $this->strError   = "Curl error: " . curl_error($hCurl);
            }

            if(!curl_errno($hCurl))
            {
                $varOutput = $varResponse;
            }
            curl_close($hCurl);

            return $varOutput;
        }

        /**
         * Sends a GET request with list of arguments
         *
         * @access protected
         *
         * @return string output of server response on success false otherwise
         */
        protected function sendGET()
        {
            $varOutput = false;
            $aHeaders  = $this->GetCurlHeaders();
            $strParams = http_build_query($this->GetArguments());
            $strURL    = $this->GetURL() . "?" . $strParams;
            $hCurl     = curl_init();
            
            if ( count( $aHeaders ) > 0 )
            {
                $this->SetCurlOption(CURLOPT_HTTPHEADER,$aHeaders);
            }

            curl_setopt_array($hCurl, $this->GetCurlOptions());
            curl_setopt($hCurl, CURLOPT_URL, $strURL);

            $varResponse     = curl_exec($hCurl);
            $this->aCurlInfo = curl_getinfo($hCurl);
            if( $varResponse === false )
            {
                $this->strError = "Curl error: " . curl_error($hCurl);
            }

            if(!curl_errno( $hCurl ))
            {
                $varOutput = $varResponse;
            }
            curl_close($hCurl);

            return $varOutput;
        }

        /**
         * Encodes a JSON respose
         *
         * @uses PHP PEAR Services_JSON class in case json_encode function does not exist
         * @access public
         * @param  mixed $varContent json encoded content
         * @return string json encoded output
         */
        public function jsonEncode( $varContent )
        {
            if ( true == function_exists('json_decode') )
            {
                $varContent = json_encode( $varContent );
            }
            else if ( is_readable( "JSON.php" ) )
            {
                require_once "JSON.php";
                $objJSON   = new Services_JSON();
                $varContent = $objJSON->encode( $varContent );
            }

            return $varContent;
        }

        /**
         * Decodes the a JSON respose
         *
         * @uses PHP PEAR Services_JSON class in case json_decode function does not exist
         * @access public
         * @param  string $varContent json encoded content
         * @return string decoded output
         */
        public function jsonDecode( $varContent, $assoc = true )
        {
            if ( true == function_exists('json_decode') )
            {
                $varContent = json_decode( $varContent, $assoc );
            }
            else if (is_readable("JSON.php"))
            {
                require_once "JSON.php";
                $looseType  = ( true == $assoc ? SERVICES_JSON_LOOSE_TYPE : "" );
                $objJSON    = new Services_JSON( $looseType );
                $varContent = $objJSON->decode( $varContent );
            }

            return $varContent;
        }
    }

?>