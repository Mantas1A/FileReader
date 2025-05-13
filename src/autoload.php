<?php

spl_autoload_register(function (string $class): void {
    // Base directory for all classes
    $baseDir = __DIR__;
    
    // Base namespace prefix
    $prefix = '';
    
    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($class, $len);
    
    // Replace namespace separators with directory separators
    // and append .php
    $class = str_replace("src\\", '', $class);
    $class = str_replace("\\", '/', $class);
    $file = $baseDir . "/" . $class . ".php";
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
