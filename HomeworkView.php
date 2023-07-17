<?php
//$gibbon->session->redirectIfNotLoggedIn();
use Gibbon\Contracts\Services\Session;
use Gibbon\Contracts\Database\Connection;
use Gibbon\Forms\CustomFieldHandler;
use Gibbon\Module\ExamAnalysis\Forms\BindValues;
use Gibbon\Module\Homework\
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
$settingGateway = $container->get(SettingGateway::class);
$attainmentAlternativeName = $settingGateway->getSettingByScope('Markbook', 'attainmentAlternativeName');
$effortAlternativeName = $settingGateway->getSettingByScope('Markbook', 'effortAlternativeName');

if (isActionAccessible($guid, $connection2, '/modules/Exam Analysis/analysis_view.php') == false){
    $page->addError(__('You do not have access to this action.'));
}
else

{

$homeworkData = new \Gibbon\Module\Homework\Tables\HomeworkData($pdo);

if ($_POST) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $data = $homeworkData->getHomeworkData($startDate, $endDate);
} else {
    $data = [];
}

echo "
<form action='HomeworkView.php' method='post'>
    <input type='date' name='startDate'>
    <input type='date' name='endDate'>
    <input type='submit' value='Filter'>
</form>
";

foreach ($data as $homework) {
    echo "<p>{$homework['homeworkName']} by {$homework['preferredName']}</p>";
}
}
?>
<canvas id="homeworkChart"></canvas>

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