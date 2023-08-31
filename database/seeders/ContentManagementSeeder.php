<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('content_management')->insert([
            [
                'title' => 'Terms Condition',
                'slug' => 'terms-condition',
                'content' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book.',
                'status' => '1',
                'created_at' => '2020-02-11 15:01:25',
                'updated_at' => NULL,
                'deleted_at' => NULL
            ],[
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book.',
                'status' => '1',
                'created_at' => '2020-02-11 15:01:25',
                'updated_at' => NULL,
                'deleted_at' => NULL
            ],[
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book.',
                'status' => '1',
                'created_at' => '2020-02-11 15:01:25',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ],[
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book.',
                'status' => '1',
                'created_at' => '2020-02-11 15:01:25',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ]
        ]);

    }
}
