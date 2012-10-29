<?php
require_once('lib/YahooHeaderParser.php');
/**
 * A simple OAuth client class for making 2 and 3 legged OAuth HTTP requests.
 * 
 * @brief A simple OAuth client class for making 2 and 3 legged OAuth HTTP requests.
 */
class OAuthClient {
    /**
     * @private
     */
    var $consumer = NULL;

    /**
     * @private
     */
    var $token = NULL;

    /**
     * @private
     */
    var $defaultTimeout = 3;

    /**
     * @private
     */
    var $oauthParamsLocation = NULL;

    /**
     * @private
     */
    var $signatureMethod = NULL;

    var $accepts = "application/json";

    /**
     * Constructs a new OAuth client.
     * 
     * @param $consumer The OAuthConsumer object to use for the requests.
     * @param $token The OAuthToken to use for the requests. Optional.
     * @param $oauthParamsLocation OAUTH_PARAMS_IN_HEADERS or OAUTH_PARAMS_IN_POST_BODY, depending on where you want the OAuth parameters to show up. Optional, defaults to using the headers.
     * @param $signatureMethod OAUTH_SIGNATURE_PLAINTEXT or OAUTH_SIGNATURE_HMAC_SHA1, depending on what request signing mechanism to use. Optional, defaults to HMAC SHA1 signatures.
     */
    function __construct($consumer, $token = NULL, 
            $oauthParamsLocation = OAUTH_PARAMS_IN_HEADERS,
            $signatureMethod = OAUTH_SIGNATURE_HMAC_SHA1) {
        $this->consumer = $consumer;
        $this->token = $token;
        $this->oauthParamsLocation = $oauthParamsLocation;

        if($signatureMethod == OAUTH_SIGNATURE_HMAC_SHA1) {
            $this->signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
        }
        else if($signatureMethod == OAUTH_SIGNATURE_PLAINTEXT) {
            $this->signatureMethod = new OAuthSignatureMethod_PLAINTEXT();
        }
        else {
            YahooLogger::error("Invalid signature method: $signatureMethod");
        }
    }

