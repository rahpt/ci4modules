<?php

namespace Rahpt\Ci4Modules\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\TemplateHelper;
use Rahpt\Ci4Modules\Helpers\ModuleRegistry;
use Rahpt\Ci4Modules\Helpers\ModuleHelper;
use Rahpt\Ci4Modules\Helpers\ModuleSetupHelper;

class ModuleInit extends BaseCommand {

    protected $group = 'Modules';
    protected $name = 'module:init';
    protected $description = 'Cria um módulo completo com estrutura de CRUD, migration, seeder e rotas.';
    protected $usage = 'module:init <NomeModulo> [Label]';

    public function run(array $params) {
        if (count($params) == 0) {
            CLI::error("Um nome de módulo precisa ser indicado!");
        }
        // Label igual ao Modulo
        if (count($params) == 1) {
            $params[] = $params[0];
        }

        if (count($params) == 2) {
            [$module, $label] = $params;
            $this->createModule($module, $label);
        }

        if (count($params) == 3) {
            [$module, $subModule, $label] = $params;
            $this->createSubModule($module, $subModule, $label);
        }
    }

    public function createModule($module, $label): void {
        if (!ModuleSetuphelper::isPatched()) {
            CLI::write("📦 Aplicando Patch de Modulos CI4 ...", 'blue');
            ModuleSetupHelper::Setup();
        }

        CLI::write("📦 Criando módulo '{$label} ({$module})'...", 'blue');
        $modulePath = ucfirst($module);
        $tableName = strtolower($module);
        $controllersPath = TemplateHelper::ModuleCreateFolder($modulePath, 'Controllers');

        TemplateHelper::ModuleCreateFolder($module, 'Models');
        TemplateHelper::ModuleCreateFolder($module, 'Database/Migrations');
        TemplateHelper::ModuleCreateFolder($module, 'Database/Seeds');
        $viewsPath = TemplateHelper::ModuleCreateFolder($modulePath, 'Views');

        // 2. Gera arquivos usando os outros comandos
        ModuleHelper::CreateRoute($module);
        ModuleHelper::CreateController($module, $controllersPath);
        ModuleHelper::CreateViewDashboard($module, $viewsPath);
        $tableExists = ModuleHelper::CreateMigration($module, $tableName);
            ModuleHelper::CreateSeeder($module, $tableName, $tableExists);
        
        ModuleHelper::CreateModel($module, $tableName, $tableExists);
        // $this->call('make:module-views', [$label, $module]);
        // Atualizar o modules.JSON
        /*
          TemplateHelper::updateModulesJson($module, $label);

         */

        // 4. Actualiza modules.json  

        $data = [
            'active' => true,
            'label' => $label,
            'path' => "app/Modules/{$modulePath}",
            'routePrefix' => strtolower($module),
            'version' => '0.1.0',
            'createdAt' => date(DATE_ATOM),
        ];

        // 2) gravar/actualizar modules.json
        ModuleRegistry::put($module, $data);

        CLI::write("✔ Módulo {$module} criado com sucesso!", 'green');
    }
}
