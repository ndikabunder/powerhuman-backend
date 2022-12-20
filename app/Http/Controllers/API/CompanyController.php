<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function all(Request $request)
    {
        // Catch Input
        $id = $request->input('id)');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // Cek Id dan Company
        if ($id) {
            $company = Company::with('users')->find($id);

            if ($company) {
                return ResponseFormatter::success($company, 'Company Found');
            }
            return ResponseFormatter::error('Company not found', 404);
        }

        $companies = Company::with('users');

        // Cek Name
        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        // Response API
        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Company Found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            // TODO: Check File and Create File Path
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // TODO: Create Company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);

            // TODO: Failed Create Company
            if (!$company) {
                throw new Exception('Company Not Created', '400');
            }

            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            $company->load('users');

            // TODO: Return Response Success Create Company
            return ResponseFormatter::success($company, 'Create Company Successfully');
        } catch (Exception $error) {
            // TODO: Return Response Failed Create Company
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            // TODO: Find Company $id
            $company = Company::find($id);

            // TODO: Check Company
            if (!$company) {
                throw new Exception('Company Not Found', 500);
            }

            // TODO: Upload Image Company
            if ($request->file('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // TODO: Update Company
            $company->update([
                'name' => $request->name,
                'logo' => $path
            ]);

            // TODO: Response Success Update
            return ResponseFormatter::success($company, 'Company Updated');
        } catch (Exception $error) {
            //throw $th;
        }
    }
}
