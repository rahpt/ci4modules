<?php

/**
 * Spark command: module:setup
 *
 * Exemplo de uso:
 *   spark module:setup
 *
 *   Configura o projeto CI4 para utilizar modules, adicionando um construtor 
 *   que executa autoload dos módulos
 * 
 *   Atualiza App/Config/Autoload.php 
 *   Criando um arquivo de Backup Autoload.php.bkp
 * 
 *  App\Config
 *      ModuleTemplate.php
 *          $namespace = 'App\Module';
 */

namespace Rahpt\Ci4Modules\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Rahpt\Ci4Modules\Helpers\TemplateHelper;

class ModuleSetup extends BaseCommand {

    protected $group = 'Modules';
    protected $name = 'module:setup';
    protected $description = 'Insere o construtor que faz autoload dos módulos';
    private string $marker = 'modules-autoload';

    public function run(array $params) {
        $autoload = APPPATH . 'Config/Autoload.php';
        $templateFile = __DIR__ . '/../Templates/AutoloadConstructor.tpl';

        $config = config('ModuleExtend');
        $namespace = $config->namespace ?? 'App\Modules';

        // 1. validações
        if (!is_file($autoload)) {
            CLI::error('Autoload.php não encontrado.');
            return;
        }
        if (str_contains(file_get_contents($autoload), $this->marker)) {
            CLI::write('✔ Autoload já contém o scanner; nada a fazer.', 'green');
            return;
        }

        // Module Config Override
        copy(__DIR__ . '/../Assets/Config/ModuleTemplate.php',
                APPPATH . 'Config/ModuleTemplate.php');

        // Bkp Autload Atual
        $backup = $autoload . '.bak';
        copy($autoload, $backup);

        // 2. lê arquivos
        $content = TemplateHelper::generateContentFromTemplate(
                $templateFile,
                ['namespace' => $namespace]
        );

        // 3. insere antes da última chave da classe
        $code = file_get_contents($autoload);
        $patched = preg_replace('/}\s*$/', $content . "\n}", $code, 1);
        file_put_contents($autoload, $patched);

        CLI::write('✔ Construtor injetado em Autoload.php', 'green');
        CLI::write("Backup criado em Autoload.php.bak", 'yellow');
        CLI::write("Pronto! Qualquer pasta em {$namespace}/* será carregada automaticamente.");
    }
}
