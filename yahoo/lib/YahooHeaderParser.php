<?php
/**
 * HTTP Header parsing class
 */
class YahooHeaderParser {
    var $headers = array();

    function YahooHeaderParser() {
    }

    function read($ch, $header) {
        $pos = strpos($header, ":");
        if($pos !== FALSE) {
            $name = substr($header, 0, $pos);
            $value = trim(substr($header, $pos + 1));
            $this->headers[$name] = $value;
        }
        return strlen($header);
    }

    function get($name) {
        if(array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
        else {
            return NULL;
        }
    }
}


