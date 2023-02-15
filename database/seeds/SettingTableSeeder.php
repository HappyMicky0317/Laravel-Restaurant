<?php

use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();
        DB::table('settings')->insert(
			array(
					array('key' => 'title','label' => 'Site Title','value' => "Primoo Pizza"),
					array('key' => 'phone','label' => 'Phone','value' => "+923005095213"),
					array('key' => 'email','label' => 'Email','value' => "arfan67@gmail.com"),
					array('key' => 'address','label' => 'Address','value' => "3rd Floor Street 6 Gali 5"),
					array('key' => 'country','label' => 'Country','value' => "Pakistan"),
					array('key' => 'timing1','label' => 'Monday To Saturday','value' => "12PM to 12AM"),
					array('key' => 'sunday','label' => 'Sunday','value' => "Closed"),
					array('key' => 'facebook','label' => 'Facebook','value' => "https://www.facebook.com/cent040"),
					array('key' => 'twitter','label' => 'Twitter','value' => "https://www.twitter.com/cent040"),
					array('key' => 'vat','label' => 'VAT','value' => "10"),
					array('key' => 'delivery_cost','label' => 'Delivery Cost','value' => "1"),
					array('key' => 'currency','label' => 'Currency','value' => "$"),
					array('key' => 'lng','label' => 'Longitude','value' => "-73.9400"),
					array('key' => 'lat','label' => 'Latitude','value' => "40.6700"),
					array('key' => 'stripe','label' => 'Stripe Payment','value' => "yes"),
					array('key' => 'frontend','label' => 'Hide Frontend','value' => "no"),
			));
    }
}
