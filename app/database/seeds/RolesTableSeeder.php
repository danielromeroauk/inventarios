<?php

class RolesTableSeeder extends Seeder {

	public function run()
	{
		// DB::table('roles')->truncate();
		$roles = array(
			array(
				'name' => 'administrador',
				'user_id' => 1,
				'branch_id' => 1
			)
		);

		DB::table('roles')->insert($roles);

		$this->command->info('roles table seeded.');
	}

}