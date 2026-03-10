<?php

namespace DepositLaporanpengadaan\Database\Migrations;

use CodeIgniter\Database\Migration;

class Laporanpengadaan extends Migration
{
	public function up()
	{
		// t_deposit_laporan_pengadaan
		$this->forge->dropTable('t_deposit_laporan_pengadaan', true);
		$this->forge->addField([
			'id' 			=> ['type' => 'MEDIUMINT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true,],
			'name' 			=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
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
		$this->forge->createTable('t_deposit_laporan_pengadaan');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('t_deposit_laporan_pengadaan', true);
	}
}
