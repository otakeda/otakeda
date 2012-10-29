<?php
/**
 * Logging wrapper for the Yahoo objects.
 *
 * @brief Logging wrapper for the Yahoo objects.
 */
class YahooLogger {
    /**
     * Log a message at the debug level.
     *
     * @param $message The message to log.
     */
    function debug($message, $object = NULL) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG;
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($GLOBAL_YAHOO_LOGGER_DEBUG) {
            if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "CONSOLE") {
                print("DEBUG - $message\n");
                if(!is_null($object)) {
                    print("DEBUG OBJECT - " . print_r($object, true) . "\n");
                }
            }
            else if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "LOG") {
                error_log("DEBUG - $message");
                if(!is_null($object)) {
                    error_log("DEBUG OBJECT - " . print_r($object, true));
                }
            }
        }
    }

    /**
     * Log a message at the info level.
     *
     * @param $message The message to log.
     */
    function info($message, $object = NULL) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "CONSOLE") {
            print("INFO - $message\n");
            if(!is_null($object)) {
                print("INFO OBJECT - " . print_r($object, true) . "\n");
            }
        }
        else if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "LOG") {
            error_log("INFO - $message");
            if(!is_null($object)) {
                error_log("INFO OBJECT - " . print_r($object, true));
            }
        }
    }

    /**
     * Log a message at the error level.
     *
     * @param $message The message to log.
     */
    function error($message, $object = NULL) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "CONSOLE") {
            print("ERROR - $message\n");
            if(!is_null($object)) {
                print("ERROR OBJECT - " . print_r($object, true) . "\n");
            }
        }
        else if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "LOG") {
            error_log("ERROR - $message");
            if(!is_null($object)) {
                error_log("ERROR OBJECT - " . print_r($object, true));
            }
        }
    }

    /**
     * Enables/disables session debugging.
     *
     * @param $debug Boolean to enable/disable debugging.
     */
    function setDebug($debug) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG;
        $GLOBAL_YAHOO_LOGGER_DEBUG = (bool) $debug;
    }

    /**
     * Allows callers to configure where debugging output is sent.
     *
     * @param $destination "LOG" to use error_log, "CONSOLE" to use printf, 
     *                     "NULL" to disable all logging output.
     * @return boolean True on success, false on failure.
     */
    function setDebugDestination($destination) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($destination == "LOG" || $destination == "CONSOLE" || 
                $destination == "NULL") {
            $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION = $destination;
            return true;
        }
        else {
            return false;
        }
    }
}

