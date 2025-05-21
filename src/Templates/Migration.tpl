<?php

namespace App\Modules\__ModuleName__\Database\Migrations;

use CodeIgniter\Database\Migration;

class Create__TableName__Table extends Migration
{
    public function up()
    {
        $this->forge->addField(
__Fields__);
__PrimaryKeys__        $this->forge->createTable('__TableName__');
    }

    public function down()
    {
        $this->forge->dropTable('__TableName__');
    }
}
