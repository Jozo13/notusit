<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $admin = new Role();
            $admin->name = Roles::ADMIN;
            $admin->save();

            $moderator = new Role();
            $moderator->name = Roles::MODERATOR;
            $moderator->save();

            $superUser = new User();

            $superUser->name = 'SuperUser';
            $superUser->email = 'superuser@email.com';
            $superUser->password = '$2y$12$60cRCCCDG1.3SbMR2mmRje9gSmTBLnANtLhJigauwzRg3zn1MMh4a';
            $superUser->save();

            $superUser->roles()->attach($admin->id);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();
    }
}
