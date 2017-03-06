<?php
namespace App\Single;
class ApiA extends Api
{

    protected $headers = array(
      "Host: api.content.market.yandex.ru",
      "Accept: */*",
      //"Authorization: LO5CTaPqu6AKzZIwVDodR19HN6R9E9",
      "Authorization: VRqnbPHdHYBZL7D5ecHeUY2W895GiA",
	      );
    protected $interface="185.76.145.92";	

  public function __construct($options=[]){
    //echo __CLASS__." / конструктор<hr>";
	parent::__construct();
  } 
    public function __destruct(){
    //echo __CLASS__." / Деструктор<hr>";
	parent:: __destruct();
  } 
}