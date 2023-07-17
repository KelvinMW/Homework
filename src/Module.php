<?php
namespace Gibbon\Module\Homework;

use Gibbon\Core;
use Gibbon\Module\Homework\Tables\HomeworkTable;

class Module extends Core
{
    public function __construct($gibbon, $pdo)
    {
        $this->gibbon = $gibbon;
        $this->pdo = $pdo;
    }

    public function postHomework($homeworkData)
    {
        $homeworkTable = new HomeworkTable($this->pdo);
        return $homeworkTable->insert($homeworkData);
    }
}
