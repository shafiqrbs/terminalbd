<?php

spl_autoload_register(function ($sClassName) {
    $name_space_part = explode('\\', $sClassName);
    if ($name_space_part[0] === 'EzMaintenance') {
        $sClassName = implode(DIRECTORY_SEPARATOR, $name_space_part);
        $file = __DIR__ . DIRECTORY_SEPARATOR . $sClassName . '.php';
        require_once($file);
    }
});
