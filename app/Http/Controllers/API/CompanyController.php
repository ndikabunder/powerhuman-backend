<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

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
}
