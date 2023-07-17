<?php
//$gibbon->session->redirectIfNotLoggedIn();
//use Gibbon\Database\Connection;
use Gibbon\Contracts\Services\Session;
use Gibbon\Contracts\Database\Connection;
use Gibbon\Forms\CustomFieldHandler;
use Gibbon\Module\Homework\Domain\HomeworkModule;
use Gibbon\Module\Homework\Tables\HomeworkData;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Domain\School\FacilityGateway;
use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;
use Gibbon\Domain\DataSet;
use Gibbon\Forms\Form;
use Gibbon\Http\Url;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Gibbon\Forms\Input\Button;
use Gibbon\Forms\Input\Input;
use Gibbon\Domain\Timetable\CourseGateway;
use Gibbon\Domain\System\AlertLevelGateway;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Comms\NotificationSender;
use Gibbon\Domain\System\LogGateway;
use Gibbon\Domain\System\NotificationGateway;
use Gibbon\Module\RecordsOfWork\Domain\IssueGateway;
use Gibbon\Module\RecordsOfWork\Domain\SubcategoryGateway;
use Gibbon\Module\RecordsOfWork\Domain\TechGroupGateway;
use Gibbon\Module\RecordsOfWork\Domain\TechnicianGateway;

require_once '../../gibbon.php';

//require_once './moduleFunctions.php';

$absoluteURL = $session->get('absoluteURL');
$moduleName = $session->get('module');
//$session = $container->get('session');
//$gibbon->session = $session;
//$container->share(\Gibbon\Contracts\Services\Session::class, $session);
$absoluteURL = $session->get('absoluteURL');
$moduleName = $session->get('module');
$URL = $absoluteURL . '/index.php?q=/modules/' . $moduleName;
//$container->share(\Gibbon\Contracts\Services\Session::class, $session);

if (!isActionAccessible($guid, $connection2, '/modules/Homework/homework_POST.php')){
    echo'You do not have access to this action.';
}
else

{

$homeworkData = $_POST;
$module = new \Gibbon\Module\Homework\Module($gibbon, $pdo);

$result = $module->postHomework($homeworkData);

if ($result) {
    echo "Homework posted successfully!";
} else {
    echo "Failed to post homework.";
}
}