<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07.02.17
 * Time: 13:12
 */

namespace App;

use Illuminate\Support\Facades\Facade;
class WidgetFacade extends Facade
{

    /**
     * Получить зарегистрированное имя компонента.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return '\App\Widget'; }

}