<?php
ini_set('date.timezone', 'Asia/Dhaka');

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';

EzMaintenance\Worker::watch('file', array(
    'path' => 'maintenance.enable',
    'template' => 'game',
   /* 'template' => 'under_construction.php',*/
    'msg' => 'Site is currently undergoing maintenance!'
));
echo "<strong>Site is currently undergoing maintenance!</strong>";
/*defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod'));

define('WEB_PATH', __DIR__);

$loader = require_once __DIR__ . '/../app/bootstrap.php.cache';

if (APPLICATION_ENV == 'dev') {
    require 'app_dev.php';
} else {
    require 'app.php';
}*/
?>
<title>Site Maintenance</title>
<style>
    body { text-align: center; padding: 150px; }
    h1 { font-size: 50px; }
    body { font: 20px Helvetica, sans-serif; color: #333; }
    article { display: block; text-align: left; width: 650px; margin: 0 auto; }
    a { color: #dc8100; text-decoration: none; }
    a:hover { color: #333; text-decoration: none; }
</style>
<article>
    <h1>We&rsquo;ll be back soon!</h1>
    <div>
        <p>Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment. If you need to you can always <a href="mailto:#">O1828148148</a>, otherwise we&rsquo;ll be back online shortly!</p>
        <p>&mdash; The Team</p>
    </div>
</article>
