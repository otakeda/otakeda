<?php
include_once( 'lib/YahooSessionStore.php' );
/**
 * @brief Cookie-based implementation of the session store.
 */
class CookieSessionStore implements YahooSessionStore {
    /**
     * Indicates if the session store has a request token.
     *
     * @return True if a request token is present, false otherwise.
     */
    public function hasRequestToken() {
        return array_key_exists("yjsdk_rt", $_COOKIE) && 
                (strlen($_COOKIE["yjsdk_rt"]) > 0);
    }

    /**
     * Indicates if the session store has an access token.
     *
     * @return True if an access token is present, false otherwise.
     */
    public function hasAccessToken() {
        return array_key_exists("yjsdk_at", $_COOKIE) && 
                (strlen($_COOKIE["yjsdk_at"]) > 0);
    }

    /**
     * Stores the given request token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of 
     *               the OAuth request token.
     * @return True on success, false otherwise.
     */
    public function storeRequestToken($token) {
        if(!headers_sent()) {
            return setcookie("yjsdk_rt", base64_encode(json_encode($token)), 
                    time() + 600);
        }
        else {
            return false;
        }
    }

    /**
     * Fetches and returns the request token from the session store.
     *
     * @return The request token.
     */
    public function fetchRequestToken() {
        return json_decode(base64_decode($_COOKIE["yjsdk_rt"]));
    }

    /**
     * Clears the request token from the session store.
     *
     * @return True on success, false otherwise.
     */
    public function clearRequestToken() {
        if(!headers_sent()) {
            return setcookie("yjsdk_rt", "", time() - 600);
        }
        else {
            return false;
        }
    }

    /**
     * Stores the given access token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of 
     *               the OAuth access token.
     * @return True on success, false otherwise.
     */
    public function storeAccessToken($token) {
        if(!headers_sent()) {
            return setcookie("yjsdk_at", base64_encode(json_encode($token)), 
                    time() + (14 * 24 * 60 * 60));
        }
        else {
            return false;
        }
    }

    /**
     * Fetches and returns the access token from the session store.
     *
     * @return The access token.
     */
    public function fetchAccessToken() {
        return json_decode(base64_decode($_COOKIE["yjsdk_at"]));
    }

    /**
     * Clears the access token from the session store.
     *
     * @return True on success, false otherwise.
     */
    public function clearAccessToken() {
        if(!headers_sent()) {
            return setcookie("yjsdk_at", "", time() - 600);
        }
        else {
            return false;
        }
    }
}

