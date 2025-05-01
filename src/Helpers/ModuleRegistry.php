<?php

namespace Rahpt\Ci4Modules\Helpers;

use Exception;
use JsonException;

/**
 * Exemplo de uso:
 * 
 *    // 1) preparar dados para o registry
 *   $module = 'Test';
 * 
 *   $data = [
 *       'active'      => true,
 *       'path'        => "app/Modules/{$module}",
 *       'routePrefix' => $routePrefix ?? strtolower($module),
 *       'version'     => '0.1.0',
 *       'createdAt'   => date(DATE_ATOM),
 *   ];
 *
 *   // 2) gravar/actualizar modules.json
 *   ModuleRegistry::put($module, $data);
 * 
 * {
 * "Test":  {
 *   "active"      : true,
 *   "path"        : "app/Modules/Test",
 *   "routePrefix" : "test",
 *   "version"     : "0.1.0",
 *   "createdAt"   : "2025-05-01T00:12:49+00:00"
 * },
 */

/**
 * Exemplo rápido num Service:
<?php
namespace Config;

use CodeIgniter\Config\BaseService;
use MeuVendor\ModuleTools\Helpers\ModuleRegistry;

class Services extends BaseService
{
    public static function modules(bool $getShared = true): array
    {
        return ModuleRegistry::all();
    }
}
?>

Depois, em qualquer parte do código:

<?php
foreach (service('modules') as $name => $conf) {
    if ($conf['active']) {
        // … registrar rotas próprias, carregar configs, etc.
    }
}
?>
 */

class ModuleRegistry {

    private const FILE = WRITEPATH . 'modules.json';

    /**
     * Load modules.json
     * 
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array {
        if (!is_file(self::FILE)) {
            return [];
        }
        return json_decode(file_get_contents(self::FILE), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Adiciona ou actualiza um módulo
     *
     * @param string $name  ex.: 'Blog'
     * @param array  $data  ex.: ['active' => true, …]
     * @return void
     * @throws JsonException
     * @throws Exception
     */
    public static function put(string $name, array $data): void {
        $all = self::all();
        $all[$name] = array_merge($all[$name] ?? [], $data);

        $json = json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new JsonException('Falha ao codificar modules.json');
        }

        // flock para evitar corrupção em requests concorrentes
        $fp = fopen(self::FILE, 'cb');
        if (!$fp || !flock($fp, LOCK_EX)) {
            throw new Exception('Não foi possível bloquear modules.json');
        }
        ftruncate($fp, 0);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    protected static function toggleState(string $name, bool $newState) {
        $module = ucfirst($name);
        $all = ModuleRegistry::all();
        $all[$name]['active'] = $newState;
        ModuleRegistry::put($module, $all[$module]);
    }

    /**
     * Ativa um módulo
     * 
     * @param string $name
     */
    public static function activate(string $name) {
        ModuleRegistry::toggleState($name, true);
    }

    /**
     * Desativa um módulo
     * 
     * @param string $name
     */
    public static function deactivate(string $name) {
        ModuleRegistry::toggleState($name, false);
    }

    
    }
