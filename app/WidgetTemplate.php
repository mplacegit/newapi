<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Widget;

class WidgetTemplate extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="mp_widget_templates";
    protected $fillable = [

    ];
    public static $types=array(
        1=>'block',
        2=>'module',
        3=>'table'
    );
    public static $default_attributes=array(
        'block'=>array(
            'slider_added'=>1,
            'show_rating'=>1,
            'show_discount'=>0
        ),

        'module'=>array(
            'cols'=>2,
            'rows'=>2,
            'show_rating'=>1,
            'show_discount'=>0
       ),
       'table'=>array(
           'show_rating'=>1,
           'show_discount'=>0
       )
    );

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
    public function Widgets() {

        return $this->hasMany('\App\Widget');
    }
    public function initialize(Widget $widget) {

    }
}
