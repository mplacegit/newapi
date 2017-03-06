<?php
namespace App\Http\Controllers\Mpwidget;
use Illuminate\Http\Response;
use App\Singlewidget;
use Cache;
class ApiController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
	
	$request=app()->make('request');
    $method=$request->input('mth');
	$res=array();
	try{
		switch($method){
		case "help":
		return Response($this->getHelp());
		break;
		case "api":
		$res=Singlewidget::getInstance("Api")->getData($request->toArray());
		break;
		case "adblock":
		$res=Singlewidget::getInstance("Adblock")->getData($request->toArray());
		break;
		case "socket":
		$res=Singlewidget::getInstance("Socket")->getData($request->toArray());
		break;
		case "test":
		define("d_13",true);
		$res=Singlewidget::getInstance("Api")->getData($request->toArray());
		break;
			}
		}catch(\Exception $e){
		$res =  ["error"=>$e->getMessage()];
		}

		return response()->json($res);
		}
	public function products($hash)
    {
	
    $str=Cache::store('redis')->remember($hash, 2, function(){
          return null;
    });
	
    if($str){
    return (new Response($str))
                  ->header('Content-Type', 'application/json');
    }else{
    $str=Singlewidget::getInstance("Api")->getDefault();
	//print '<pre>';
	//print_r(json_decode($str,true));
	//print '</pre>';
	return (new Response($str))
                  ->header('Content-Type', 'application/json');
    }
	return response()->json($res);
	}
	public function offerlink($hash)
	{
	if(!defined("onclicklink")){
	define ("onclicklink",1);
	}
	try{
	$data=\App\Single\Encryption::decrypt($hash);
			
	$id=$data["id"];
	$new = new \App\Responsemethods\Yandexoffer();
	$request=[
	"id"=>$id
	];

	$res=$new->GetOfferLink($request);
	header("location:".$res);
	exit();
    return response()->json(["url"=>$res]);
	}catch(\Exception $e){
	$res=["error"=>$e->getMessage()];
	return response()->json($res);
	}
		
	}
    public function modellink($hash)
	{
	exit();
		//var_dump($hash);
	}	
	private function getHelp(){
	$str='<div>Методы</div>';
	$str.='<ul>';
	$str.='<li>';
	$str.='<b>help</b>';
	$str.='</li>';
	$str.='<li>';
	$str.='<b>api</b>Апи javasctipt';
	$str.='</li>';
	$str.='<li>';
	$str.='<b>adblock</b>Обходчик адблока';
	$str.='</li>';
	$str.='<li>';
	$str.='<b>socket</b>PHP curl';
	$str.='</li>';
	$str.='</ul>';
	return $str;
	}
}