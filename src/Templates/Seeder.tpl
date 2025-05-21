<?php

namespace App\Modules\__Module__\Database\Seeds;

use CodeIgniter\Database\Seeder;

class __TableName__ extends Seeder
{
    public function run()
    {
        $fields = __Fields__;

        $data = __Data__;

        foreach ($data as $row) {
            $insert = array_combine($fields, $row);
            $this->db->table('__TableName__')->insert($insert);
        }
    }
}