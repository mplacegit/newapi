<?php
namespace App\Offers;
use App\Single\Widget;
class Model
{
    public $id;
    public $vendorId;
    public $modelImg;
    public $modelName;
    public $modelLink; 
    public $links=[]; 

	public function add_links($links){
	$this->links=array_merge($this->links,$links);
	}	
	public function add_link($link){
	array_push($this->links,$link);
	}	
}