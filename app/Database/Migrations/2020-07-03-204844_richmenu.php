<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Richmenu extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' 		=> 10,
				'unsigned'			=> true,
				'auto_increment'	=> true
			],
			'richMenuId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 50,
				'unique'			=> true,
			],
			'name' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'null'				=> true,
				'unique'			=> true
			],
			'data' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 2500,
				'null'				=> true
			],
			'isDefault' => [
				'type'				=> 'TINYINT',
				'constraint'		=> 1,
				'null'				=> true
			],
			'created_datetime' => [
				'type'				=> 'DATETIME',
				'null'				=> true,
			],
			'updated_datetime' => [
				'type'				=> 'DATETIME',
				'null'				=> true,
			],
			'deleted_datetime' => [
				'type'				=> 'DATETIME',
				'null'				=> true,
			]

		])
		->addPrimaryKey('id')
		->createTable('richmenu');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('richmenu');
	}
}
