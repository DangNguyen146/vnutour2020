<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $post = new App\Post();

        $post->post_id = "3611743408878597";
        $post->title = Str::random(10);
        $post->save();
        // $this->call(UsersTableSeeder::class);
    }
}
