<?php

use Illuminate\Database\Seeder;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('users')->delete();
        DB::table('users')->insert(
                        array(
                                array(
                                        'name' => 'Admin',
                                        'email' => 'admin@example.com',
                                        'password' => Hash::make('12345678'),
                                        'role_id'  => 1
                                ),
                                array(
										 'name' => 'Sale Manger',
                                        'email' => 'sales@manager.com',
                                        'password' => Hash::make('12345678'),
                                        'role_id'  => 2
                                ),
                        ));
    }
}
