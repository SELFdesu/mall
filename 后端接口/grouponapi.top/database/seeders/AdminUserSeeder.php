<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建用户
        $adminuser=User::create([
            'username'=>'网站管理员',
            'avatar'=>'default.jpg',
            'tel'=>'',
            'email'=>'',
            'password'=>bcrypt('12345678'),
            'sex'=>'0',
        ]);

        $user=User::create([
            'username'=>'username01',
            'avatar'=>'default.jpg',
            'tel'=>'',
            'email'=>'',
            'password'=>bcrypt('12345678'),
            'sex'=>'0',
        ]);
        //给用户分配角色
        $adminuser->assignRole('admin');
        $user->assignRole('user');
    }
}
