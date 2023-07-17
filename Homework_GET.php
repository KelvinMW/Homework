<?php
//$gibbon->session->redirectIfNotLoggedIn();
//import classes
use Gibbon\Comms\NotificationSender;
use Gibbon\Domain\System\NotificationGateway;
use Gibbon\Data\Validator;
use Gibbon\Contracts\Services\Session;
use Gibbon\Contracts\Database\Connection;
use Gibbon\Forms\CustomFieldHandler;
use Gibbon\Module\ExamAnalysis\Forms\BindValues;
use Gibbon\Module\Homework\Tables\HomeworkData;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Domain\School\FacilityGateway;
use Gibbon\Domain\Timetable\CourseGateway;
use Gibbon\Domain\System\AlertLevelGateway;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;
use Gibbon\Domain\DataSet;
use Gibbon\Forms\Form;
use Gibbon\Http\Url;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Gibbon\Forms\Input\Button;
use Gibbon\Forms\Input\Input;
$session = $container->get('session');
$gibbon->session = $session;
$container->share(\Gibbon\Contracts\Services\Session::class, $session);

//get alternative headers
$settingGateway = $container->get(SettingGateway::class);
$attainmentAlternativeName = $settingGateway->getSettingByScope('Markbook', 'attainmentAlternativeName');
$effortAlternativeName = $settingGateway->getSettingByScope('Markbook', 'effortAlternativeName');

if (isActionAccessible($guid, $connection2, '/modules/Homework/homework_GET.php') == false){
    $page->addError(__('You do not have access to this action.'));
}
else

