<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;

class ModuleRoutes {
    
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
    
}
