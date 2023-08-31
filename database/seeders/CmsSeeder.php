<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addCmsRoles();
        $this->addCmsUser();
        $this->addCmsModules();
        $this->mailTemplates();
        $this->addApplicationSettings();
        $this->cms_widget();
        $this->category();
        $this->packages();
        \App\Models\User::factory(20)->create();
    }

    public function addCmsRoles()
    {
       \DB::table('user_groups')->insert([
            [
                'title'          => 'Super Admin',
                'slug'           => Str::slug('Super Admin'),
                'type'           => 'admin',
                'is_super_admin' => '1',
                'created_at'     => Carbon::now()
            ],[
                'title'          => 'Admin',
                'slug'           => Str::slug('Admin'),
                'type'           => 'admin',
                'is_super_admin' => '0',
                'created_at'     => Carbon::now()
            ],[
                'title' => 'User',
                'slug'  => 'user',
                'type'          => 'user',
                'is_super_admin'=> '0',
                'created_at' => Carbon::now()
            ],[
                'title' => 'Vendor',
                'slug'  => 'vendor',
                'type'          => 'user',
                'is_super_admin'=> '0',
                'created_at' => Carbon::now()
            ]
        ]);
    }

    public function addCmsUser()
    {
        $role = \DB::table('user_groups')->where('title','Super Admin')->first();
        $role2 = \DB::table('user_groups')->where('title','Admin')->first();
        \DB::table('users')->insert([
            [
                'user_group_id' => $role->id,
                'user_type'   => 'admin',
                'name'        => 'RetroCube',
                'username'    => 'retrocube',
                'slug'        => 'retrocube',
                'email'       => 'admin@retrocube.com',
                'mobile_no'   => '1-8882051816',
                'password'    => Hash::make('admin@123$'),
                'is_email_verify' => '1',
                'email_verify_at' => Carbon::now(),
                'is_mobile_verify' => '1',
                'mobile_verify_at' => Carbon::now(),
                'created_at'  => Carbon::now(),
            ],
            [
                'user_group_id' => $role2->id,
                'user_type'     => 'admin',
                'name'          => '54health',
                'username'      => '54health',
                'slug'          => '54health',
                'email'         => 'admin@54health.com',
                'mobile_no'     => '1-1112223210',
                'password'      => Hash::make('Admin@123'),
                'is_email_verify'  => '1',
                'email_verify_at'  => Carbon::now(),
                'is_mobile_verify' => '1',
                'mobile_verify_at' => Carbon::now(),
                'created_at'       => Carbon::now(),
            ]
        ]);
    }

    public function addCmsModules()
    {
        $data = [
            [
                'parent_id'    => 0,
                'name'         => 'Cms Roles Management',
                'route_name'   => 'cms-roles-management.index',
                'icon'         => 'fa fa-key',
                'status'       => '1',
                'sort_order'   => 1,
                'created_at'   => Carbon::now()
            ],
            [
                'parent_id'    => 0,
                'name'         => 'Cms Users Management',
                'route_name'   => 'cms-users-management.index',
                'icon'         => 'fa fa-users',
                'status'       => '1',
                'sort_order'   => 2,
                'created_at'   => Carbon::now()
            ],
            [
                'parent_id'    => 0,
                'name'         => 'Application Setting',
                'route_name'   => 'admin.application-setting',
                'icon'         => 'fa fa-cog',
                'status'       => '1',
                'sort_order'   => 3,
                'created_at'   => Carbon::now()
            ],
            [
                'parent_id'    => 0,
                'name'         => 'Users Management',
                'route_name'   => 'user-management',
                'icon'         => 'fa fa-users',
                'status'       => '1',
                'sort_order'   => 4,
                'created_at'   => Carbon::now()
            ],[
                'parent_id'    => 4,
                'name'         => 'User',
                'route_name'   => 'app-users.index',
                'icon'         => 'fa fa-users',
                'status'       => '1',
                'sort_order'   => 1,
                'created_at'   => Carbon::now()
            ],
            [
                'parent_id'    => 4,
                'name'         => 'Vendor',
                'route_name'   => 'vendor.index',
                'icon'         => 'fa fa-users',
                'status'       => '1',
                'sort_order'   => 2,
                'created_at'   => Carbon::now()
            ],[
                'parent_id'    => 0,
                'name'         => 'Category Management',
                'route_name'   => 'category-management.index',
                'icon'         => 'fa fa-tasks',
                'status'       => '1',
                'sort_order'   => 5.0,
                'created_at'   => Carbon::now()
            ],[
                'parent_id'    => 7,
                'name'         => 'Business',
                'route_name'   => 'business-category.index',
                'icon'         => 'fa fa-tasks',
                'status'       => '1',
                'sort_order'   => 1,
                'created_at'   => Carbon::now()
            ],[
                'parent_id'    => 7,
                'name'         => 'Promote',
                'route_name'   => 'promote-category.index',
                'icon'         => 'fa fa-tasks',
                'status'       => '1',
                'sort_order'   => 2,
                'created_at'   => Carbon::now()
            ],[
                'parent_id'    => 0,
                'name'         => 'Content Management',
                'route_name'   => 'content-management.index',
                'icon'         => 'fa fa-tasks',
                'status'       => '1',
                'sort_order'   => 6,
                'created_at'   => Carbon::now()
            ],[
                'parent_id'    => 0,
                'name'         => 'FAQ`s',
                'route_name'   => 'faq.index',
                'icon'         => 'fa fa-question-circle-o',
                'status'       => '1',
                'sort_order'   => 7,
                'created_at'   => Carbon::now()
            ],
            [
                'parent_id'    => 0,
                'name'         => 'Quote Management',
                'route_name'   => 'quote.index',
                'icon'         => 'fa fa-quote-left',
                'status'       => '1',
                'sort_order'   => 7,
                'created_at'   => Carbon::now()
            ]
        ];
        \DB::table('cms_modules')->insert($data);
    }


    public function addApplicationSettings()
    {
        $data = [
            [
                'identifier' => 'application_setting',
                'meta_key' => "favicon",
                'value' => uploadMediaByPath('app_setting',public_path('images/favicon.png')),
                'is_file' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'identifier' => 'application_setting',
                'meta_key' => "logo",
                'value' => uploadMediaByPath('app_setting',public_path('images/logo.jpg')),
                'is_file' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'identifier' => 'application_setting',
                'meta_key' => "application_name",
                'value' => 'RetroCube',
                'is_file' => 0,
                'created_at' => Carbon::now()
            ]
        ];
        \DB::table('application_setting')->insert($data);
    }


    public function mailTemplates()
    {
        $forgot_password_template = file_get_contents(__DIR__ . '/forgot_password_template_body.blade.php',FILE_USE_INCLUDE_PATH);
        $user_register_template =  file_get_contents(__DIR__ . '/user_register_template_body.blade.php',FILE_USE_INCLUDE_PATH);
        $data =
            [
                [
                    'identifier' => 'forgot-password',
                    'subject'    => 'Forgot Password Confirmation',
                    'body'       => $forgot_password_template,
                    'wildcard'   => '[USERNAME],[LINK],[YEAR],[APP_NAME]',
                    'created_at' => Carbon::now()
                ],
                [
                    'identifier' => 'user_registration',
                    'subject'    => 'Welcome to [APP_NAME]',
                    'body'       => $user_register_template,
                    'wildcard'   => '[USERNAME],[LINK],[YEAR],[APP_NAME]',
                    'created_at' => Carbon::now()
                ]
            ];
        \DB::table('mail_templates')->insert($data);
    }

    public function cms_widget()
    {
        \DB::table('cms_widgets')
            ->insert([
                [
                    'title'            => 'Total User',
                    'slug'              => time(). uniqid(),
                    'description'      => NULL,
                    'icon'             => 'icon-user',
                    'color'            => '#4b71fa',
                    'div_column_class' => 'col-md-3',
                    'link'             => '/admin/app-users',
                    'widget_type'      => 'small_box',
                    'sql'              => 'Select count(*) from users limit 1',
                    'config'           => NULL,
                ],
                [
                    'title'       => 'Total Roles',
                    'slug'              => time(). uniqid(),
                    'description' => NULL,
                    'icon'        => 'icon-people',
                    'color'            => '#7bcb4d',
                    'div_column_class' => 'col-md-3',
                    'link'             => '/admin/app-users',
                    'widget_type'      => 'small_box',
                    'sql'              => 'Select count(*) from users limit 1',
                    'config'           => NULL,
                ],
                [
                    'title'       => 'Total User',
                    'slug'              => time(). uniqid(),
                    'description' => NULL,
                    'icon'        => 'icon-user',
                    'color'            => '#4b71fa',
                    'div_column_class' => 'col-md-3',
                    'link'             => '/admin/app-users',
                    'widget_type'      => 'small_box',
                    'sql'              => 'Select count(*) from users limit 1',
                    'config'           => NULL,
                ],
                [
                    'title'       => 'Total Roles',
                    'slug'              => time(). uniqid(),
                    'description' => NULL,
                    'icon'        => 'icon-people',
                    'color'            => '#7bcb4d ',
                    'div_column_class' => 'col-md-3',
                    'link'             => '/admin/app-users',
                    'widget_type'      => 'small_box',
                    'sql'              => 'Select count(*) from users limit 1',
                    'config'           => NULL,
                ],
                [
                    'title'       => 'Users',
                    'slug'              => time(). uniqid(),
                    'description' => NULL,
                    'icon'        => 'icon-user',
                    'color'            => '#fff',
                    'div_column_class' => 'col-md-12',
                    'link'             => '/admin/app-users',
                    'widget_type'      => 'line_chart',
                    'sql'              => 'SELECT count(id) AS value, MONTHNAME(created_at) as label FROM users where YEAR(created_at) = YEAR(now()) group by MONTH(created_at) order by MONTH(created_at) asc',
                    'config'           => NULL,
                ]
            ]);

    }

    public function category()
    {
        \DB::table('categories')
            ->insert([
                [
                    'user_id' => 0,
                    'title' => 'Fitness',
                    'slug' => 'fitness',
                    'type' => 'business',
                    'status' => '1',
                    'created_at' => now()
                ],
                [
                    'user_id' => 0,
                    'title' => 'Clothing',
                    'slug' => 'clothing',
                    'type' => 'business',
                    'status' => '1',
                    'created_at' => now()
                ],
                [
                    'user_id' => 0,
                    'title' => 'Grocery',
                    'slug' => 'grocery',
                    'type' => 'business',
                    'status' => '1',
                    'created_at' => now()
                ],
                [
                    'user_id' => 0,
                    'title' => 'Health',
                    'slug' => 'health',
                    'type' => 'business',
                    'status' => '1',
                    'created_at' => now()
                ],
                [
                    'user_id' => 0,
                    'title' => 'Appliances',
                    'slug' => 'appliances',
                    'type' => 'business',
                    'status' => '1',
                    'created_at' => now()
                ],
                [
                    'user_id' => 0,
                    'title' => 'Physical Health',
                    'slug' => 'physical-health',
                    'type' => 'promote',
                    'status' => '1',
                    'created_at' => now()
                ],
                [
                    'user_id' => 0,
                    'title' => 'Mental Health',
                    'slug' => 'mental-health',
                    'type' => 'promote',
                    'status' => '1',
                    'created_at' => now()
                ],
                [
                    'user_id' => 0,
                    'title' => 'Social Health',
                    'slug' => 'social-health',
                    'type' => 'promote',
                    'status' => '1',
                    'created_at' => now()
                ]
            ]);
    }

    public function packages()
    {
        \DB::table('packages')
            ->insert([
                [
                    'title'         => 'Half Yearly',
                    'slug'          => 'half-yearly',
                    'description'   => '<p>All Features in standard</p><p>All Features in standard</p><p>All Features in standard</p>',
                    'amount'        => '99',
                    'trial_period'  => '0',
                    'duration'      => '6',
                    'duration_unit' => 'month',
                    'status'        => '1',
                    'created_at'    => now()
                ],[
                    'title'         => 'Yearly',
                    'slug'          => 'yearly',
                    'description'   => '<p>All Features in standard</p><p>All Features in standard</p><p>All Features in standard</p>',
                    'amount'        => '179',
                    'trial_period'  => '0',
                    'duration'      => '12',
                    'duration_unit' => 'month',
                    'status'        => '1',
                    'created_at'    => now()
                ]
            ]);
    }
}
