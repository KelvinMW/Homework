<?php
namespace Gibbon\Module\Homework\Tables;

class HomeworkData
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getHomeworkData($startDate, $endDate)
    {
        $sql = "SELECT gibbonPlannerEntryHomework.*, gibbonPerson.*
                FROM gibbonPlannerEntryHomework
                JOIN gibbonPlannerEntryStudentHomework ON gibbonPlannerEntryHomework.gibbonPlannerEntryHomeworkID = gibbonPlannerEntryStudentHomework.gibbonPlannerEntryHomeworkID
                JOIN gibbonPerson ON gibbonPlannerEntryHomework.gibbonPersonIDCreator = gibbonPerson.gibbonPersonID
                WHERE gibbonPlannerEntryHomework.date >= :startDate AND gibbonPlannerEntryHomework.date <= :endDate
                ORDER BY gibbonPlannerEntryHomework.date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);

        return $stmt->fetchAll();
    }
}
