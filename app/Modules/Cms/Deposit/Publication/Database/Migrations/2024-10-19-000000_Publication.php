<?php

namespace DepositPublication\Database\Migrations;

use CodeIgniter\Database\Migration;

class Publication extends Migration
{
	public function up()
	{
		// t_publication
		$this->forge->dropTable('t_publication', true);
		$this->forge->addField([
			'id' 					=> ['type' => 'MEDIUMINT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true,],
			'document' 				=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'title'					=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'slug' 					=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'category' 				=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'author' 				=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'publisher'				=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'city'					=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'content'				=> ['type' => 'LONGTEXT', 'null' => true,],
			'edition'				=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'publication_year'		=> ['type' => 'YEAR', 'constraint' => '4', 'null' => true,],
			'worksheet' 			=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'meta_title'			=> ['type' => 'LONGTEXT', 'null' => true,],
			'meta_keywords'			=> ['type' => 'LONGTEXT', 'null' => true,],
			'meta_description'		=> ['type' => 'LONGTEXT', 'null' => true,],
			'open'					=> ['type' => 'BIGINT', 'constraint' => 20, 'null' => true,],
			'download'				=> ['type' => 'BIGINT', 'constraint' => 20, 'null' => true,],
			'channel' 				=> ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true,],
			'created_by' 			=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'updated_by' 			=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'created_at' 			=> ['type' => 'DATETIME', 'null' => true,],
			'updated_at' 			=> ['type' => 'DATETIME', 'null' => true,],
			'deleted_at' 			=> ['type' => 'DATETIME', 'null' => true,],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('t_publication');
		// t_publication
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('t_publication', true);
	}
}
