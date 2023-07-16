<?php
$gibbon->session->redirectIfNotLoggedIn();

$homeworkData = $_POST;
$module = new \Gibbon\Module\Homework\Module($gibbon, $pdo);

$result = $module->postHomework($homeworkData);

if ($result) {
    echo "Homework posted successfully!";
} else {
    echo "Failed to post homework.";
}
