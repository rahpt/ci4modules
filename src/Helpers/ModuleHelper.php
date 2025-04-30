<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;

class ModuleHelper {

    // ROUTES
    public static function CreateRoute(string $module) {
        // 1.Traduz o conteudo
        $templateFile = __DIR__ . '/../Templates/Routes/Routes.tpl';
        $content = TemplateHelper::generateContentFromTemplate($templateFile,
                [
                    'Module' => ucfirst($module),
                    'module' => strtolower($module),
                ]
        );

        // 2.Cria o diretorio
        $filePath = TemplateHelper::ModuleCreateFolder($module, 'Config');

        // 3. Salva o arquivo
        file_put_contents($filePath . 'Routes.php', $content);
        CLI::write("Rota principal criada com sucesso em Modules/{$module}/Config/Routes.php", 'green');
    }

    public static function CreateSubRoute(string $module, string $subModule, string $label): void {

        $Module = ucfirst($module);
        $SubModule = ucfirst($subModule);

        $className = ucfirst($submodule) . 'Controller';
        $routeBase = strtolower($submodule);

        $templateFile = __DIR__ . '/../Templates/Routes.tpl';
        $content = TemplateHelper::generateContentFromTemplate(
                $templateFile,
                [
                    'Module' => $Module,
                    'Entity' => $submodule,
                    'module' => strtolower($module),
                    'entity' => $submodule,
                    'Controller' => $className,
                    'RouteBase' => $routeBase,
                ]
        );

        $fileName = 'Routes.php';
        /*
          if (file_exists($filePath . $fileName)) {
          CLI::error("O arquivo de rotas já existe: Modules/{$module}/Config/Routes.php");
          return;
          }
         */
        $filePath = TemplateHelper::ModuleCreateFolder($module, 'Config');
        file_put_contents($filePath . $fileName, $content);
    }

    // CONTROLLERS
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
