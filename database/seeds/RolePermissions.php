<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolePerms = $this->_getRolePerms();
        foreach ($rolePerms as $role => $perms) {
            $r = Role::whereName($role)->first();
            if ($r) {
                foreach ($perms as $per) {
                    $p = Permission::whereName($per)->first();
                    if ($p && !$r->hasPermission($per)) {
                        $r->attachPermission($p);
                    }
                }
            }
        }
    }

    private function _getRolePerms()
    {
        return ['admin' => Permission::getPemNameNoAll()];
    }
}
