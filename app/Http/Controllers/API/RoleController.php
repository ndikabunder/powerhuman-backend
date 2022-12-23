<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function fetch(Request $request)
    {
        // TODO: Get Params
        $id    = $request->input('id');
        $name  = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);

        // TODO: Get all Role data
        $roleQuery = Role::query();

        // TODO: Get Single Data Role
        if ($id) {
            $role = $roleQuery->with('responsibilities')->find($id);

            if ($role) {
                return ResponseFormatter::success('$role', 'Role Found');
            }

            return ResponseFormatter::error('Role Not Found', 404);
        }

        // TODO: Get multiple data
        $roles = $roleQuery->where('company_id', $request->company_id);

        // TODO: Search by name
        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibilities) {
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles Found'
        );
    }

    public function create(CreateRoleRequest $request)
    {
        try {
            // TODO: Create Role
            $role = Role::create([
                'name'       => $request->name,
                'company_id' => $request->company_id
            ]);

            // TODO: Check Role
            if (!$role) {
                throw new Exception("Role not found", 500);
            }

            // TODO: Response Create Successfully
            return ResponseFormatter::success($role, 'Create Role Successfully');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            // TODO: Find Role by Id
            $role = Role::find($id);

            // TODO: If Role Not Found
            if (!$role) {
                throw new Exception('Role nof Found', 500);
            }

            // TODO: Role Update
            $role->update([
                'name'       => $request->name,
                'company_id' => $request->company_id
            ]);

            // TODO: Response Update Successfully
            return ResponseFormatter::success($role, 'Update Role Successfully');
        } catch (Exception $error) {
            // TODO: Response Update Failed
            return ResponseFormatter::error($error->getMessage(), 400);
        }
    }

    public function destroy($id)
    {
        try {
            // TODO: Get Item by id
            $role = Role::find($id);

            // TODO: Response if data not found
            if (!$role) {
                throw new Exception('Role Not Found', 400);
            }

            // TODO: Delete Role
            $role->delete();

            // TODO: Response Successfully Delete Role
            return ResponseFormatter::success('Role Deleted');
        } catch (Exception $error) {
            // TODO: Response Failed Delete Role
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
