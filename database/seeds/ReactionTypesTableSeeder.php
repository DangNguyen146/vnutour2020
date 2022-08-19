<?php

use Illuminate\Database\Seeder;

class ReactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reactionTypes = [
            'like' => [
                'image' => '/assets/images/reaction/like.png',
                'css_class' => 'text-primary',
                'title' => 'thích'
            ],
            'love' => [
                'image' => '/assets/images/reaction/love.png',
                'css_class' => 'text-pink',
                'title' => 'yêu thích'
            ],
            'care' => [
                'image' => '/assets/images/reaction/care.png',
                'css_class' => 'text-warning',
                'title' => 'thương thương'
            ],
            'haha' => [
                'image' => '/assets/images/reaction/haha.png',
                'css_class' => 'text-warning',
                'title' => 'haha'
            ],
            'wow' => [
                'image' => '/assets/images/reaction/wow.png',
                'css_class' => 'text-warning',
                'title' => 'ngạc nhiên'
            ],
            'sad' => [
                'image' => '/assets/images/reaction/sad.png',
                'css_class' => 'text-warning',
                'title' => 'buồn'
            ],
            'angry' => [
                'image' => '/assets/images/reaction/angry.png',
                'css_class' => 'text-danger',
                'title' => 'phẫn nộ'
            ]
        ];

        $index = 1;
        foreach ($reactionTypes as $name => $reactionType) {
            $_reactionType = App\ReactionType::find($name);
            if (!$_reactionType){
                $_reactionType = new App\ReactionType();
                $_reactionType->id  = $name;
            }

            foreach ($reactionType as $key => $value) {
                $_reactionType[$key] = $value;
            }
            $_reactionType->order = $index;
            $_reactionType->save();
            $index ++;
        }
    }
}
