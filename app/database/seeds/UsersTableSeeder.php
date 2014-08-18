<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		// DB::table('users')->truncate();
		$users = array(
			array(
				'name' => 'Daniel Romero Gelvez',
				'email' => 'danielromeroauk@gmail.com',
				'password' => Hash::make('123')
			)
		);

		DB::table('users')->insert($users);

		$this->command->info('users table seeded.');
	}

}