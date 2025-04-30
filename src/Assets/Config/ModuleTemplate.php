<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ModuleTemplate extends BaseConfig {

    // Sobrepoe o namespace criado pelo pacote
    // Utilizar '\\' e não acrescentar em "\\" no final
   
    // public string $namespace  = 'App\\Modules';
    
    public $preset = 'Default'; //'Bootstrap'; // 👈 O desenvolvedor escolhe aqui
    
    public $templates = [
        // 'Default' => APPPATH . 'Templates/Default/',
        // 'Bootstrap' => APPPATH . 'Templates/Bootstrap/',
        // 'Tailwind' => APPPATH . 'Templates/Tailwind/',
        // 'AdminLTE' => APPPATH . 'Templates/AdminLTE/',
    ];
    
    
    // Exemplo de views para sobrepor 
    public $templatesView = [
        'viewIndex' => APPPATH . 'Templates/Custom/index.tpl',
        'viewCreate' => APPPATH . 'Templates/Custom/create.tpl',
        'viewEdit' => APPPATH . 'Templates/Custom/edit.tpl',
        'viewShow' => APPPATH . 'Templates/Custom/show.tpl',
        'controllerEntity' => APPPATH . 'Templates/Custom/ControllerEntity.tpl',
        'controllerModule' => APPPATH . 'Templates/Custom/ControllerModule.tpl',
            // outros se necessário
    ];    
}
