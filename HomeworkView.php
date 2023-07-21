<?php
//$gibbon->session->redirectIfNotLoggedIn();
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

//require_once '../../gibbon.php';
require_once __DIR__ . '/../../gibbon.php';

$settingGateway = $container->get(SettingGateway::class);
$session = $container->get('session');
$gibbon->session = $session;
$container->share(\Gibbon\Contracts\Services\Session::class, $session);

if (isActionAccessible($guid, $connection2, '/modules/Homework/HomeworkView.php') == false){
    $page->addError(__('You do not have access to this action.'));
}
else

{
//$homeworkData = new HomeworkData($gibbon->sqlConnection);
//$homeworkData = $container->get(HomeworkData::class);
$homeworkData = new \Gibbon\Module\Homework\Tables\HomeworkData($pdo);
//$homeworkData = new HomeworkData($gibbon->session->get('database'));
//$endDate = date('Y-m-d');
//$startDate = date('Y-m-d', strtotime($endDate . ' -5 days'));
if ($_POST) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $data = $homeworkData->getHomeworkData($startDate, $endDate);
    if(empty($data)){
        $data = [];
    }
} else {
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime($endDate . ' -5 days'));
}
$form = Form::create('HomeworkStatistics', $session->get('absoluteURL').'/index.php?q=/modules/Homework/HomeworkView.php');

$form->addHiddenValue('address', $session->get('address'));

$row = $form->addRow();
    $row->addLabel('startDate', __('Start Date'));
    $row->addDate('startDate')->setValue(Format::date($startDate))->required();

$row = $form->addRow();
    $row->addLabel('endDate', __('End Date'));
    $row->addDate('endDate')->setValue(Format::date($endDate))->required();
//$sql = "SELECT gibbonYearGroup.name as value, name FROM gibbonYearGroup ORDER BY sequenceNumber";
//$row = $form->addRow();
//    $row->addLabel('gibbonYearGroupID', __('Year Group'));
//    $row->addSelect('gibbonYearGroupID')->fromArray(array('all' => __('All')))->fromQuery($pdo, $sql)->selectMultiple()->selected($yearGroups);
$form->addRow()->addSubmit();
echo $form->getOutput();

$data = $homeworkData->getHomeworkData($startDate, $endDate);
$post = $homeworkData->getHomeworkDataPosts($startDate, $endDate);
if(empty($data)){
    $data = [];
}
/*echo "<form action='HomeworkView.php' method='post'>
<input type='date' name='startDate' value = $startDate>
<input type='date' name='endDate' value = $endDate>
<input type='submit' value='Filter'>
</form>
<br>
<br>
*/
echo "<canvas id='homeworkChart'></canvas>";
    // DATA TABLE
    $table = DataTable::create('Homework');
    $table->setTitle(__('Posted Homework'));
    $table->addColumn('preferredName', __('Teacher'))->sortable();
    $table->addColumn('class', __('Class'))
          ->sortable()
          ->format(Format::using('courseClassName', ['CourseName', 'class']));
//    $table->addColumn('CourseName', __('Course'));
//    $table->addColumn('class', __('Class'));
    $table->addColumn('homeworkDetails', __('homework Details'));
    $table->addColumn('date', __('Date Posted'))->sortable();
//foreach ($data as $homework) {
//    $row->"<p>{$homework['gibbonCourse.name']}.{$homework['gibbonCourseClass.name']} {$homework['homeworkDetails']} by <b>{$homework['title']} {$homework['preferredName']}</b> on {$homework['date']}<p>";
//}
echo $table->render($post);
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let dates = <?php echo json_encode(array_column($data, 'date')); ?>;
let counts = dates.reduce((a, c) => (a[c] = (a[c] || 0) + 1, a), {});
new Chart(document.getElementById('homeworkChart'), {
    type: 'line',
    data: {
        labels: Object.keys(counts),
        datasets: [{
            label: 'Posted Homework',
            data: Object.values(counts),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
        }]
    },
    options: {
        scales: {
            x: { display: true, title: { text: 'Date' } },
            y: { display: true, title: { text: 'Homework Count' } }
        }
    }
});
</script>