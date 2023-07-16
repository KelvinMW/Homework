<?php

// Include the Gibbon database connection
require_once 'gibbon.php';

// Create the homework table
$createTableSQL = "
CREATE TABLE IF NOT EXISTS `gibbonHomework` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `subject` VARCHAR(255) NOT NULL,
    `dueDate` DATE NOT NULL,
    `description` TEXT NOT NULL,
    `instructions` TEXT,
    `classID` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($createTableSQL);