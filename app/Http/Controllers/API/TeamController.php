<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {
        // TODO: Get Params
        $id    = $request->input('id');
        $name  = $request->input('name');
        $limit = $request->input('limit', 10);

        // TODO: Get all Team data
        $teamQuery = Team::query();

        // TODO: Get Single Data Team
        if ($id) {
            $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success('$team', 'Team Found');
            }

            return ResponseFormatter::error('Team Not Found', 404);
        }

        // TODO: Get multiple data
        $teams = $teamQuery->where('company_id', $request->company_id);

        // TODO: Search by name
        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams Found'
        );
    }

    public function create(CreateTeamRequest $request)
    {
        try {
            // TODO: Check Icon file and store icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // TODO: Create Team
            $team = Team::create([
                'name'       => $request->name,
                'icon'       => $path,
                'company_id' => $request->company_id
            ]);

            // TODO: Check Team
            if (!$team) {
                throw new Exception("Team not found", 500);
            }

            // TODO: Response Create Successfully
            return ResponseFormatter::success($team, 'Create Team Successfully');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            // TODO: Find Team by Id
            $team = Team::find($id);

            // TODO: If Team Not Found
            if (!$team) {
                throw new Exception('Team nof Found', 500);
            }

            // TODO: Check Image and Upload Image
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // TODO: Team Update
            $team->update([
                'name'       => $request->name,
                'icon'       => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id
            ]);

            // TODO: Response Update Successfully
            return ResponseFormatter::success($team, 'Update Team Successfully');
        } catch (Exception $error) {
            // TODO: Response Update Failed
            return ResponseFormatter::error($error->getMessage(), 400);
        }
    }

    public function destroy($id)
    {
        try {
            // TODO: Get Item by id
            $team = Team::find($id);

            // TODO: Response if data not found
            if (!$team) {
                throw new Exception('Team Not Found', 400);
            }

            // TODO: Delete Team
            $team->delete();

            // TODO: Response Successfully Delete Team
            return ResponseFormatter::success('Team Deleted');
        } catch (Exception $error) {
            // TODO: Response Failed Delete Team
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
