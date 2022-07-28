<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\TimeRecord;
use \App\Models\ProjectStage;
use \App\Models\Project;
use Illuminate\Support\Facades\Validator;

class TimeRecordController extends Controller
{
    public function getTimeRecordsByDate($date) {
        $timeRecords = TimeRecord::with('project', 'projectStage')->where('user_id', Auth::user()->id)->where('date', $date)->whereNull('deleted_at')->get();
        return response()->json($timeRecords);
    }

    public function create (Request $request) {
        $validator = Validator::make($request->all(), [
            'project' => 'required',
            'date' => 'required',
            'duration' => 'required|numeric|min:0|not_in:0'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(['valid' => false, 'errors' => $messages], 422);
        }

        $project = Project::findOrFail($request->input('project'));

        // by default, the request is unauthorised for better security
        $authorised = false;
        // If the user is a standard employee with base level authorisation
        if (Auth::user()->auth_level != 2) {
            // Get the department that the project belongs to
            $projectDepartment = $project->client->department;
            // Loop over all of the departments that the user has permission to access
            foreach (Auth::user()->departments as $departmentKey => $department) {
                // If the user has permission to access the same department that the project belongs to, authorise the request
                if ($department->id == $projectDepartment->id) $authorised = true;
            }
        } else $authorised = true;

        if (!$authorised) return response()->json("You are not authorised to create a time record for this project", 403);

        // Detect if the time record is being attached to a project stage (optional relationship)
        $projectStage = ($request->input('projectStage')) ? ProjectStage::findOrFail($request->input('projectStage')) : null;
        // Detect if the time record has notes (optional relationship)
        $notes = ($request->input('notes')) ? $request->input('notes') : null;
        $duration = $request->input('duration');

        $timeRecord = TimeRecord::create([
            'minutes' => $duration,
            'notes' => $notes,
            'date' => $request->input('date'),
            'user_id' => Auth::user()->id,
            'project_id' => $project->id,
            'project_stage_id' => ($projectStage) ? $projectStage->id : null
        ]);

        return response()->json(TimeRecord::with('project', 'projectStage')->where('id', $timeRecord->id)->first());
    }

    public function destroy ($id) {
        $timeRecord = TimeRecord::findOrFail($id);
        if ($timeRecord->user_id != Auth::user()->id) return response()->json("You are not authorised to delete this time record", 403);
        if ($timeRecord->deleted_at) return response()->json(["The time record cannot be deleted"], 422);
        $timeRecord->deleted_at = date('Y-m-d H:i:s');
        $timeRecord->save();
        return response()->json("Time record has been deleted", 204);
    }
}
