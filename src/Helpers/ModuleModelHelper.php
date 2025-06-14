<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\ModuleTableUtils;

class ModuleModelHelper {

    // ROUTES
    public static function CreateModel(string $module, string $tableName, bool $tableExists = true) {
        $className = ucfirst($tableName) . 'Model';
        $fieldsArray = ModuleTableUtils::getFieldsFromTable($tableName);
        $fields = "'" . implode("', '", $fieldsArray) . "'";
        $validationRulesIdent = "''";
        
        if ($tableExists && ModuleTableUtils::tableExists($tableName)) {
            $validationRulesArray = ModuleTableUtils::getValidationRulesFromTable($tableName);

            // Gera a string PHP formatada
            $validationRules = var_export($validationRulesArray, true);
            $validationRulesIdent = preg_replace('/^/m', '    ', $validationRules); // Identação bonita
        }
        $templatePath = __DIR__ . '/../Templates/Model.tpl';
        $content = TemplateHelper::generateContentFromTemplate(
                $templatePath,
                [
                    'Module' => $module,
                    'ClassName' => $className,
                    'table' => $tableName,
                    'fields' => $fields,
                    'validationRules' => $validationRulesIdent,
                ]
        );

        $filePath = TemplateHelper::ModuleCreateFolder($module, 'Models');
        $fileName = "{$className}.php";

        file_put_contents($filePath . $fileName, $content);
        CLI::write("Model criado: Modules/{$module}/Models/{$className}.php", 'green');
    }
}
