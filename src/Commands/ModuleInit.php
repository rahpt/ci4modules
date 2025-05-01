<?php

namespace Rahpt\Ci4Modules\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\TemplateHelper;
use Rahpt\Ci4Modules\Helpers\ModuleRegistry;
use Rahpt\Ci4Modules\Helpers\ModuleHelper;

class ModuleInit extends BaseCommand {

    protected $group = 'Modules';
    protected $name = 'module:init';
    protected $description = 'Cria um módulo completo com estrutura de CRUD, migration, seeder e rotas.';
    protected $usage = 'module:init <NomeModulo> [Label]';

    public function run(array $params) {
        // Label igual ao Modulo
        if (count($params) == 1) {
            $params[] = $params[0];
        }

        if (count($params) < 2) {
            CLI::error('Você deve informar o nome do módulo (ex: Admin) e sua Label (ex: "Admin 1")');
            return;
        }

        [$module, $label] = $params;
        // $entityLower = strtolower($label);

        CLI::write("📦 Criando módulo '{$label} ({$module})'...", 'blue');
        $modulePath = ucfirst($module);
        $controllersPath = TemplateHelper::ModuleCreateFolder($modulePath, 'Controllers');

        TemplateHelper::ModuleCreateFolder($module, 'Models');
        // TemplateHelper::ModuleCreateFolder($module, 'Database/Migrations');
        // TemplateHelper::ModuleCreateFolder($module, 'Database/Seeds');
        $viewsPath = TemplateHelper::ModuleCreateFolder($modulePath, 'Views');

        // 2. Gera arquivos usando os outros comandos
        ModuleHelper::CreateRoute($module);
        ModuleHelper::CreateController($module, $controllersPath);
        ModuleHelper::CreateViewDashboard($module, $viewsPath);
        // $this->call('make:module-views', [$label, $module]);
        // $this->call('make:module-model', [$label, $module]);
        // $this->call('make:module-seeder', [$label, $module]);
        // $this->call('make:module-migration', [$label, $module]);
        // Atualizar o modules.JSON
        /*
          TemplateHelper::updateModulesJson($module, $label);

         */

         // 4. Actualiza modules.json  
        
        $data = [
            'active'      => true,
            'label'       => $label,
            'path'        => "app/Modules/{$modulePath}",
            'routePrefix' => strtolower($module),
            'version'     => '0.1.0',
            'createdAt'   => date(DATE_ATOM),
        ];
 
        // 2) gravar/actualizar modules.json
        ModuleRegistry::put($module, $data);

        CLI::write("✔ Módulo {$module} criado com sucesso!", 'green');
    }
}
