<?php
namespace App\Single;
use Cache;
class Api
{
  protected $url = "https://api.content.market.yandex.ru/v1/";
  private $Curl;

  public function __construct($options=[]){
  $this->connect__();
    //echo __CLASS__." / конструктор<hr>";
  } 
  protected function connect__(){
    $this->Curl=curl_init();
	curl_setopt($this->Curl, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($this->Curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->Curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($this->Curl, CURLOPT_FORBID_REUSE, false);
	curl_setopt($this->Curl, CURLOPT_FRESH_CONNECT, false);
	curl_setopt($this->Curl, CURLOPT_INTERFACE, $this->interface);
  }
  
  public function executeSearch($url,$cnt=1){
  
		if($cnt<=0) return false;
        $iurl=$this->url.$url;
        curl_setopt($this->Curl, CURLOPT_URL, $iurl);
        $jstr = curl_exec($this->Curl);
        if(mb_strlen($jstr)<175 && preg_match('/errors/',$jstr)){
		usleep(500);
		return $this->executeSearch($url,($cnt-1));
     	//return false;
        }
        return  $jstr;
  }
  public function __destruct(){
    $this->disconnect__();
    //echo __CLASS__." / Деструктор<hr>";
  }
 protected function disconnect__(){
 if($this->Curl) 
 curl_close($this->Curl);
 } 
}