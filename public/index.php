<?php
error_reporting(1);

use App\Route\Route;
use App\Http\UploadController;
use App\Http\PagesController;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../app/Bootstrap.php';

Route::add('/phpinfo', function() {
    phpinfo();
});


Route::add('/sample/pdf', function() {

    $task = new PagesController('pdf');
    print_r($task->view());

}, 'get');

Route::add('/sample/upload-pdf', function() {

    $task = new UploadController('pdf');

    $task::start();
}, 'post');

Route::run(BASEPATH);
?>