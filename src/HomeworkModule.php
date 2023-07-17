<?php
namespace Gibbon\Module\Homework;

use Gibbon\Core;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Services\Format;
use Gibbon\Contracts\Database\Connection;

use Gibbon\Module\Homework\Tables\HomeworkTable;

class HomeworkModule extends Core
{
    public function __construct()
    {
        $this->db = new Database();
    }

    public function postHomework($homeworkData)
    {
        $homeworkTable = new HomeworkTable($this->db);
        return $homeworkTable->insert($homeworkData);
    }
}
