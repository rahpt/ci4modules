<?php

namespace Rahpt\Ci4Modules\Helpers;

use Rahpt\Ci4Modules\Helpers\ModuleHelper;

class TemplateHelper {

    public static function formatArray(array $data): string {
        $export = var_export($data, true);
        return preg_replace('/^/m', '            ', $export);
    }

    public static function formatPrimaryKeys(array $keys): string {
        if (empty($keys)) {
            return '';
        }
        $list = implode("','", $keys);
        return "        \$this->forge->addKey('{$list}', true);\n";
    }

    public static function generateContentFromTemplate(string $templatePath, array $vars): string {

        $template = file_get_contents($templatePath);

        foreach ($vars as $key => $value) {
            $template = str_replace("__{$key}__", $value, $template);
        }

        return $template;
    }

    public static function ModuleCreateFolder(string $module, string $context = '') {
        $modulePath = ucfirst($module);
        $path = ROOTPATH . "app/Modules/{$modulePath}/";
        if ($context) {
            $path .= "{$context}/";
        }

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }

    public static function isShieldInstalled(): bool {
        return class_exists('CodeIgniter\Shield\Authentication\Authentication');
    }

    public static function getTemplatePath(string $fileName): string {
        // Caminho padrão
        $defaultPath = __DIR__ . '/../Templates/Default/' . $fileName;

        // Se ModuleTemplate não existir, retorna o Default
        if (!class_exists('Config\ModuleTemplate')) {
            return $defaultPath;
        }
        $config = config('ModuleTemplate');
        $preset = $config->preset ?? 'Default';
        $paths = $config->templates ?? [];

        if (!isset($paths[$preset])) {
            return $defaultPath;
        }

        return $paths[$preset] . $fileName;
    }

    public static function updateModulesJson($module, $entity) {
        $modules = ModuleHelper::getModules();

        if (!isset($modules[$module])) {
            $modules[$module] = [
                'active' => true,
                'icon' => 'fas fa-folder-open', // Ícone padrão
                'label' => $module,
                'entities' => [],
            ];
        }

        $modules[$module]['entities'][$entity] = [
            'active' => true,
            'label' => $entity . 's',
            'route' => '/' . strtolower($module) . '/' . strtolower($entity),
        ];

        ModuleHelper::saveModules($modules);
    }
}
