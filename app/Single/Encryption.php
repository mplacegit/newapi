<?php
namespace App\Single;
use Illuminate\Support\Facades\Crypt;
class Encryption
{

	private static $offerLink="http://newapi.market-place.su/api/product/";
	private static $modelLink="http://newapi.market-place.su/api/model/";	
    public static function cryptOffer($id)
    {

	$arr=["t"=>time(),"wid"=>\App\Single\Widget::$wid,"affiliate_id"=>\App\Single\Widget::$affiliate_id,"id"=>$id];
	$istr=Crypt::encrypt($arr);
	return self::$offerLink.$istr;
    }
	public static function cryptModel($id)
    {

	$arr=["t"=>time(),"wid"=>\App\Single\Widget::$wid,"affiliate_id"=>\App\Single\Widget::$affiliate_id,"id"=>$id];
	$istr=Crypt::encrypt($arr);
	return self:: $modelLink.$istr;
    }
    public  static function   decrypt($text)
    {
     return Crypt::decrypt($text);
    }
}