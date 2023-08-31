<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name     = $this->faker->name();
        $email    = $this->faker->unique()->safeEmail();
        $username = Str::slug($name);
        return [
            'user_group_id' => 3,
            'user_type' => 'user',
            'name' =>  $name,
            'business_name' =>  $name,
            'business_category_id' => $this->categoryId('business'),
            'promote_category_id'  => $this->categoryId('promote'),
            'username' => $username,
            'slug' =>$username,
            'work_email' => $email,
            'email' => $email,
            'mobile_no'  => '1-' . $this->faker->numerify('##########'),
            'password'   => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'image_url'  => $this->faker->image('public/storage/users',640,480, null, false),
            'country'    => 'united state',
            'city'       => 'Helotes',
            'state'      => 'TX',
            'zipcode'    => '78023',
            'address'    => 'Winter Haven, FL',
            'open_time'  => '09:00:00',
            'close_time' => '18:00:00',
            'about'      => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            'product_service' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
            'site_url'   => 'https://www.lipsum.com/',
            'is_email_verify' => '1',
            'email_verify_at' => now(),
            'account_approved' => '1',
            'invite_code'       => rand(11,99) . uniqid(),
            'remember_token'    => Str::random(10),
            'created_at' => now(),
        ];
    }

    public function categoryId($type)
    {
        $record = \DB::table('categories')->where('type',$type)->first();
        return $record->id;
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
