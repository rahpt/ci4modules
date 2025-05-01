<?php

namespace Rahpt\Ci4Modules\Config;

use CodeIgniter\Config\BaseService;
use Rahpt\Ci4Modules\Helpers\ModuleRegistry;

class Services extends BaseService {

    /**
     * Retorna (ou partilha) o array de módulos registado em writable/modules.json
     */
    public static function modules(bool $getShared = true): array {
        if ($getShared) {
            return static::getSharedInstance('modules');
        }

        return ModuleRegistry::all();
    }
}
