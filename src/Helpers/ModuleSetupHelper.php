<?php

namespace Rahpt\Ci4Modules\Helpers;

use CodeIgniter\CLI\CLI;

/**
 * Description of ModuleSetup
 *
 * @author jose.proenca
 */
class ModuleSetupHelper {

    private const AUTOLOAD_MARKER = 'modules-autoload';
    private const AUTOLOAD_FILE = APPPATH . 'Config/Autoload.php';
    private const AUTOLOAD_TEMPLATE = __DIR__ . '/../Templates/AutoloadConstructor.tpl';

    public static function getNamespace(): string {
        $config = config('ModuleExtend');
        $namespace = $config->namespace ?? 'App\Modules';
        return $namespace;
    }

    public static function Setup(string $namespace = '') {

        if ($namespace == '') {
            $namespace = self::getNamespace();
        }
        // 1. validações
        if (!is_file(self::AUTOLOAD_FILE)) {
            CLI::error('Autoload.php não encontrado.');
            return;
        }

        if (self::isPatched()) {
            CLI::write('✔ Autoload já contém o scanner. Nada a fazer.', 'green');
            return;
        }

        // Module Config Override
        copy(__DIR__ . '/../Assets/Config/ModuleTemplate.php',
                APPPATH . 'Config/ModuleTemplate.php');

        // Bkp Autload Atual
        $backup = self::AUTOLOAD_FILE . '.bak';
        copy(self::AUTOLOAD_FILE, $backup);
        CLI::write("Backup criado em Autoload.php.bak", 'yellow');

        // 2. lê arquivos
        $content = TemplateHelper::generateContentFromTemplate(
                self::AUTOLOAD_TEMPLATE, [
            'namespace' => $namespace,
            'autoload_marker' => self::AUTOLOAD_MARKER]
        );

        // 3. insere antes da última chave da classe
        $code = file_get_contents(self::AUTOLOAD_FILE);
        $patched = preg_replace('/}\s*$/', $content . "\n}", $code, 1);
        file_put_contents(self::AUTOLOAD_FILE, $patched);
        CLI::write('✔ Construtor injetado em Autoload.php', 'green');
        return true;
    }

    public static function UnSetup() {
        if (!self::isPatched()) {
            CLI::write('✔ Autoload não contém o scanner. Nada a fazer.', 'green');
            return;
        }

        $backup = self::AUTOLOAD_FILE . '.bak';
        if (!is_file($backup)) {
            CLI::error('Arquivo de Backup não encontrado.');
            return;
        }

        // Bkp Autload Atual
        $backup2 = self::AUTOLOAD_FILE . '.bak2';
        copy(self::AUTOLOAD_FILE, $backup2);

        // recupera Backup
        copy($backup, self::AUTOLOAD_FILE);
        // Module Config Override
        if (is_file(APPPATH . 'Config/ModuleTemplate.php')) {
            unlink(APPPATH . 'Config/ModuleTemplate.php');
            unlink($backup);
            copy($backup2, $backup);
            unlink($backup2);
        }
        return true;
    }

    public static function isPatched(): bool {
        return str_contains(file_get_contents(self::AUTOLOAD_FILE), self::AUTOLOAD_MARKER);
    }
}
