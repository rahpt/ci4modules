<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\ModuleRoutes;
use Rahpt\Ci4Modules\Helpers\ModuleMigrationHelper;
use Rahpt\Ci4Modules\Helpers\ModuleSeederHelper;
use Rahpt\Ci4Modules\Helpers\ModuleModelHelper;

class ModuleHelper {

    // ROUTES
    public static function CreateRoute(string $module) {
        return ModuleRoutes::CreateRoute($module);
    }

    public static function appendSubmoduleRoutes(string $modulo, string $submodulo) {
        return ModuleRoutes::appendSubmoduleRoutes($modulo, $submodulo);
    }

    // DATABASE
    public static function CreateMigration(string $module, string $tableName) {
        return ModuleMigrationHelper::CreateMigration($module, $tableName);
    }

    public static function CreateSeeder(string $module, string $tableName, bool $tableExists) {
        return ModuleSeederHelper::CreateSeeder($module, $tableName, $tableExists);
    }

    // MODELS
    public static function CreateModel(string $module, string $tableName) {
        return ModuleModelHelper::CreateModel($module, $tableName);
    }

    // CONTROLLERS
    // Main Controller
    public static function CreateController(string $module, string $filePath): void {

        $Module = ucfirst($module);
        $className = $Module . 'Controller';

        $content = TemplateHelper::generateContentFromTemplate(
                TemplateHelper::getTemplatePath('Controllers/ControllerModule.tpl'),
                ['Module' => $Module]
        );

        file_put_contents($filePath . "{$className}.php", $content);
        CLI::write("Module Controller criado: Modules/{$Module}/Controllers/{$className}.php", 'green');
    }

    protected function generateEntityController($filePath, $data) {
        $templateFile = TemplateHelper::isShieldInstalled() ?
                TemplateHelper::getTemplatePath('Controllers/ControllerEntityWithPolicy.tpl') :
                TemplateHelper::getTemplatePath('Controllers/ControllerEntity.tpl');

        if (TemplateHelper::isShieldInstalled()) {
            CLI::write('Gerando Controller com Policy (Shield detectado).', 'cyan');
        } else {
            CLI::write('Gerando Controller sem Policy (Shield não detectado).', 'yellow');
        }

        $content = TemplateHelper::generateContentFromTemplate(
                $templateFile, $data
        );
        $className = $data['Entity'] . 'Controller';

        file_put_contents($filePath . "{$className}.php", $content);
        CLI::write("Entity Controller criado: Modules/{$data['Module']}/Controllers/{$className}.php", 'green');
    }

    // VIEWS
    public static function CreateViewDashboard(string $module, string $viewsPath) {
        $fileName = 'dashboard.php';
        $Module = ucfirst($module);

        if (file_exists($viewsPath . $fileName)) {
            CLI::error("A view Dashboard (Modules/{$Module}/Views/{$fileName}) já existe!");
            return;
        }

        $config = config('ModuleTemplate');
        $templateFile = $config->templates['viewIndex'] ??
                TemplateHelper::getTemplatePath('Views/dashboard.tpl');

        $content = TemplateHelper::generateContentFromTemplate(
                $templateFile,
                ['Module' => $Module]);

        file_put_contents($viewsPath . $fileName, $content);
        CLI::write("Simple Dashboard criado: Modules/{$Module}/Views/{$fileName}", 'green');
    }
}
