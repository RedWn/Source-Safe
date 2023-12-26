<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\File;
use App\Models\Folder;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use function Laravel\Prompts\password;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Project::factory()
            ->count(10)
            ->hasFolders(3)
            ->hasUsers(3)
            ->create();



        User::create(['username' => 'hasan', 'password' => '1234']);
    }
}
