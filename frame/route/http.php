<?php
use SwoStar\Routes\Route;

Route::get('index',function (){
    return 'this is route index () tests';
});

Route::get('index/test', 'Index@test');