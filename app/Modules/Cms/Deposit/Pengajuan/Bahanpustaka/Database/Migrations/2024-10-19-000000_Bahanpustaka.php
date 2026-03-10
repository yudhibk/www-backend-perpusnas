<?php

namespace DepositPengajuanBahanpustaka\Database\Migrations;

use CodeIgniter\Database\Migration;

class Bahanpustaka extends Migration
{
	public function up()
	{
		// t_deposit_pengajuan_bahan_pustaka
		$this->forge->dropTable('t_deposit_pengajuan_bahan_pustaka', true);
		$this->forge->addField([
			'id' 				=> ['type' => 'MEDIUMINT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true,],
			'title' 			=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'slug' 				=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'meta_title' 		=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'meta_keywords' 	=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'meta_description' 	=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'content' 			=> ['type' => 'TEXT', 'null' => true,],
			'file'				=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'created_by' 		=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'updated_by' 		=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'created_at' 		=> ['type' => 'DATETIME', 'null' => true,],
			'updated_at' 		=> ['type' => 'DATETIME', 'null' => true,],
			'deleted_at' 		=> ['type' => 'DATETIME', 'null' => true,],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('t_deposit_pengajuan_bahan_pustaka');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('t_deposit_pengajuan_bahan_pustaka', true);
	}
}
