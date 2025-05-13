<?php

use Controller\FileReaderController;

require __DIR__ . '/../src/autoload.php';

$controller = new FileReaderController();
$controller->index();
