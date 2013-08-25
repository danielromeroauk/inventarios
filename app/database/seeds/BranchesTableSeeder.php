<?php

class BranchesTableSeeder extends Seeder {

	public function run()
	{
		// DB::table('branches')->truncate();
		$branches = array(
			array(
				'name' => 'Contruimportados',
				'comments' => 'Sede principal',
			),
			array(
				'name' => 'Dubai',
				'comments' => 'Sucursal',
			),
			array(
				'name' => 'Bodega',
				'comments' => 'Bodega de la carrera 18',
			)
		);

		DB::table('branches')->insert($branches);

		$this->command->info('branches table seeded.');
	}

}