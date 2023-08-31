<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('faqs')->insert([
            [
                'slug' => uniqid(uniqid()),
                'question' => 'How to change the language',
                'answer' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book.',
                'status' => '1',
                'created_at' => '2020-02-17 15:45:47',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ], [
                'slug' => uniqid(uniqid()),
                'question' => 'How to restore your chat history',
                'answer' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book.',
                'status' => '1',
                'created_at' => '2020-02-17 15:51:16',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ]
        ]);
    }
}
