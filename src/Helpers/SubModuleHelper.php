<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;

class SubModuleHelper {

    public static function createSubRoute($module, $sub) {
        // 1.Traduz o conteudo
        $templateFile = __DIR__ . '/../Templates/Routes/SubRoutes.tpl';
        $content = TemplateHelper::generateContentFromTemplate($templateFile,
                [
                    'Module' => ucfirst($sub),
                    'module' => strtolower($sub),
                ]
        );

        // 2.Cria o diretorio
        $filePath = TemplateHelper::ModuleCreateFolder($module, 'Config');

        // Prpara conteeudo
        $marker = 'rotas-' . strtolower($sub);
        $fileRoute = $filePath . 'Routes.php';
        $mainFooter = "});";

        $original = file_get_contents($fileRoute);

        // Inserção confiável sem regex
        $insertPoint = strrpos($original, $mainFooter);
        if ($insertPoint !== false) {
            $before = substr($original, 0, $insertPoint);
            $after = substr($original, $insertPoint);
            $original = $before . "\n{$content}\n" . $after;
        }

        // Localiza início do grupo principal
        $start = strpos($original, $mainHeader);
        $end = strpos($original, $mainFooter, $start);

        if ($start !== false && $end !== false) {
            // Encontra a posição exata para inserir antes do fechamento
            $insertionPoint = $end;

            // Divide o conteúdo original
            $before = substr($original, 0, $insertionPoint);
            $after = substr($original, $insertionPoint);

            // Insere o subgrupo antes do fechamento
            $updated = $before . "\n" . $subgroupRoutes . "\n" . $after;

            file_put_contents($routeFile, $updated);
            return;
        }

        // 3. Salva o arquivo
        file_put_contents($fileRoute, $original);
        CLI::write("Rota Secundária adicionada com sucesso em Modules/{$module}/Config/Routes.php", 'green');
    }

    public static function createRoute(string $modulo, string $submodulo): void {


        $modulo = ucfirst($modulo);
        $submodulo = ucfirst($submodulo);
        $groupName = strtolower($modulo);
        $subGroup = strtolower($submodulo);
        $namespace = "App\\Modules\\{$modulo}\\Controllers";
        $controller = "{$submodulo}Controller";
        $routeFile = APPPATH . "Modules/{$modulo}/Config/Routes.php";

        // Se o arquivo ainda não existir, cria o grupo completo
        if (!file_exists($routeFile)) {
            ModuleHelper::CreateRoute($modulo);
        }

        // Bloco do subgrupo a ser inserido
        // 1.Traduz o conteudo
        $templateFile = __DIR__ . '/../Templates/Routes/SubRoutes.tpl';
        $subgroupBlock = TemplateHelper::generateContentFromTemplate($templateFile,
                [
                    'Module' => ucfirst($submodulo),
                    'module' => strtolower($submodulo),
                ]
        );


        $original = file_get_contents($routeFile);
// dd($routeFile,$original, "group('{$subGroup}'");
        // Evita duplicação: se subgrupo já estiver presente, nada é feito
        if (str_contains($original, "group('{$subGroup}'")) {
            CLI::write("🔁 Subgrupo '{$subGroup}' já existe no módulo '{$modulo}'. Ignorando adição.", 'yellow');
            return;
        }

        // Localiza o fechamento do grupo principal via contagem de chaves
        $groupStart = strpos($original, "\$routes->group('{$groupName}'");
        $braceCount = 0;
        $insertPos = null;
        $length = strlen($original);

        for ($i = $groupStart; $i < $length; $i++) {
            if ($original[$i] === '{') {
                $braceCount++;
            } elseif ($original[$i] === '}') {
                $braceCount--;
                if ($braceCount === 0) {
                    $insertPos = $i;
                    break;
                }
            }
        }

        if ($insertPos === null) {
            CLI::error("❌ Erro: Não foi possível localizar fechamento do grupo de rotas principal do módulo '{$modulo}'.");
            return;
        }

        // Insere o subgrupo antes da chave de fechamento
        $before = substr($original, 0, $insertPos);
        $after = substr($original, $insertPos);

        $updated = $before . "\n{$subgroupBlock}\n" . $after;

        file_put_contents($routeFile, $updated);
        CLI::write("✅ Subrotas adicionadas ao módulo '{$modulo}': '{$subGroup}'", 'green');
    }
}
