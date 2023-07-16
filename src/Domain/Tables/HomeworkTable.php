<?php
namespace Gibbon\Module\Homework\Tables;

class HomeworkTable
{
    protected $tableName = 'gibbonPlannerEntryHomework';
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert($data)
    {
        $sql = "INSERT INTO {$this->tableName} (column1, column2) VALUES (:value1, :value2)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'value1' => $data['value1'],
            'value2' => $data['value2']
        ]);
    }
}
