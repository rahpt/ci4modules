<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers;
use Config\Database;

class ModuleMigrationHelper {

    public static function getContentEmpty(string $module, string $tableName): string {
        $forgeFields = [
            'id' => ['type' => 'INT', 'null' => false, 'unsigned' => true, 'auto_increment' => true],
            'nome' => ['type' => 'VARCHAR', 'null' => false, 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $fields = ModuleTableUtils::formatArrayExport($forgeFields);
        $keys = ModuleTableUtils::formatPrimaryKeys(['id']);

        $templatePath = __DIR__ . '/../Templates/Migration.tpl';
        $content = TemplateHelper::generateContentFromTemplate(
                $templatePath,
                [
                    'ModuleName' => ucfirst($module),
                    'TableName' => strtolower($tableName),
                    'Fields' => $fields,
                    'PrimaryKeys' => $keys,
                ]
        );
        return $content;
    }

    public static function getContentFields(string $module, string $tableName): string {
        // ExportFieldDefs
        $db = Database::connect();        
        $fieldData = $db->getFieldData($tableName);
        $primaryKeys = [];
        $forgeFields = [];

        foreach ($fieldData as $field) {
            $type = strtoupper($field->type);
            $definition = [
                'type' => $type,
                'null' => $field->nullable,
            ];

            if ($field->max_length) {
                $definition['constraint'] = $field->max_length;
            }

            if ($field->primary_key == 1) {
                $definition['unsigned'] = true;
                $definition['auto_increment'] = true;
                $primaryKeys[] = $field->name;
            }

            $forgeFields[$field->name] = $definition;
        }
        $fields = ModuleTableUtils::formatArrayExport($forgeFields);
        $keys = ModuleTableUtils::formatPrimaryKeys($primaryKeys);

        $templatePath = __DIR__ . '/../Templates/Migration.tpl';
        $content= TemplateHelper::generateContentFromTemplate(
                $templatePath,
                [
                    'ModuleName' => ucfirst($module),
                    'TableName' => strtolower($tableName),
                    'Fields' => $fields,
                    'PrimaryKeys' => $keys,
                ]
        );
        return $content;
    }

    // Migration
    public static function CreateMigration(string $module, string $tableName):bool {

        $db = Database::connect();
        $tableExists = false;

        // CheckTableExists
        if (!ModuleTableUtils::tableExists($tableName)) {
            CLI::error("A tabela '{$tableName}' não existe.");
            CLI::write("Utilizando template vazio.", 'yellow');

            $template = ModuleMigrationHelper::getContentEmpty($module, $tableName);
        } else {
            $template = ModuleMigrationHelper::getContentFields($module, $tableName);
            $tableExists = true;
        }

        $filePath = TemplateHelper::ModuleCreateFolder($module, 'Database\Migrations');
        $timestamp = date('Y-m-d-His');
        $fileName = "{$timestamp}_create_{$tableName}_table.php";

        file_put_contents($filePath . $fileName, $template);
        CLI::write("Migration criada: Modules/{$module}/Database/Migrations/{$fileName}", 'green');
        return $tableExists;
    }
}
