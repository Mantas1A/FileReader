<?php

// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Display full PHP configuration
echo "<h1>Full PHP Configuration</h1>";
phpinfo();

// Display specific configuration sections
echo "<h1>Environment Variables</h1>";
echo "<pre>";
print_r($_ENV);
echo "</pre>";

echo "<h1>Server Variables</h1>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";

echo "<h1>PHP Disabled Functions</h1>";
echo "<pre>";
$disabled_functions = ini_get('disable_functions');
echo $disabled_functions ? $disabled_functions : "No functions are disabled";
echo "</pre>";

echo "<h1>Current Environment Variables</h1>";
echo "<pre>";
print_r(getenv());
echo "</pre>";

echo "<h1>Upload Configuration</h1>";
echo "<pre>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "</pre>";

echo "<h1>Important PHP Extensions</h1>";
echo "<pre>";
$extensions = get_loaded_extensions();
sort($extensions);
print_r($extensions);
echo "</pre>";

// Test putenv functionality
echo "<h1>putenv() Test</h1>";
echo "<pre>";
$testVar = "TEST_VAR_" . time();
$testValue = "test_value_" . time();
$result = putenv("$testVar=$testValue");
echo "putenv() test result: " . ($result ? "Success" : "Failed") . "\n";
echo "getenv() test result: " . (getenv($testVar) ?: "Not found") . "\n";
echo "</pre>"; 