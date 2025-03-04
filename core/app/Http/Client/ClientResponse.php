<?php namespace App\Http\Client;

class ClientResponse {


    private $code;
    private $raw_body;
    private $body;
    private $headers;

    /**
     * @param int $code response code of the cURL request
     * @param string $raw_body the raw body of the cURL response
     * @param string $headers raw header string from cURL response
     * @param array $json_args arguments to pass to json_decode function
     */

    public function __construct($code, $raw_body, $headers = '', $json_args = []) {
        $this->code     = $code;
        $this->raw_body = $raw_body;
    }

    public function getReturnCode( ) {
        return $this->code;
    }

    public function getRawBody( ) {
        return $this->raw_body;
    }

    public function getBody( ) {
        return $this->raw_body;        
        //return $this->body;
    }
    /**
     * if PECL_HTTP is not available use a fall back function
     *
     * @param string $raw_headers raw headers
     * @return array
     */
    private function parseHeaders($raw_headers)
    {

        $key = '';
        $headers = array();

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                        $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                        $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                        $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
            }
        }
        return $headers;
    }
    public function __set( $k, $v ) {
        $this->{ $k } = $v;
    }

    public function __get( $k ) {
        return \property_exists(__CLASS__, $k) ? $this->{ $k } : null;
    }

    

}