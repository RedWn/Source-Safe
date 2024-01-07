<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $project = Project::create([
            'name' => $request->input("name"),
            'admin_id' => $request->user()->id,
        ]);

        $project->users()->attach($request->user()->id);

        $root = $project->folders()->create([
            'name' => '/',
        ]);
        $project["root_id"] = $root->id;
        $project->save();

        Log::info("Project $project->name was created.");

        return $this->success(new ProjectResource($project), 'Project created successfully.', 201);
    }

    public function edit(Request $request, int $projectID)
    {
        $request->validate([
            'name' => 'required|string|max:200',
        ]);
        $project = Project::findOrFail($projectID);

        Gate::authorize('update-project', $projectID);

        $project->update(['name' => $request->input('name')]);

        return $this->success(message: 'Project edited successfully.');
    }

    public function delete(int $projectID)
    {
        $project = Project::findOrFail($projectID);

        Gate::authorize('update-project', $projectID);

        $project->users()->detach();
        $project->files()->delete();

        $project->root_id = null;
        $project->save();
        $project->folders()->delete();

        $project->delete();
        return $this->success(message: 'Deleted project successfully!');
    }

    public function addUser(Request $request, int $projectID)
    {
        $request->validate([
            'userID' => 'required|exists:users,id',
        ]);

        $project = Project::findOrFail($projectID);

        Gate::authorize('update-project', $projectID);

        $userId = $request->input("userID");
        $user = $project->users()->find($userId);

        if ($user) {
            return $this->error("User already added to project.", 400);
        }

        $project->users()->attach($userId);

        return $this->success(message: 'User added successfully!', status: 201);
    }

    public function removeUser(Request $request, int $projectID)
    {
        $request->validate([
            'userID' => 'required|exists:users,id',
        ]);

        $project = Project::findOrFail($projectID);

        Gate::authorize('update-project', $projectID);

        $userID = $request->input("userID");

        $user = $project->users()->find($userID);

        if (!$user) {
            return $this->error("User does not belong to project.");
        }

        if ($user->id == $project->admin_id) {
            $project->users()->detach($userID);
            $users = $project->users()->where("id", "!=", $user->id)->get();

            // Delete project if 0 members are remaining
            if (count($users) == 0) {
                self::delete($projectID);
                return $this->success(message: "User and Project Deleted successfully");
            }

            // Assign one of the members the admin role (sorted by created_at)
            $project->admin_id = $users->first()->id;
            $project->save();
            return $this->success(message: 'User removed successfully! Admin changed');
        }

        $project->users()->detach($userID);

        return $this->success(message: 'User removed successfully!');
    }

    public function getMyProjects(Request $request)
    {
        $projects = ProjectResource::collection($request->user()->projects()->get());
        return $this->success($projects, 'success', 200);
    }

    public function getProjectUsers(int $projectID)
    {
        $project = Project::findOrFail($projectID);
        $users = UserResource::collection($project->users()->get());

        return $this->success($users, 'success', 200);
    }
}
