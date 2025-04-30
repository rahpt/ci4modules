<?php

namespace Rahpt\Ci4Modules\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\ModuleHelper;

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

        // Main Module Route
        ModuleHelper::CreateRoute($module);
        if (count($params) == 1) {
            // Cria apenas o Modulo Pai
            return;
        }

        if (count($params) < 3) {
            CLI::error('Você deve informar o <Modulo> <SubModulo> <Label>.');
            CLI::write('Exemplo: spark module:routes Artigo "Blog"', 'yellow');
            return;
        }
        $subModule = ucfirst($parasm[1]);
        $label = $params[2];

        ModuleHelper::CreateSubRoute($module, $subModule, $label);
    }
}
