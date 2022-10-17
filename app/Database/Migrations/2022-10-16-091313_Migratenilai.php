<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migratenilai extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_nilai'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kelas'              => ['type' => 'VARCHAR', 'constraint' => 50],
            'pelajaran'          => ['type' => 'VARCHAR', 'constraint' => 225],
            'nilai'              => ['type' => 'double'],
            'foto_nilai'         => ['type' => 'VARCHAR', 'constraint' => 225, 'null' => true],
            'created_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'updated_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'deleted_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_nilai', true);
        $this->forge->addForeignKey('created_id', 'users', 'id', '', '');
        $this->forge->addForeignKey('updated_id', 'users', 'id', '', '');
        $this->forge->addForeignKey('deleted_id', 'users', 'id', '', '');
        $this->forge->createTable('nilai_pelajaran');
    }
    public function down()
    {
        $this->forge->dropForeignKey('nilai_pelajaran', 'nilai_pelajaran_created_id_foreign');
        $this->forge->dropForeignKey('nilai_pelajaran', 'nilai_pelajaran_updated_id_foreign');
        $this->forge->dropForeignKey('nilai_pelajaran', 'nilai_pelajaran_deleted_id_foreign');
        $this->forge->dropTable('nilai_pelajaran');
    }
}
