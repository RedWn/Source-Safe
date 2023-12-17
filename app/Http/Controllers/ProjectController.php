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

    public function edit(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required|string',
        ]);
        $project = Project::findOrFail($request->input('id'));
        $project->update(['name' => $request->input('name')]);

        return $this->success(message: 'project edited successfully!');
    }

    public function delete(int $projectID)
    {
        Ledger::where('projectID', $projectID)->delete();
        Folder::where('projectID', $projectID)->delete();
        File::where('projectID', $projectID)->delete();
        $project = Project::findOrFail($projectID);
        $project->delete();

        return $this->success(message: 'Deleted project successfully!');
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'projectID' => 'required|exists:projects,id',
            'userID' => 'required|exists:users,id',
        ]);
        $projectID = $request->input("projectID");
        $userID = $request->input("userID");
        $entry = Ledger::where('projectID', $projectID)->where('userID', $userID)->first();
        if ($entry) {
            return $this->error("user already added", 400);
        }
        Ledger::create([
            'projectID' => $projectID,
            'userID' => $userID,
        ]);
        return $this->success(message: 'User added successfully!', status: 201);
    }

    public function removeUser(Request $request)
    {
        $request->validate([
            'projectID' => 'required|exists:projects,id',
            'userID' => 'required|exists:users,id',
        ]);
        $projectID = $request->input("projectID");
        $userID = $request->input("userID");
        Ledger::where('projectID', $projectID)->where('userID', $userID)->delete();
        return $this->success(message: 'User removed successfully!');
    }

    public function getUserProjects(Request $request)
    {
        $ledger = Ledger::where('userID', $request->user()->id)->get();
        $projects = [];
        foreach ($ledger as $entry) {
            $project = Project::findOrFail($entry->projectID);
            $projects[] = $project;
        }
        return $this->success($projects, 'success', 200);
    }

    public function getProjectUsers(int $projectID)
    {
        $ledger = Ledger::where('projectID', $projectID)->get();
        $users = [];
        foreach ($ledger as $entry) {
            $user = User::findOrFail($entry->userID);
            $users[] = $user;
        }
        return $this->success($users, 'success', 200);
    }

    public function addFirstUser(int $projectID, int $userID)
    {
        Ledger::create([
            'projectID' => $projectID,
            'userID' => $userID,
        ]);
    }

    public function createRootFolder(int $projectID)
    {
        Folder::create([
            'name' => '/',
            'projectID' => $projectID,
        ]);
    }
}
