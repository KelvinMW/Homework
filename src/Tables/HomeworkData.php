<?php
namespace Gibbon\Module\Homework\Tables;

class HomeworkData
{
    protected $pdo;

    public function __construct(\Gibbon\Database\Connection $pdo)
    {
        $this->pdo = $pdo->getConnection();
    }
    
    public function getHomeworkData($startDate, $endDate)
    {
        $data = array('startDate'=>$startDate, 'endDate'=>$endDate);
        $sql = "SELECT name, date, homeworkDueDateTime, gibbonPersonIDCreator
                FROM gibbonPlannerEntry
                JOIN gibbonPerson ON gibbonPlannerEntry.gibbonPersonIDCreator = gibbonPerson.gibbonPersonID
                WHERE gibbonPlannerEntry.date>=:startDate AND gibbonPlannerEntry.date <= :endDate
                ORDER BY date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchAll();
    }
}
