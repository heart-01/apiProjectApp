<?php
//เพิ่มในส่วนส่วนนี้เพื่อให้ใช้เป็น api ได้ เพิ่อจะตั้งค่าการใช้งาน
header("Access-Control-Allow-Origin:*"); //ใครบ้างที่สามารถใช้ Api นี้ได้ ถ้า * คือทุกคนสามารถเข้าถึงได้
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE"); //ให้สามารถใช้งานฟังชั่น GET POST PUT DELETE ในการแก้ไขฐานข้อมูลได้
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");//header แบบไหนที่สามารถส่งเข้ามาได้บ้าง
//

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
