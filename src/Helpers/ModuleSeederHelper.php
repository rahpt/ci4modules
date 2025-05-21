<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\ModuleTableUtils;
use Config\Database;

class ModuleSeederHelper {

    // Migration
    public static function CreateSeeder(string $module, string $tableName) {

        $db = Database::connect();

        $fieldsArray = ModuleTableUtils::getFieldsFromTable($tableName);
        $fields = "['" . implode("', '", $fieldsArray) . "']";

        $db = Database::connect();

        if (!$db->tableExists($tableName)) {
            CLI::error("A tabela '{$tableName}' não existe.");
            return;
        }

        $builder = $db->table($tableName);
        $limit = 100;

        $result = $builder->limit($limit)->get()->getResultArray();
        if (empty($result)) {
            CLI::error("A tabela '{$tableName}' não possui dados para exportar.");
            CLI::write('O arquivo será criado sem informações!', 'yellow');
        }

        $dataExport = ModuleSeederHelper::compactDataExport($result, $fieldsArray);

        $templatePath = __DIR__ . '/../Templates/Seeder.tpl';
        
        $content = TemplateHelper::generateContentFromTemplate(
                $templatePath,
                [
                    'Module' => $module,
                    // 'ClassName' => $className,
                    'TableName' => $tableName,
                    'Fields' => $fields,
                    'Data' => $dataExport,
                ]
        );

        $filePath = TemplateHelper::ModuleCreateFolder($module, 'Database\Seeds');

        $timestamp = date('Y-m-d-His');
        $fileName = "{$timestamp}_{$tableName}.php";

        file_put_contents($filePath . $fileName, $content);
        CLI::write("Seeder criado: Modules/{$module}/Database/Seeds/{$fileName}", 'green');
    }

    public static function compactDataExport(array $data, array $fields): string {
        $export = "[\n";
        foreach ($data as $row) {
            $values = [];
            foreach ($fields as $field) {
                $values[] = var_export($row[$field] ?? null, true);
            }
            $export .= "    [" . implode(', ', $values) . "],\n";
        }
        $export .= "]";

        return $export;
    }
}
