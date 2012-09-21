<?
    error_reporting(E_ALL | ~ E_STRICT);

    include_once('CAS/CAS.php');

    phpCAS::setDebug();

    // initialize phpCAS
    phpCAS::client(CAS_VERSION_2_0,'with.deecorp.jp',443,'/dee_stg/cas2');

    // force CAS authentication
    phpCAS::forceAuthentication();

    // logout if desired
    if (isset($_REQUEST['logout'])) {
        phpCAS::logout();
    }
?>

<html>
  <head>
    <title>phpCAS simple client</title>
  </head>
  <body>
    <h1>Successfull Authentication!</h1>
    <p>the user's login is <b><?php echo phpCAS::getUser(); ?></b>.</p>
    <p>phpCAS version is <b><?php echo phpCAS::getVersion(); ?></b>.</p>
    <p><a href="?logout=">Logout</a></p>
  </body>
</html>
