<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        Project::factory()
            ->count(10)
            ->hasUsers(3)
            ->create();

        Folder::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => [
                'project_id' => Project::all()->random()->id,
                'folder_id' => random_int(1, 10),
            ])->create();

        $user = User::create(['username' => 'hasan', 'password' => '1234']);
        $user->projects()->attach(1);

        Schema::enableForeignKeyConstraints();
    }
}