{
    
    //import phpSpreadsheet classes
//require __DIR__.'/vendor/autoload.php';

// Module includes
$moduleName = $session->get('module');
require_once __DIR__ . '/moduleFunctions.php';
  // School Year Info
  $settingGateway = $container->get(SettingGateway::class);
  $attainmentAlternativeName = $settingGateway->getSettingByScope('Markbook', 'attainmentAlternativeName');
  $effortAlternativeName = $settingGateway->getSettingByScope('Markbook', 'effortAlternativeName');

 $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'] ?? $session->get('gibbonSchoolYearID');
 $gibbonCourseID = $_GET['gibbonCourseID'] ?? null;


//echo "
//<form action='Homework_POST.php' method='post'>
//    <input type='text' name='value1' placeholder='Homework details'>
//    <input type='text' name='value2' placeholder='Due date'>
//    <input type='submit' value='Post Homework'>
//</form>
//";
$form = Form::create('createIssue', $session->get('absoluteURL') . '/modules/' . $moduleName . '/Homework_POST.php', 'post');   
$form->setFactory(DatabaseFormFactory::create($pdo));     
$form->addHiddenValue('address', $session->get('address'));

//$form = Form::create('action', $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/Homework_POST.php');
                
//$form->addHiddenValue('address', $_SESSION[$guid]['address']);
            //HOMEWORK
            $form->addRow()->addHeading('Homework', __($homeworkNameSingular));

            $form->toggleVisibilityByClass('homework')->onRadio('homework')->when('Y');
            $row = $form->addRow();
                $row->addLabel('homework', __('Add {homeworkName}?', ['homeworkName' => __($homeworkNameSingular)]));
                $row->addRadio('homework')->fromArray(array('Y' => __('Yes'), 'N' => __('No')))->required()->checked('N')->inline(true);
                        //classes
$row = $form->addRow()->addClass('homework');
//$row->addLabel('gibbonCourseClassID', __('Class'))->description(__('Select class within a course/Subject.'));
$data = array('gibbonSchoolYearID' => $session->get('gibbonSchoolYearID'), 'gibbonPersonID' => $session->get('gibbonPersonID'));
$sql = "SELECT gibbonCourseClass.gibbonCourseClassID as value, CONCAT(gibbonCourse.nameShort, '.', gibbonCourseClass.nameShort) as name
    FROM gibbonCourse
    JOIN gibbonCourseClass ON (gibbonCourseClass.gibbonCourseID=gibbonCourse.gibbonCourseID)
    JOIN gibbonCourseClassPerson ON (gibbonCourseClassPerson.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID)
    WHERE gibbonPersonID=:gibbonPersonID AND gibbonSchoolYearID=:gibbonSchoolYearID AND NOT role LIKE '%- Left' ORDER BY gibbonCourseClass.name";
    $row = $form->addRow()->addClass('class bg-blue-100')->addClass('homework');
        $row->addLabel('gibbonCourseClassID', __('Select Classes'));
        $row->addCheckbox('gibbonCourseClassID')->fromQuery($pdo, $sql, $data)
            ->required();
            $row = $form->addRow()->addClass('homework');
                $row->addLabel('homeworkDueDate', __('Due Date'))->description(__('Date is required, time is optional.'));
                $col = $row->addColumn('homeworkDueDate')->addClass('homework');
                $col->addDate('homeworkDueDate')->addClass('mr-2')->required();
                $col->addTime('homeworkDueDateTime');

            $row = $form->addRow()->addClass('homework');
                $row->addLabel('homeworkTimeCap', __('Time Cap?'))->description(__('The maximum time, in minutes, for students to work on this.'));
                $row->addNumber('homeworkTimeCap');

            $row = $form->addRow()->addClass('homework');
                $column = $row->addColumn();
                $column->addLabel('homeworkDetails', __('{homeworkName} Details', ['homeworkName' => __($homeworkNameSingular)]));
                $column->addEditor('homeworkDetails', $guid)->setRows(15)->showMedia()->setValue($description)->required();

            $form->toggleVisibilityByClass('homeworkSubmission')->onRadio('homeworkSubmission')->when('Y');
            $row = $form->addRow()->addClass('homework');
                $row->addLabel('homeworkSubmission', __('Online Submission?'));
                $row->addRadio('homeworkSubmission')->fromArray(array('Y' => __('Yes'), 'N' => __('No')))->required()->checked('N')->inline(true);

            $row = $form->addRow()->setClass('homeworkSubmission');
                $row->addLabel('homeworkSubmissionDateOpen', __('Submission Open Date'));
                $row->addDate('homeworkSubmissionDateOpen')->required();

            $row = $form->addRow()->setClass('homeworkSubmission');
                $row->addLabel('homeworkSubmissionDrafts', __('Drafts'));
                $row->addSelect('homeworkSubmissionDrafts')->fromArray(array('' => __('None'), '1' => __('1'), '2' => __('2'), '3' => __('3')));

            $row = $form->addRow()->setClass('homeworkSubmission');
                $row->addLabel('homeworkSubmissionType', __('Submission Type'));
                $row->addSelect('homeworkSubmissionType')->fromArray(array('Link' => __('Link'), 'File' => __('File'), 'Link/File' => __('Link/File')))->required();

            $row = $form->addRow()->setClass('homeworkSubmission');
                $row->addLabel('homeworkSubmissionRequired', __('Submission Required'));
                $row->addSelect('homeworkSubmissionRequired')->fromArray(array('Optional' => __('Optional'), 'Required' => __('Required')))->required();

            if (isActionAccessible($guid, $connection2, '/modules/Crowd Assessment/crowdAssess.php')) {
                $form->toggleVisibilityByClass('homeworkCrowdAssess')->onRadio('homeworkCrowdAssess')->when('Y');
                $row = $form->addRow()->addClass('homeworkSubmission');
                    $row->addLabel('homeworkCrowdAssess', __('Crowd Assessment?'));
                    $row->addRadio('homeworkCrowdAssess')->fromArray(array('Y' => __('Yes'), 'N' => __('No')))->required()->checked('N')->inline(true);

                $row = $form->addRow()->addClass('homeworkCrowdAssess');
                    $row->addLabel('homeworkCrowdAssessControl', __('Access Controls?'))->description(__('Decide who can see this {homeworkName}.', ['homeworkName' => __($homeworkNameSingular)]));
                    $column = $row->addColumn()->setClass('flex-col items-end');
                        $column->addCheckbox('homeworkCrowdAssessClassTeacher')->checked(true)->description(__('Class Teacher'))->disabled();
                        $column->addCheckbox('homeworkCrowdAssessClassSubmitter')->checked(true)->description(__('Submitter'))->disabled();
                        $column->addCheckbox('homeworkCrowdAssessClassmatesRead')->description(__('Classmates'));
                        $column->addCheckbox('homeworkCrowdAssessOtherStudentsRead')->description(__('Other Students'));
                        $column->addCheckbox('homeworkCrowdAssessOtherTeachersRead')->description(__('Other Teachers'));
                        $column->addCheckbox('homeworkCrowdAssessSubmitterParentsRead')->description(__('Submitter\'s Parents'));
                        $column->addCheckbox('homeworkCrowdAssessClassmatesParentsRead')->description(__('Classmates\'s Parents'));
                        $column->addCheckbox('homeworkCrowdAssessOtherParentsRead')->description(__('Other Parents'));
            }

            $row = $form->addRow();
    $row->addFooter();
    $row->addSubmit('Post Homework');

echo $form->getOutput();

}


