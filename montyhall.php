<?php
namespace Atasoft\MHS;


spl_autoload_register(function ($class_name) {
    $class_name = str_replace(__NAMESPACE__.'\\', '', $class_name);
    include $class_name . '.php';
});

(new MainLoop())->run();
?>
