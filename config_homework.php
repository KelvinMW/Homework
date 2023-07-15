<?php
// config_homework.php
// Create a module configuration file that defines the module's settings

// Register the module in GibbonEdu
Gibbon\Module\Homework\HomeworkModule::register();

// routes_homework.php
// Add the necessary routes for the module
$router->map(['GET', 'POST'], '/homework', 'Gibbon\Module\Homework\HomeworkController@index');
$router->map('POST', '/homework/save', 'Gibbon\Module\Homework\HomeworkController@save');

?>