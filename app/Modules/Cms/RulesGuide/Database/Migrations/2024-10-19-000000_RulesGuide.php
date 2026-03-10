<?php

namespace RulesGuide\Database\Migrations;

use CodeIgniter\Database\Migration;

class RulesGuide extends Migration
{
	public function up()
	{
		// rules_guide
		$this->forge->dropTable('rules_guide', true);
		$this->forge->addField([
			'id' 					=> ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true,],
			'document' 				=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'title'					=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'slug' 					=> ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true,],
			'category' 				=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'content'				=> ['type' => 'LONGTEXT', 'null' => true,],
			'meta_title'			=> ['type' => 'LONGTEXT', 'null' => true,],
			'meta_keywords'			=> ['type' => 'LONGTEXT', 'null' => true,],
			'meta_description'		=> ['type' => 'LONGTEXT', 'null' => true,],
			'open'					=> ['type' => 'BIGINT', 'constraint' => 20, 'null' => true,],
			'created_by' 			=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'updated_by' 			=> ['type' => 'INT', 'constraint' => 11, 'null' => true,],
			'created_at' 			=> ['type' => 'DATETIME', 'null' => true,],
			'updated_at' 			=> ['type' => 'DATETIME', 'null' => true,],
			'deleted_at' 			=> ['type' => 'DATETIME', 'null' => true,],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('rules_guide');
		// rules_guide
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('rules_guide', true);
	}
}
