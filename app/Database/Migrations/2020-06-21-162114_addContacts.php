<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContacts extends Migration
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
			'userId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 50,
				'unique'			=> true,
			],
			'displayName' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'null'				=> true
			],
			'language' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 10,
				'default'			=> 'TH',
			],
			'profile_id' => [
				'type'				=> 'INT',
				'constraint'		=> 10,
				'unsigned'			=> true,
				'default'			=> null,
				'null'				=> true
			],
			'uniqueId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'unique'			=> true,
			],
			'following' => [
				'type'				=> 'TINYINT',
				'constraint'		=> 1,
				'default'			=> 1,
				'unsigned'			=> true,
			],
			'banned' => [
				'type'				=> 'TINYINT',
				'constraint'		=> 1,
				'default'			=> 0,
				'unsigned'			=> true,
			],
			'banned_reason' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 250,
				'default'			=> null,
				'null'				=> true,
			],
			'created_datetime' => [
				'type'				=> 'DATETIME',
				'null'				=> true,
			],
			'follow_datetime' => [
				'type'				=> 'DATETIME',
				'null'				=> true,
			],
			'unfollow_datetime' => [
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
		->addForeignKey('profile_id','profiles','id','CASCADE','CASCADE')
		->createTable('contacts');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('contacts');
	}
}
