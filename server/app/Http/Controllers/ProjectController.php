<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\Project;
use \App\Models\User;
use Validator;

class ProjectController extends Controller
{
    public function index () {
        $projects = Project::with('projectStages')->whereNull('deleted_at')->get();
        $authUser = User::with('departments')->findOrFail(Auth::user()->id)->first();
        $authorisedProjects = [];

        // If the user does not have admin / manager privelages, only return the projects which they have access to
        // Return projects that belong to a client, which belongs to the user's department
        if (Auth::user()->auth_level != 2) {
            foreach ($projects as $key => $project) {
                $projectDepartment = $project->client->department;
                foreach ($authUser->departments as $departmentKey => $department) {
                    if ($department->id == $projectDepartment->id) array_push($authorisedProjects, $project);
                }
            }
        } else $authorisedProjects = $projects;
        
        return response()->json($authorisedProjects);
    }

    public function create (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'max:50|required',
            'reference' => 'max:50|required',
            'client_id' => 'required',
        ]);

        if ($validator->fails()) {
            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            return response()->json(['errors' => $messages], 422);
        }

        $project = Project::create($request->all());

        return response()->json($project);
    }

    public function destroy (Request $request, $id) {
        $project = Project::findOrFail($id);
        if ($project->deleted_at) return response()->json(["The project cannot be deleted"], 422);
        $project->deleted_at = date('Y-m-d H:i:s');
        $project->save();
        return response()->json("Project has been deleted", 204);
    }

    public function get (Request $request, $id) {
        $project = Project::with('projectStages')->whereNull('deleted_at')->findOrFail($id);
        return response()->json($project);
    }

    public function getAnalytics (Request $request, $id) {
        $project = Project::with('projectStages', 'timeRecords', 'timeRecords.user', 'timeRecords.projectStage')->whereNull('deleted_at')->findOrFail($id);
        return response()->json($project);
    }
}
