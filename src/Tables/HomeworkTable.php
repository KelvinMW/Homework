<?php
namespace Gibbon\Module\Homework\Tables;
//use Gibbon\Contracts\Database\Connection;
require_once '../../gibbon.php';

class HomeworkTable
{
    protected $tableName = 'gibbonPlannerEntryHomework';
//    protected $pdo;

//    public function __construct($pdo)
//    {
 //       $this->pdo = $pdo;
 //   }

    public function insert($data)
    {
        $sql = "INSERT INTO {$this->tableName} (column1, column2) VALUES (:value1, :value2)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'value1' => $data['value1'],
            'value2' => $data['value2']
        ]);
    }
}
