<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pegawai extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'class' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'division' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'active' => [
                'type'       => 'INT',
                'null' => false,
            ],
            'file' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_pegawai');
    }

    public function down()
    {
        $this->forge->dropTable('t_pegawai');
    }
}
