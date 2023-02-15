<?php

use Illuminate\Database\Seeder;

class HomepageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('homepage')->delete();
        DB::table('homepage')->insert(
			array(
					array('key' => 'story_title','type' => 'text','label' => 'Story Title','value' => "<span>Discover</span>Our Story"),
					array('key' => 'story_desc','type' => 'textarea','label' => 'Story Description','value' => "accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est."),
					array('key' => 'menu_title','type' => 'text','label' => 'Menu Title','value' => "<span>Discover</span>Our Menu"),
					array('key' => 'menu_desc','type' => 'textarea','label' => 'Menu Description','value' => "accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est."),
					array('key' => 'img_title1','type' => 'text','label' => 'Image Title 1','value' => "<h2><span>We Are Sharing</span></h2>                    <h1>delicious treats</h1>"),
					array('key' => 'img_title2','type' => 'text','label' => 'Image Title 2','value' => "<h2><span>The Perfect</span></h2>                    <h1>Blend</h1>"),
					array('key' => 'category','type' => '','label' => 'Home Categories','value' => ""),
			));
    }
}
