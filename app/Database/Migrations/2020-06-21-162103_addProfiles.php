<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfiles extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type'				=> 'INT',
				'constraint' 		=> 10,
				'unsigned'			=> true,
				'auto_increment'	=> true
			],
			'firstname' => [
				'type'				=> 'VARCHAR',
				'constraint' 		=> 100,
				'null'				=> true
			],
			'lastname' => [
				'type'				=> 'VARCHAR',
				'constraint' 		=> 100,
				'null'				=> true
			],
			'email' => [
				'type'				=> 'VARCHAR',
				'constraint' 		=> 100,
				'null'				=> true
			],
			'password' => [
				'type'				=> 'VARCHAR',
				'constraint' 		=> 100,
				'null'				=> true
			],
			'created_datetime' => [
				'type'				=> 'DATETIME',
				'null'				=> true
			],
			'updated_datetime' => [
				'type'				=> 'DATETIME',
				'null'				=> true
			]
		])
		->addPrimaryKey('id')
		->addUniqueKey('email')
		->createTable('profiles');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('profiles');
	}
}
