<?php

class BranchesTableSeeder extends Seeder {

	public function run()
	{
		// DB::table('branches')->truncate();
		$branches = array(
			array(
				'name' => 'PRINCIPAL',
				'comments' => 'Sede principal',
			)
		);

		DB::table('branches')->insert($branches);

		$this->command->info('branches table seeded.');
	}

}