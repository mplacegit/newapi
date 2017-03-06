<?php
namespace App\Requestmethods;
/*
тестовая ссылка
http://newapi.market-place.su/api?mth=socket&data={%22aff_id%22:%22572ba8d726ed1%22,%22wid%22:1,%22models%22:%22772455%22,%22cid%22:%2290560%22,%22text%22:%22Msi%22,%22offers_count%22:%225%22,%22offers_link_count%22:%227%22,%22url%22:%22https:\/\/naobzorah.ru\/player\/msi_mega_stick_528_1_gb%22,%22ip%22:%2266.249.76.58%22}

*/
class Socket extends Abs
{

  public function getData($data){
  
  $mcData=json_decode($data["data"],true);
  print_r($mcData);
  exit();
  throw new \Exception('нет метода '.__CLASS__);
  }
}