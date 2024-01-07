<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

enum ProjectActions: string
{
    case UpdateProjectResource = "Meow";
}

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define("update-project", function (User $user, int $projectId) {
           $project = Project::findOrFail($projectId);
           return $project->admin_id == $user->id;
        });

        // Determine if user belongs to this project or not.
        Gate::define("update-project-resource", function (User $user, int $projectId) {
            $project = $user->projects()->find($projectId);

            if ($project) return true;
            return false;
        });
    }
}
