<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class SuperAdmins extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->_createAdmin();
        $this->_createUser();
    }

    private function _createAdmin()
    {
        $role = Role::where(['name' => 'admin'])->first();
        if ($role) {
            return true;
        }
        $role = [
            'name' => 'admin',
            'display_name' => '管理员',
            'description' => '管理员',
        ];
        Role::create($role);
    }

    private function _createUser()
    {
        $role = Role::where(['name' => 'admin'])->first();
        if ($role) {
            $user = \App\User::where(['username' => 'oaadmin'])->first();
            if ($user) {
                return true;
            }
            $user = [
                'username' => 'oaadmin',
                'alias' => '超级管理员',
                'email' => 'liuxiaoqing@shiyuegame.com',
                'mobile' => '18922753066',
                'is_mobile' => 1,
                'password' => bcrypt('123123'),
                'status' => \App\User::STATUS_ENABLE,
                'role_id' => $role->id,
            ];
            $admin = \App\User::create($user);
            $admin->attachRole($role);
            \App\Models\UserExt::create(['user_id' => $admin->user_id, 'is_confirm' => 1]);
        }
    }
}
