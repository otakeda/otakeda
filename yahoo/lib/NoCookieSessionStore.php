<?php
include_once( 'lib/YahooSessionStore.php' );
/**
 * @brief URL Parameter-based implementation of the session store.
 */
class NoCookieSessionStore implements YahooSessionStore {

    /**
     * constructor
     */
    function __construct() {
        $this->_init();
    }

    /**
     * Initialize session. 
     */
    private function _init() {
        ini_set( 'session.use_cookies', '0' );
        ini_set( 'session.use_trans_sid', '1' );
        $sid = $_GET['sid'];
        if ( !empty( $sid ) ) {
            session_id( $sid );
        }
        session_start(); 
    }

    /**
     * Indicates if the session store has a request token.
     *
     * @return True if a request token is present, false otherwise.
     */
    public function hasRequestToken() {
        return array_key_exists( "rtoken", $_SESSION ) && ( strlen( $_SESSION["rtoken"] ) > 0 );
    }

    /**
     * Indicates if the session store has an access token.
     *
     * @return True if an access token is present, false otherwise.
     */
    public function hasAccessToken() {
        return array_key_exists( "atoken", $_SESSION ) && ( strlen( $_SESSION["atoken"] ) > 0 );
    }

    /**
     * Stores the given request token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of 
     *               the OAuth request token.
     * @return True on success, false otherwise.
     */
    public function storeRequestToken($token) {
        $_SESSION['rtoken'] = base64_encode( json_encode( $token ) );
    }

    /**
     * Fetches and returns the request token from the session store.
     *
     * @return The request token.
     */
    public function fetchRequestToken() {
        return json_decode( base64_decode( $_SESSION['rtoken'] ) );
    }

    /**
     * Clears the request token from the session store.
     *
     * @return True on success, false otherwise.
     */
    public function clearRequestToken() {
        $_SESSION['rtoken'] = NULL;
    }

    /**
     * Stores the given access token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of 
     *               the OAuth access token.
     * @return True on success, false otherwise.
     */
    public function storeAccessToken($token) {
        $_SESSION['atoken'] = base64_encode( json_encode( $token ) );
    }

    /**
     * Fetches and returns the access token from the session store.
     *
     * @return The access token.
     */
    public function fetchAccessToken() {
        return json_decode( base64_decode( $_SESSION['atoken'] ) );
    }

    /**
     * Clears the access token from the session store.
     *
     * @return True on success, false otherwise.
     */
    public function clearAccessToken() {
        $_SESSION['atoken'] = NULL;
    }
}

