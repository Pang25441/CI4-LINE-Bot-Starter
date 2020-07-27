<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoomGroupContact extends Migration
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
			'groupId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 50,
				'unique'			=> true,
			],
			'groupName' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'null'				=> true
			],
			'status' => [
				'type'				=> 'TINYINT',
				'constraint'		=> 1,
				'default'			=> 1,
				'unsigned'			=> true,
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
		->createTable('groups');

		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' 		=> 10,
				'unsigned'			=> true,
				'auto_increment'	=> true
			],
			'uniqueId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'unique'			=> true,
			],
			'userId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 50,
			],
			'displayName' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'null'				=> true
			],
			'group_id' => [
				'type' => 'INT',
				'constraint' 		=> 10,
				'unsigned'			=> true,
				'null'				=> false
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
		->addForeignKey('group_id','groups','id','CASCADE','CASCADE')
		->addKey(['userId','group_id'],false, true)
		->createTable('group_members');

		/********************************************************** */

		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' 		=> 10,
				'unsigned'			=> true,
				'auto_increment'	=> true
			],
			'roomId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 50,
				'unique'			=> true,
			],
			'roomName' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'null'				=> true
			],
			# 1 = Active, 2 = Leave
			'status' => [
				'type'				=> 'TINYINT',
				'constraint'		=> 1,
				'default'			=> 1,
				'unsigned'			=> true,
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
		->createTable('rooms');

		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' 		=> 10,
				'unsigned'			=> true,
				'auto_increment'	=> true
			],
			'uniqueId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'unique'			=> true,
			],
			'userId' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 50,
			],
			'displayName' => [
				'type'				=> 'VARCHAR',
				'constraint'		=> 100,
				'null'				=> true
			],
			'room_id' => [
				'type' => 'INT',
				'constraint' 		=> 10,
				'unsigned'			=> true,
				'null'				=> false
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
		->addForeignKey('room_id','groups','id','CASCADE','CASCADE')
		->addKey(['userId','room_id'],false, true)
		->createTable('room_members');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('group_members');
		$this->forge->dropTable('groups');
		$this->forge->dropTable('room_members');
		$this->forge->dropTable('rooms');
	}
}
