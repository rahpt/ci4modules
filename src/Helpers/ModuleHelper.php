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

    public static function appendSubmoduleRoutes(string $modulo, string $submodulo) {
        $modulo = ucfirst($modulo);
        $submodulo = ucfirst($submodulo);
        $routeFile = APPPATH . "Modules/{$modulo}/Config/Routes.php";

        // Define o conteúdo do grupo de rotas do submódulo
        $subroute = strtolower($submodulo);

        // 1.Traduz o conteudo
        $templateFile = __DIR__ . '/../Templates/Routes/SubRoutes.tpl';
        $content = TemplateHelper::generateContentFromTemplate($templateFile,
                [
                    'Module' => ucfirst($submodulo),
                    'module' => strtolower($submodulo),
                ]
        );
        
        // Bloco do grupo principal
        $groupNamespace = "App\\Modules\\{$modulo}\\Controllers";
        $mainGroupHeader = "\$routes->group('" . strtolower($modulo) . "', ['namespace' => '{$groupNamespace}'], function (\$routes) {";
        $mainGroupFooter = "});";

        // Caso o arquivo não exista ainda
        if (!file_exists($routeFile)) {
            ModuleHelper::CreateRoute($module);
        }

        // Se o arquivo já existe, vamos modificar
        $original = file_get_contents($routeFile);

        // Verifica se o subgrupo já está registrado
        if (strpos($original, "group('{$subroute}'") !== false) {
            return; // Já existe, não adiciona novamente
        }

        // Insere o novo grupo dentro do grupo principal
        if (strpos($original, $mainGroupHeader) === false) {
            // Não tem grupo principal ainda
            $original .= "\n\n{$mainGroupHeader}\n{$subgroupBlock}\n{$mainGroupFooter}\n";
        } else {
            // Insere dentro do grupo principal
            $original = preg_replace(
                    "/(" . preg_quote($mainGroupHeader, '/') . ")(.*)(" . preg_quote($mainGroupFooter, '/') . ")/sU",
                    "\$1\$2\n{$subgroupBlock}\n\$3",
                    $original
            );
        }

        file_put_contents($routeFile, $original);
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
