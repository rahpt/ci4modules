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
use Rahpt\Ci4Modules\Helpers\ModuleSetupHelper;

class ModuleSetup extends BaseCommand {

    protected $group = 'Modules';
    protected $name = 'module:setup';
    protected $description = 'Insere o construtor que faz autoload dos módulos';
    protected $options = [
        '-d' => 'Reverte a inclusão do autoload dos módulos',
    ];

    public function run(array $params) {
        $namespace = ModuleSetupHelper::getNamespace();
        if (CLI::getOption('d')) {
            if (ModuleSetupHelper::UnSetup()) {
                CLI::write("Gestor de módulos restaurado com sucesso.");
            } 
            return;
        }
        if (ModuleSetupHelper::Setup($namespace)) {
            CLI::write("Pronto! Qualquer pasta em {$namespace} será carregada automaticamente.");
            return;
        }
        CLI::error("Erro ao tentar aplicar patchs.");
    }
}
