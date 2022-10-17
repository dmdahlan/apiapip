<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migrateusers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'            => ['type' => 'varchar', 'constraint' => 100, 'unique' => true],
            'username'         => ['type' => 'varchar', 'constraint' => 30, 'unique' => true],
            'nis'              => ['type' => 'varchar', 'constraint' => 30, 'unique' => true],
            'level'            => ['type' => 'varchar', 'constraint' => 30],
            'password_hash'    => ['type' => 'varchar', 'constraint' => 255],
            'foto'             => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'active'           => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            // 'reset_hash'       => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            // 'reset_at'         => ['type' => 'datetime', 'null' => true],
            // 'reset_expires'    => ['type' => 'datetime', 'null' => true],
            // 'activate_hash'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            // 'status'           => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            // 'status_message'   => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            // 'active'           => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            // 'force_pass_reset' => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            'created_at'       => ['type' => 'datetime', 'null' => true],
            'updated_at'       => ['type' => 'datetime', 'null' => true],
            'deleted_at'       => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }
    public function down()
    {
        $this->forge->dropTable('users');
    }
}
