<?php

namespace Rahpt\Ci4Modules\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\ModuleMigrationHelper;

class ModuleMigration extends BaseCommand {

    protected $group = 'Modules';
    protected $name = 'module:migration';
    protected $description = 'Gera uma migration de um módulo baseado em uma tabela existente';
    protected $usage = 'module:migration <Modulo> <nome-da-tabela>';

    public function run(array $params) {
        if (count($params) == 0) {
            CLI::error('Você deve informar ao menos o <Modulo>.');
            return;
        }

        $module = ucfirst($params[0]);
        $tableName = isset($params[1]) ? strtolower($params[1]) : strtolower($params[0]);
        CLI::write("Migration {$module} - {$tableName}", 'green');

        ModuleMigrationHelper::CreateMigration($module, $tableName);
    }
}
