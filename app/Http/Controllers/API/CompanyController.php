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
    public function fetch(Request $request)
    {
        $id    = $request->input('id');
        $name  = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['users'])
            ->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            });

        // Get single data
        if ($id) {
            $company = $companyQuery->find($id);

            if ($company) {
                return ResponseFormatter::success($company, 'Company found');
            }

            return ResponseFormatter::error('Company not found', 404);
        }

        // Get multiple data
        $companies = $companyQuery;

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies found'
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
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // TODO: Update Company
            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo
            ]);

            // TODO: Response Success Update
            return ResponseFormatter::success($company, 'Company Updated');
        } catch (Exception $error) {
            // TODO: Response Failed Updated
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
