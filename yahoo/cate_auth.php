<?php
require_once('lib/YahooAbstractApi.php');

class cateListApi extends YahooAbstractApi {  
  
    /** 
    *  １．APIのリクエストURL 
    */  

    const REQUEST_URL = "http://chiebukuro.yahooapis.jp/Chiebukuro/V1/questionSearch?appid=dj0yJmk9NGc4amJSS3dPcmRLJmQ9WVdrOU0wWjBjVGxITkdFbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD01Mg--&query=deecorp&type=all&condition=solved";

  
    /** 
    *  ２．コンストラクタ 
    */  
    public function __construct(YahooSession $session) {  
        parent::__construct($session);  
    }  
  
    /** 
    * ３．APIレスポンスを取得する 
    */  
    public function getResponse( $parameters ) {  
        $this->httpGet( self::REQUEST_URL, $parameters );  
        return $this->getResponseBody();  
    }  
}  
?>
