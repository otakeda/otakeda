<?php
/**
 *  Yahoo Authorization URL class
 */
class YahooAuthorization {
    public static function getRequestToken($consumerKey, $consumerSecret, $callback) {
        global $YahooConfig;

        if(is_null($callback)) {
            $callback = "oob";
        }

        $consumer = new OAuthConsumer($consumerKey, $consumerSecret);
        $client = new OAuthClient($consumer, NULL, OAUTH_PARAMS_IN_POST_BODY, OAUTH_SIGNATURE_HMAC_SHA1);

        $request_url = sprintf("https://%s/oauth/v2/get_request_token", $YahooConfig["OAUTH_HOSTNAME"]);
		$parameters = array("oauth_callback" => $callback);
		
        $response = $client->post($request_url, "application/x-www-form-urlencoded", $parameters);

        if(is_null($response)) {
            YahooLogger::error("OAuth call to get request token failed");
            return NULL;
        }

        parse_str($response["responseBody"], $token);

        if($response["code"] != 200) {
            $problem = array_key_exists("oauth_problem", $token) ? 
                    $token["oauth_problem"] : "unknown problem";
            YahooLogger::error("Failed to create request token: $problem");
            return NULL;
        }

        if(!array_key_exists("oauth_callback_confirmed", $token) || 
                !$token["oauth_callback_confirmed"]) {
            // Callback wasn't confirmed.
            YahooLogger::error("Failed to create request token: callback was not confirmed");
            return NULL;
        }

        $requestToken = new stdclass();
        $requestToken->key = $token["oauth_token"];
        $requestToken->secret = $token["oauth_token_secret"];
        return $requestToken;
    }

    public static function createAuthorizationUrl($requestToken) {
        global $YahooConfig;

        if(!is_object($requestToken) || !property_exists($requestToken, "key")) {
            YahooLogger::error("Request token doesn't have a 'key' property");
            return NULL;
        }

        return sprintf("https://%s/oauth/v2/request_auth?oauth_token=%s", $YahooConfig["OAUTH_HOSTNAME"], urlencode($requestToken->key));
    }

    public static function getAccessToken($consumerKey, $consumerSecret, $requestToken, $verifier) 
	{
        $at = YahooAuthorization::getAccessTokenProxy($consumerKey, $consumerSecret, $requestToken, $verifier);

        if(is_null($at)) {
            // Failed to fetch the access token, sleep for 250ms and 
            // then try one more time.
            YahooLogger::info("Failed to fetch access token, retrying");
            usleep(250000);
            $at = YahooAuthorization::getAccessTokenProxy($consumerKey, $consumerSecret, $requestToken, $verifier);
        }

        return $at;
    }

    public static function getAccessTokenProxy($consumerKey, $consumerSecret, $requestToken, $verifier) 
    {
        global $YahooConfig;

        $request_url = sprintf("https://%s/oauth/v2/get_token", $YahooConfig["OAUTH_HOSTNAME"]);
		
        $consumer = new OAuthConsumer($consumerKey, $consumerSecret);

        $parameters = array();
        if(property_exists($requestToken, "sessionHandle")) {
            $parameters["oauth_session_handle"] = $requestToken->sessionHandle;
        }

        if(!is_null($verifier)) {
            $parameters["oauth_verifier"] = $verifier;
        }

        $client = new OAuthClient($consumer, $requestToken, OAUTH_PARAMS_IN_POST_BODY);
		
        $response = $client->post($request_url, "application/x-www-form-urlencoded", $parameters);

        if(is_null($response)) {
            YahooLogger::error("OAuth call to get access token failed");
            return NULL;
        }

        parse_str($response["responseBody"], $token);

        if($response["code"] != 200) {
            YahooLogger::error("Failed to fetch access token: " . $token["oauth_problem"]);
            return NULL;
        }

        $now = time();

        $accessToken = new stdclass();
        $accessToken->key = $token["oauth_token"];
        $accessToken->secret = $token["oauth_token_secret"];
        $accessToken->guid = $token["xoauth_yahoo_guid"];
        $accessToken->consumer = $consumerKey;
        $accessToken->sessionHandle = $token["oauth_session_handle"];

        // Check to see if the access token ever expires.
        YahooLogger::debug('AT expires in '.$token['oauth_expires_in'].'; ASH expires in '.$token["oauth_authorization_expires_in"]);
        if(array_key_exists("oauth_expires_in", $token)) {
            $accessToken->tokenExpires = $now + $token["oauth_expires_in"];
        }
        else {
            $accessToken->tokenExpires = -1;
        }

        // Check to see if the access session handle ever expires.
        if(array_key_exists("oauth_authorization_expires_in", $token)) {
            $accessToken->handleExpires = $now + 
                    $token["oauth_authorization_expires_in"];
        }
        else {
            $accessToken->handleExpires = -1;
        }
        return $accessToken;
    }
}
