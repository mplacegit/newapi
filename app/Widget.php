<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class Widget extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="mp_widgets";
    protected $fillable = [

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
    public function __construct(){
       // var_dump($this->wid);
    }

}
