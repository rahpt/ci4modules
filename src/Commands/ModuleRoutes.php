<?php

namespace Rahpt\Ci4Modules\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\ModuleHelper;
use Rahpt\Ci4Modules\Helpers\SubModuleHelper;

class ModuleRoutes extends BaseCommand {

    protected $group = 'Modules';
    protected $name = 'module:routes';
    protected $description = 'Cria rotas REST básicas (CRUD) para um módulo no formato Routes.php';
    protected $usage = 'module:routes <nome-do-modulo>';

    public function run(array $params) {
        if (count($params) == 0) {
            CLI::error('Você deve informar ao menos o <Modulo>.');
            return;
        }
        $module = ucfirst($params[0]);
        $routeFile = APPPATH . "Modules/{$module}/Config/Routes.php";
        if (!file_exists($routeFile)) {
            ModuleHelper::CreateRoute($module);
        }
        
        if (count($params) == 1) {
            // Cria apenas o Modulo Pai
            return;
        }

        $subModule = ucfirst($params[1]);

        SubModuleHelper::CreateRoute($module, $subModule);
    }
}
