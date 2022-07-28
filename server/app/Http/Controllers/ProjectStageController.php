<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ProjectStage;
use Validator;

class ProjectStageController extends Controller
{
    public function create (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'max:50|required',
            'project_id' => 'max:50|required',
        ]);

        if ($validator->fails()) {
            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            return response()->json(['errors' => $messages], 422);
        }

        $projectStage = ProjectStage::create($request->all());

        return response()->json($projectStage);
    }

    public function destroy (Request $request, $id) {
        $projectStage = ProjectStage::findOrFail($id);
        if ($projectStage->deleted_at) return response()->json(["The project stage cannot be deleted"], 422);
        $projectStage->deleted_at = date('Y-m-d H:i:s');
        $projectStage->save();
        return response()->json("Project stage has been deleted", 204);
    }
}
