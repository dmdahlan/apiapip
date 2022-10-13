<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migratemahasiswa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nim'              => ['type' => 'varchar', 'constraint' => 10, 'unique' => true],
            'nama'             => ['type' => 'varchar', 'constraint' => 225, 'null' => true],
            'path_foto'        => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'datetime', 'null' => true],
            'updated_at'       => ['type' => 'datetime', 'null' => true],
            'deleted_at'       => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mahasiswa');
    }
    public function down()
    {
        $this->forge->dropTable('mahasiswa');
    }
}
