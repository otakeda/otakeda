<?php

// デバッグログ出力
$GLOBAL_YAHOO_LOGGER_DEBUG = true;


$GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION = "LOG";
$GLOBAL_YAHOO_SESSION = NULL;

define("OAUTH_PARAMS_IN_HEADERS", "HEADERS");
define("OAUTH_PARAMS_IN_POST_BODY", "POSTBODY");
define("OAUTH_SIGNATURE_PLAINTEXT", "PLAINTEXT");
define("OAUTH_SIGNATURE_HMAC_SHA1", "HMAC_SHA1");

define("YAHOO_OAUTH_RT_SESSION_TYPE", "YAHOO_OAUTH_RT_SESSION_TYPE");
define("YAHOO_OAUTH_AT_SESSION_TYPE", "YAHOO_OAUTH_AT_SESSION_TYPE");

$YahooConfig = array(
    "OAUTH_HOSTNAME" => "auth.login.yahoo.co.jp",
);

if(!class_exists("OAuthConsumer")) {
    require_once("lib/OAuth.php");
}
require_once("lib/YahooUtil.php");
require_once("lib/YahooLogger.php");
require_once("lib/YahooSession.php");
require_once("lib/CookieSessionStore.php");
require_once("lib/NoCookieSessionStore.php");
require_once("lib/OAuthClient.php");

?>
