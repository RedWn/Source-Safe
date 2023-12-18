<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\Ledger;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $project = Project::create([
            'name' => $request->input("name"),
        ]);
        $this->addFirstUser($project->id, $request->user()->id);
        $this->createRootFolder($project->id);
        return $this->success($project, 'project created successfully', 201);
    }

    public function edit(Request $request, int $projectId)
    {
        $request->validate([
            'name' => 'required|string|max:200',
        ]);
        $project = Project::findOrFail($projectId);
        $project->update(['name' => $request->input('name')]);

        return $this->success(message: 'project edited successfully!');
    }

    public function delete(int $projectID)
    {
        Ledger::where('project_id', $projectID)->delete();
        Folder::where('project_id', $projectID)->delete();
        File::where('project_id', $projectID)->delete();
        $project = Project::findOrFail($projectID);
        $project->delete();

        return $this->success(message: 'Deleted project successfully!');
    }

    public function addUser(Request $request, int $projectID)
    {
        $request->validate([
            'userID' => 'required|exists:users,id',
        ]);

        // TODO: Replace this query with proper error handling
        $project = Project::findOrFail($projectID);

        $userID = $request->input("userID");

        $entry = Ledger::where('project_id', $projectID)->where('user_id', $userID)->first();
        if ($entry) {
            return $this->error("user already added", 400);
        }
        Ledger::create([
            'project_id' => $projectID,
            'user_id' => $userID,
        ]);
        return $this->success(message: 'User added successfully!', status: 201);
    }

    public function removeUser(Request $request, int $projectID)
    {
        $request->validate([
            'userID' => 'required|exists:users,id',
        ]);
        $projectID = $request->input("projectID");
        $userID = $request->input("userID");
        Ledger::where('project_id', $projectID)->where('user_id', $userID)->delete();
        return $this->success(message: 'User removed successfully!');
    }

    public function getMyProjects(Request $request)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)->get();

        $projects = [];
        foreach ($ledger as $entry) {
            $project = Project::findOrFail($entry->project_id);
            $projects[] = $project;
        }
        return $this->success($projects, 'success', 200);
    }

    public function getProjectUsers(int $projectID)
    {
        $ledger = Ledger::where('project_id', $projectID)->get();
        $users = [];
        foreach ($ledger as $entry) {
            $user = User::findOrFail($entry->user_id);
            $users[] = $user;
        }
        return $this->success($users, 'success', 200);
    }

    private function addFirstUser(int $projectID, int $userID)
    {
        Ledger::create([
            'project_id' => $projectID,
            'user_id' => $userID,
        ]);
    }

    private function createRootFolder(int $projectID)
    {
        Folder::create([
            'name' => '/',
            'project_id' => $projectID,
        ]);
    }
}