    /**
     * Executes a properly signed OAuth HTTP GET request.
     *
     * @param $url The URL to request.
     * @param $queryParameters Any query string parameters to be sent in the request.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    public function get($url, $queryParameters = array(), $timeout = NULL) {
        if(strpos($url, "?") !== FALSE) {
            YahooLogger::error("Put the query parameters in the second argument to OAuthClient::get(), not in the URL itself: URL = $url");
            return NULL;
        }

        return $this->request(array(
                "method" => "GET", 
                "url" => $url, 
                "query" => $queryParameters,
                "timeout" => $timeout));
    }

    /**
     * Executes a properly signed OAuth HTTP DELETE request.
     *
     * @param $url The URL to request.
     * @param $queryParameters Any query string parameters to be sent in the request.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    public function delete($url, $queryParameters = array(), $timeout = NULL) {
        if(strpos($url, "?") !== FALSE) {
            YahooLogger::error("Put the query parameters in the second argument to OAuthClient::delete(), not in the URL itself: URL = $url");
            return NULL;
        }

        return $this->request(array(
                "method" => "DELETE", 
                "url" => $url, 
                "query" => $queryParameters,
                "timeout" => $timeout));
    }

    /**
     * Executes a properly signed OAuth HTTP PUT request.
     *
     * @param $url The URL to request.
     * @param $contentType The Content-Type of the PUT data.
     * @param $content The raw content to be PUT.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    public function put($url, $contentType, $content, $timeout = NULL) {
        return $this->request(array(
                "method" => "PUT", 
                "url" => $url, 
                "content" => $content, 
                "contentType" => $contentType,
                "timeout" => $timeout));
    }

    /**
     * Executes a properly signed OAuth HTTP POST request.
     *
     * @param $url The URL to request.
     * @param $contentType The Content-Type of the POST data.
     * @param $content The content to be POST.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    public function post($url, $contentType = "application/x-www-form-urlencoded", 
                $content = array(), $timeout = NULL) {
        return $this->request(array(
                "method" => "POST", 
                "url" => $url, 
                "content" => $content, 
                "contentType" => $contentType, 
                "timeout" => $timeout));
    }

    /**
     * @private
     */
    private function request($request) {
        if(!array_key_exists("content", $request)) {
            $request["content"] = array();
        }
        if(!array_key_exists("query", $request)) {
            $request["query"] = array();
        }

        if(is_array($request["content"])) {
            $combinedParams = array_merge(
                    $request["query"], $request["content"]);
        }
        else {
            $combinedParams = $request["query"];
        }

        $oauthRequest = OAuthRequest::from_consumer_and_token(
                $this->consumer, $this->token, $request["method"], 
                $request["url"], $combinedParams);
        $oauthRequest->sign_request($this->signatureMethod, $this->consumer, 
                $this->token);

        $headers = array("Accept: " . $this->accepts);
        if($this->oauthParamsLocation == OAUTH_PARAMS_IN_HEADERS) {
            $headers[] = $oauthRequest->to_header();
        }
        if(!empty($request["content"]) || $this->oauthParamsLocation == OAUTH_PARAMS_IN_POST_BODY) {
            $headers[] = "Content-Type: " . $request["contentType"];
        }

        if(!empty($request["query"])) {
            $requestUrl = sprintf("%s?%s", $request["url"], 
                    YahooUtil::oauth_http_build_query($request["query"]));
        }
        else {
            $requestUrl = $request["url"];
        }

        $requestTimeout = array_key_exists("timeout", $request) ? 
                $request["timeout"] : $this->defaultTimeout;
        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, $requestTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request["method"]);
        if(($this->oauthParamsLocation == OAUTH_PARAMS_IN_POST_BODY) || 
                (!empty($request["content"]) && is_array($request["content"]))) {
            // Content is an array, URL encode it.
            if($this->oauthParamsLocation == OAUTH_PARAMS_IN_POST_BODY) {
                $request["content"] = $oauthRequest->to_postdata();
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request["content"]);
            }
            else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, 
                        YahooUtil::oauth_http_build_query($request["content"]));
            }
        }
        else if(!empty($request["content"])) {
            // Content is raw.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request["content"]);
        }

        // Enable compressed responses from the servers.
        curl_setopt($ch, CURLOPT_ENCODING, "");

        // Set the user agent so the SDK properly identifies itself for 
        // usage tracking purposes. Include the version of the SDK and あ
        // the version of PHP being used.
        $sdkVersion = "1.0";
        $agent = sprintf("YJOAuthSDK/%s php/%s", $sdkVersion, phpversion());
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        $headerParser = new YahooHeaderParser();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$headerParser, "read"));
        $response = curl_exec($ch);
        if(is_bool($response) && !$response) {
            YahooLogger::error("Error making libcurl request(" . 
                    $requestUrl . "): " . curl_error($ch));
            return NULL;
        }

        $response = array(
            'method' => $request["method"],
            'url' => $requestUrl,
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'requestHeaders' => $headers,
            'requestBody' => !empty($request["content"]) ? $request["content"] : NULL,
            'responseHeaders' => $headerParser->headers,
            'responseBody' => $response
            );
        if($response["code"] != 200) {
            YahooLogger::error("HTTP request failed", $response);

            $this->checkExpired($response["code"], $headerParser);
            return NULL;
        }
        YahooLogger::debug("HTTP request details", $response);

        return $response;
    }

    /**
     * Checks to see if the code and headers indicate an expired OAuth token.
     * If so, requests a new one.
     *
     * @private
     */
    private function checkExpired($code, $headerParser) {
        if ($code != 401) return; // HTTP Unauthorized
        $authenticateHeader = $headerParser->get('WWW-Authenticate');
        if (!$authenticateHeader) return;
        if (!preg_match('/oauth_problem="([^"]+)"/', $authenticateHeader, $match)) return;
        $oauth_problem = $match[1];
        if ($oauth_problem == 'token_expired') {
            YahooLogger::error('Access token expired. Please fetch a new one');
        }
        if ($oauth_problem == 'consumer_key_unknown') {
            YahooLogger::error('Consumer Key unkown.  Please check that the Consumer Key is valid.');
        }
        if ($oauth_problem == 'callback_invalid') {
            YahooLogger::error('Callback URL invalid. Please check that the callback host is the one you registered and using port 80 or 443.');
        }
        if ($oauth_problem == 'additional_authorization_required') {
            YahooLogger::error('The app identified by this Consumer Key is not authorized to access this resource.  Authorization is defined under Access Scopes on the application\'s settings page.');
        }
    }
}

