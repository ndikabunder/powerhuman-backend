<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Http\Requests\UpdateResponsibilityRequest;
use App\Models\Responsibility;
use Exception;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        // TODO: Get Params
        $id    = $request->input('id');
        $name  = $request->input('name');
        $limit = $request->input('limit');

        // TODO: Get all Responsibility data
        $responsibilityQuery = Responsibility::query();

        // TODO: Get Single Data Responsibility
        if ($id) {
            $responsibility = $responsibilityQuery->find($id);

            if ($responsibility) {
                return ResponseFormatter::success('$responsibility', 'Responsibility Found');
            }

            return ResponseFormatter::error('Responsibility Not Found', 404);
        }

        // TODO: Get multiple data
        $Responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        // TODO: Search by name
        if ($name) {
            $Responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $Responsibilities->paginate($limit),
            'Responsibilities Found'
        );
    }

    public function create(CreateResponsibilityRequest $request)
    {
        try {
            // TODO: Create Responsibility
            $responsibility = Responsibility::create([
                'name'    => $request->name,
                'role_id' => $request->role_id
            ]);

            // TODO: Check Responsibility
            if (!$responsibility) {
                throw new Exception("Responsibility not found", 500);
            }

            // TODO: Response Create Successfully
            return ResponseFormatter::success($responsibility, 'Create Responsibility Successfully');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    // public function update(UpdateResponsibilityRequest $request, $id)
    // {
    //     try {
    //         // TODO: Find Responsibility by Id
    //         $responsibility = Responsibility::find($id);

    //         // TODO: If Responsibility Not Found
    //         if (!$responsibility) {
    //             throw new Exception('Responsibility nof Found', 500);
    //         }

    //         // TODO: Responsibility Update
    //         $responsibility->update([
    //             'name'       => $request->name,
    //             'company_id' => $request->company_id
    //         ]);

    //         // TODO: Response Update Successfully
    //         return ResponseFormatter::success($responsibility, 'Update Responsibility Successfully');
    //     } catch (Exception $error) {
    //         // TODO: Response Update Failed
    //         return ResponseFormatter::error($error->getMessage(), 400);
    //     }
    // }

    public function destroy($id)
    {
        try {
            // TODO: Get Item by id
            $responsibility = Responsibility::find($id);

            // TODO: Response if data not found
            if (!$responsibility) {
                throw new Exception('Responsibility Not Found', 400);
            }

            // TODO: Delete Responsibility
            $responsibility->delete();

            // TODO: Response Successfully Delete Responsibility
            return ResponseFormatter::success('Responsibility Deleted');
        } catch (Exception $error) {
            // TODO: Response Failed Delete Responsibility
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
