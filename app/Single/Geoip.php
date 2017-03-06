<?php
namespace App\Single;
class Geoip
{
  public static $clientIp;
  public static $geo_id;
  public static function getGeoParams(){
     if(self::$geo_id){
	 return 'geo_id='.self::$geo_id;
	 }
	 if(self::$clientIp){
	 return 'remote_ip='.self::$clientIp;
	 }
	 return 'remote_ip='.$_SERVER["REMOTE_ADDR"];

  }
}