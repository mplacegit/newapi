<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$app->get('/api/product/{hash}', ['uses' => 'Mpwidget\ApiController@offerlink']);
$app->get('/api/model/{hash}', ['uses' => 'Mpwidget\ApiController@modellink']);
$app->get('/api/hash/{hash}', ['uses' => 'Mpwidget\ApiController@products']);

$app->get('/api', ['uses' => 'Mpwidget\ApiController@index']);



$app->get('/', function () use ($app) {
//    $conf=$app->getconf();
//    $widget=DB::table('mp_widgets')->find(1);
//    $widget=App\Widget::first();
//    $widget=$app->make('MpWidget');
////    ('','97');
//    var_dump($widget);
//    $request=$app->make('request');
//    var_dump($request->toArray());
//    $aff='56015401b3da9';
//
//    $app->singleton('Mp',function()use( $aff){
//
//        return \App\Widget::where('affiliate_id',$aff)->first();;
//    });
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    $request=$app->make('Mp');
//    var_dump("sfsd");
//    var_dump($widget->wid);
//    return $app->version();
    $w=App\WidgetFacade::first();
    var_dump($w->id);
});
