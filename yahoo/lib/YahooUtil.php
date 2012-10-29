<?php
/**
 * Yahoo Utility class
 */
final class YahooUtil {

    /**
     *  Get current URL
     */
	public static function current_url() {
		return sprintf("http://%s%s",$_SERVER["SERVER_NAME"],$_SERVER["SCRIPT_NAME"]);
	}
	
    /**
     *  Verfy signature
     */
    public static function verify_signature($consumer, $token=NULL, $oauth_signature) {
        $oauth_signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        $oauth_consumer = new OAuthConsumer($consumer->key, $consumer->secret);
        $oauth_token = ($token) ? new OAuthToken($token->key, $token->secret) : NULL;
        $oauth_request = OAuthRequest::from_request();
		
        $ok = $oauth_signature_method->check_signature($oauth_request, $oauth_consumer, $oauth_token, $oauth_signature);

        return $ok;
    }

	public static function is_response_error($response) {
		return (is_null($response) || $response["code"] != 200);
	}

    /**
     * An OAuth compatible version of http_build_query. http_build_query 
     * doesn't work because it turns spaces into "+", which isn't allowed 
     * by OAuth.
     */
    public static function oauth_http_build_query($parameters) {
        $strings = array();
        foreach($parameters as $name => $value) {
            $strings[] = sprintf("%s=%s", rawurlencode($name), rawurlencode($value));
        }
        $query = implode("&", $strings);
        return $query;
    }


    /**
     *  Add session id to URL when using no cookie session store.
     */
    public static function add_session_id($url) {
        list( $url, $flagment ) = explode( '#', $url );
        $separator = ( strpos( $url, '?' ) === FALSE ) ? '?' : '&';
        $flagment = ( $flagment === NULL ) ? '' : '#' . $flagment;
        return $url . $separator . 'sid=' . session_id() . $flagment;
    } 
}

