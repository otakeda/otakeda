<?php
/**
 *   ヘッダー(DB connection定義、各アクセス用ID) 
 **/
 
require_once "cate_env.inc";

        $db=null;
	if (SERVENV=="hon") 
	{
		define("YAHOO_ID", "dj0yJmk9NGc4amJSS3dPcmRLJmQ9WVdrOU0wWjBjVGxITkdFbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD01Mg--");
		$consumer_key="dj0yJmk9NGc4amJSS3dPcmRLJmQ9WVdrOU0wWjBjVGxITkdFbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD01Mg--";
		$consumer_secret="44b2e124be1c7877c9e06b5d362d4e96a00b770d";
	}
	else
	{
		define("YAHOO_ID", "dj0yJmk9QUM5b25jdjMzcHQ4JmQ9WVdrOVUxUndTREF4TldVbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1hMA--");
		$consumer_key="dj0yJmk9QUM5b25jdjMzcHQ4JmQ9WVdrOVUxUndTREF4TldVbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1hMA--";
		$consumer_secret="1dd2135324e6e32d22fb0dd29520720a5a032fbb";
	}

?>

