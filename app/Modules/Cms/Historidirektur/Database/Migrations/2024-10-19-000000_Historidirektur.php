<?php

namespace Historidirektur\Database\Migrations;

use CodeIgniter\Database\Migration;

class Historidirektur extends Migration
{
	public function up()
	{
		// t_deposit_profil_histori_direktur
		$this->forge->dropTable('t_profil_histori_direktur', true);
		$this->forge->addField([
			'id' 			=> ['type' => 'MEDIUMINT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true,],
			'name' 			=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'awal_menjabat' => ['type' => 'DATETIME', 'null' => true,],
			'akhir_menjabat' => ['type' => 'DATETIME', 'null' => true,],
			'file'			=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'slug' 			=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'description' 	=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'channel' 		=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'created_by' 	=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'updated_by' 	=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'created_at' 	=> ['type' => 'DATETIME', 'null' => true,],
			'updated_at' 	=> ['type' => 'DATETIME', 'null' => true,],
			'deleted_at' 	=> ['type' => 'DATETIME', 'null' => true,],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('t_profil_histori_direktur');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('t_profil_histori_direktur', true);
	}
}
