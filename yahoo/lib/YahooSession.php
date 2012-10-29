<?php
require_once('lib/YahooAuthentication.php');
/**
 * Defines a session between an application and the Yahoo! platform.
 *
 * @brief Defines a session between an application and the Yahoo! platform.
 */
class YahooSession {
    private $guid = NULL;
    private $consumer = NULL;
    private $accessToken = NULL;
    private $client = NULL;

    function __construct($consumer, $accessToken) 
    {
        $this->consumer = $consumer;
        $this->accessToken = $accessToken;
        $this->guid = $accessToken->guid;

        $this->client = new OAuthClient($consumer, $accessToken);
		
    }

    public function getConsumer() {
        return $this->consumer;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getClient() {
        return $this->client;
    }

    public function getGuid() {
        return $this->guid;
    }

    /**
     * @private
     */
    function redirectForAuthorization($consumerKey, $consumerSecret, $callback = NULL, $sessionStore = NULL) {
        $url = self::createAuthorizationUrl($consumerKey, $consumerSecret, $callback, $sessionStore);

        if(!is_null($url)) {
            header(sprintf("Location: %s", $url));
            exit();
        }
        else {
			// TODO: throw a YahooException
            YahooLogger::error("Failed to create authorization URLs");
        }
    }

    /**
     * Destroys the current session, effectively logging out the current
     * user.
     *
     * @param $sessionStore The session store implementation to clear. See 
     *                      YahooSessionStore for more information. If no 
     *                      session store is provided, clearSession will 
     *                      instantiate a CookieSessionStore and use that.
     */
    function clearSession($sessionStore = NULL) {
        global $GLOBAL_YAHOO_SESSION;

        if(is_null($sessionStore)) {
            $sessionStore = new CookieSessionStore();
        }
        
        $sessionStore->clearRequestToken();
        $sessionStore->clearAccessToken();

        $GLOBAL_YAHOO_SESSION = NULL;
    }

    /**
     * Checks to see if there is a session in this PHP page request. 
     * Doesn't cause any redirects for the user to log in, for that 
     * you should call requireSession().
     *
     * @param $consumerKey The OAuth consumer key.
     * @param $consumerSecret The OAuth consumer key secret.
     * @param $sessionStore The session store implementation to use. See 
     *                      YahooSessionStore for more information. If no 
     *                      session store is provided, clearSession will 
     *                      instantiate a CookieSessionStore and use that.
     * @return boolean True if a session is present, false otherwise.
     */
    function hasSession($consumerKey, $consumerSecret, $sessionStore = NULL, $verifier = NULL) 
    {
        if(is_null($sessionStore)) {
            $sessionStore = new CookieSessionStore();
        }

		if(is_null($verifier) && array_key_exists("oauth_verifier", $_GET)) {
            $verifier = $_GET["oauth_verifier"];
        }
        
        $session = self::initSession($consumerKey, $consumerSecret,  FALSE, NULL, $sessionStore, $verifier);
        return !is_null($session);
    }

    /**
     * Requires that there be a session in this PHP page request. Generates 
     * a redirect for the user to log in, if necessary. You must call 
     * requireSession() before any data is sent back to the user in order 
     * for the redirect to work.
     *
     * @param $consumerKey The OAuth consumer key.
     * @param $consumerSecret The OAuth consumer key secret.
     * @param $callback The callback URL to redirect the user to after 
     *                  they verify the application access. If no callback 
     *                  is provided, the current page URL will be used.
     * @param $sessionStore The session store implementation to use. See 
     *                      YahooSessionStore for more information. If no 
     *                      session store is provided, clearSession will 
     *                      instantiate a CookieSessionStore and use that.
     * @param $verifier The oauth_verifier returned by the OAuth servers 
     *                  after authorization. Passing NULL indicates that 
     *                  authorization was completed previously or that 
     *                  requireSession() should look for oauth_verifier in 
     *                  the $_GET superglobal.
     * @return YahooSession The current session or NULL if a session cannot 
     *                      be established.
     */
    public static function requireSession($consumerKey, $consumerSecret, $callback, $sessionStore = NULL, $verifier = NULL) 
    {
        if(is_null($sessionStore)) {
            $sessionStore = new CookieSessionStore();
        }

        if(is_null($verifier) && array_key_exists("oauth_verifier", $_GET)) {
            $verifier = $_GET["oauth_verifier"];
        }
        
        return self::initSession($consumerKey, $consumerSecret, TRUE, $callback, $sessionStore, $verifier);
    }

    /**
     * Creates authorization URLs, allowing applications to manage their 
     * user experience when the user needs to be sent to Yahoo! to authorize 
     * the application to access their account.
     *
     * @param $consumerKey The OAuth consumer key.
     * @param $consumerSecret The OAuth consumer key secret.
     * @param $callback The callback URL to redirect the user to after 
     *                  they verify the application access. If no callback 
     *                  is provided, the current page URL will be used. 
     *                  Use the "oob" callback for desktop clients or for 
     *                  web clients where no callback should be used.
     * @param $sessionStore The session store implementation to use. See 
     *                      YahooSessionStore for more information. If no 
     *                      session store is provided, createAuthorizationUrl
     *                      will instantiate a CookieSessionStore and use that.
     * @return stdclass A PHP object with two properties: "urlWithCallback" 
     *                  and "urlWithoutCallback". This allows the application 
     *                  to mix and match authorizations that do and don't 
     *                  have callbacks in the URLs. urlWithoutCallback is 
     *                  useful for JavaScript popup windows while 
     *                  urlWithCallback is useful for normal <a href> 
     *                  tags.
     */
    public static function createAuthorizationUrl($consumerKey, $consumerSecret, $callback, $sessionStore = NULL) 
    {
        global $GLOBAL_YAHOO_SESSION;

        if(is_null($sessionStore)) {
            $sessionStore = new CookieSessionStore();
        }

        // No callback URL supplied. Build one from the current URL.
        if(is_null($callback)) {
            $callback = YahooUtil::current_url();
        }

        if( $sessionStore instanceof NoCookieSessionStore ) {
            $callback = YahooUtil::add_session_id($callback);
        }

        // Redirect the user to log in.
        $requestToken = YahooAuthorization::getRequestToken($consumerKey, $consumerSecret, $callback);
        
        if(!is_null($requestToken)) 
        {
            $sessionStore->storeRequestToken($requestToken);

            $url = YahooAuthorization::createAuthorizationUrl($requestToken, $callback);
            return $url;
        }
        else 
        {
            YahooLogger::error("Failed to create request token");
            $GLOBAL_YAHOO_SESSION = NULL;
            return null;
        }
    }


    /**
     * @private
     */
    private static function initSession($consumerKey, $consumerSecret, $redirect, $callback, $sessionStore, $verifier) 
    {
        global $GLOBAL_YAHOO_SESSION;

        if(!is_null($GLOBAL_YAHOO_SESSION)) {
            return $GLOBAL_YAHOO_SESSION;
        }

        $consumer = new stdclass();
        $consumer->key = $consumerKey;
        $consumer->secret = $consumerSecret;

        $checkSession = self::checkSession($type, $sessionStore);
        
        if(!$checkSession) {
            // There doesn't appear to be a session here.
            if($redirect)  {
                $GLOBAL_YAHOO_SESSION = NULL;
                self::redirectForAuthorization($consumerKey, $consumerSecret, $callback, $sessionStore);
            }
            else {
                // Don't redirect the user, just inform the caller that 
                // no session is present.
				// TODO: throw a YahooException
                $GLOBAL_YAHOO_SESSION = NULL;
            }
        }
        else if($type == YAHOO_OAUTH_AT_SESSION_TYPE) {
            // Found an OAuth Access Token session.
            $accessToken = $sessionStore->fetchAccessToken();
            $now = time(); 
            
            YahooLogger::debug("OAuth AT: " . $accessToken->key . "   ATS: ". $accessToken->secret);

            if($accessToken->consumer != $consumerKey) 
            {
                YahooLogger::error("Consumer key for token does not match the defined Consumer Key. The Consumer Key has probably changed since the user last authorized the application.");
                self::clearSession($sessionStore);
                
                if($redirect) {
                    self::redirectForAuthorization($consumerKey, $consumerSecret, $callback, $sessionStore);
                }
            }
			
            if($accessToken->tokenExpires >= 0) {
                YahooLogger::debug('AT Expires in: ' . ($accessToken->tokenExpires - $now));
            }

            if(($accessToken->tokenExpires >= 0) && ($accessToken->tokenExpires - $now) < 30) {
                // The access token will expire in less than 30 seconds or 
                // it may have expired already. Try to get a new one.
                self::accessTokenExpired($accessToken, $consumer, $sessionStore);
            }
            else {
                // The access token is still good for a little while, continue using it.
                $GLOBAL_YAHOO_SESSION = new YahooSession($consumer, $accessToken);
            }
        }
        else if($type == YAHOO_OAUTH_RT_SESSION_TYPE) 
        {
            if(is_null($verifier)) {
                // Can't proceed without the oauth_verifier, treat it as 
                // though there's no session present.
                $sessionStore->clearRequestToken();

				// TODO: throw a YahooException
                $GLOBAL_YAHOO_SESSION = NULL;
            }

            // Found an OAuth Request Token session.
            $requestToken = $sessionStore->fetchRequestToken();

            $accessToken = YahooAuthorization::getAccessToken($consumerKey, $consumerSecret, $requestToken, $verifier);
			
            if(!is_null($accessToken)) {
                $sessionStore->storeAccessToken($accessToken);
                $sessionStore->clearRequestToken();

                $GLOBAL_YAHOO_SESSION = new YahooSession($consumer, $accessToken);
            }
            else if($redirect) 
			{
                // TODO: Add redirect counter so this doesn't happen over and over and over when Yahoo! is completely busted.
                // The fetch for the access token failed. Generate a new 
                // request token and try again.
                $GLOBAL_YAHOO_SESSION = NULL;
                self::redirectForAuthorization($consumerKey, $consumerSecret, $callback, $sessionStore);
            }
            else 
			{
                // Don't redirect the user, just inform the caller that 
                // no session is present.
                $sessionStore->clearRequestToken();
                $GLOBAL_YAHOO_SESSION = NULL;
            }
        }
        else 
        {
            trigger_error("Unknown session type found", E_USER_ERROR);
			// TODO: throw a YahooException
            $GLOBAL_YAHOO_SESSION = NULL;
        }

        return $GLOBAL_YAHOO_SESSION;
    }
	
    /**
     * @private
     */
    private static function accessTokenExpired($accessToken, $consumer, $sessionStore) 
	{
        global $GLOBAL_YAHOO_SESSION;

        $now = time();
        if(($accessToken->handleExpires === -1) ||
                ($now < $accessToken->handleExpires)) {
            // Either the access session handle doesn't expire 
            // or it hasn't expired yet. Get a new access token.
            $newAccessToken = YahooAuthorization::getAccessToken(
                    $consumer->key, $consumer->secret, $accessToken, null);
            if(is_null($newAccessToken)) {
                YahooLogger::error("Failed to fetch access token");
                $GLOBAL_YAHOO_SESSION = NULL;
            }

            $sessionStore->storeAccessToken($newAccessToken);

            YahooLogger::debug("Got new AT/ATS from ASH!");
            YahooLogger::debug("OAuth AT: " . $newAccessToken->key . "   ATS: ". $newAccessToken->secret);

            $GLOBAL_YAHOO_SESSION = new YahooSession($consumer, $newAccessToken);
        }
        else 
		{
            // The access token is expired and we don't have 
            // a sufficient access session handle to renew 
            // the access token. Clear the cookie and redirect 
            // to authorization point or return a NULL session.
            $sessionStore->clearAccessToken();
			
            if ($redirect) {
                self::redirectForAuthorization($consumer->key, $consumer->secret, $callback, $sessionStore);
            } else {
                $GLOBAL_YAHOO_SESSION = NULL;
            }
        }
    }

    /**
     * @private
     *
     * Checks to see if the current PHP page request has a session and, if so,
     * indicates what type of session is present.
     *
     * @param[out] $sessionType The session type present, if any.
     * @return boolean True if a session is present, false otherwise.
     */
    private static function checkSession(&$sessionType, $sessionStore) {
        if(array_key_exists("yap_appid", $_POST)) {
            $sessionType = YAHOO_YAP_SESSION_TYPE;
            return true;
        }
        else if($sessionStore->hasAccessToken()) {
            $sessionType = YAHOO_OAUTH_AT_SESSION_TYPE;
            return true;
        }
        else if($sessionStore->hasRequestToken()) {
            $sessionType = YAHOO_OAUTH_RT_SESSION_TYPE;
            return true;
        }
        else {
            return false;
        }
    }
}

