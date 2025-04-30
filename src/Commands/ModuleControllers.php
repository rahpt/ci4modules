<?php

namespace Rahpt\Ci4Modules\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\TemplateHelper;
use Rahpt\Ci4Modules\Helpers\ModuleHelper;

class MakeModuleController extends BaseCommand {

    protected $group = 'Modules';
    protected $name = 'module:controllers';
    protected $description = 'Gera um Controller para um módulo baseado nas rotas CRUD';
    protected $usage = 'module:controllers <Modulo> <SubModulo>';

    public function run(array $params) {
        if (count($params) < 2) {
            CLI::error('Você deve informar o nome da entidade e o nome do módulo.');
            CLI::write('Exemplo: php spark make:module-controller Produto Admin', 'yellow');
            return;
        }

        [$entity, $module] = $params;

        ModuleHelper::CreateController($module);
        $moduleName = ucfirst($module);
        $entityName = ucfirst($entity);
        $moduleLower = strtolower($module);
        $className = ucfirst($entity) . 'Controller';
        $tableName = strtolower($entity);

        $viewPath = TemplateHelper::ModuleCreateFolder($module, 'Views');
        $controllerPath = TemplateHelper::ModuleCreateFolder($module, 'Controllers');

        $data = [
            'Module' => $moduleName,
            'module' => $moduleLower,
            'Entity' => $entityName,
            'entity' => $tableName,
        ];
        // Criar Controller da entidade
        $this->generateEntityController($controllerPath, $data);

        // Criar Controller do módulo
        $this->generateModuleController($controllerPath, $data);

        // Criar view dashboard
        $this->generateDashboardView($viewPath, $data);

        CLI::write("Controller criado: Modules/{$module}/Controllers/{$className}.php", 'green');
    }

}
