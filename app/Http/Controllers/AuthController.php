<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $service)
    {
        $this->authService = $service;
    }

    public function index(Request $request)
    {
        try 
        {
            $login = $this->authService->userLogin($request);
            return AppConstants::apiResponse(404, 'Login Successful!', $login);

        } 
        catch (\Exception $e) 
        {
            Log::error('Error in user login.', [$e]);
            return AppConstants::apiResponse(404, 'Error in user login, please try again!');
        }
    }

    public function me()
    {
        try 
        {
            $me = $this->authService->me();
            return AppConstants::apiResponse(200, 'Customer Data!', $me);
        } 
        catch (\Exception $e) 
        {
            Log::error('Error in customer data fetch!', [$e]);
            return AppConstants::apiResponse(404, 'Error in customer data fetch, please try again!');
        }
    }
}
