<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Http\Requests\Callback\VerifySslczSuccessRequest;
use App\Services\SslcPaymentVerifyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyPaymentController extends Controller
{
    private $paymentVerifyService;

    public function __construct(SslcPaymentVerifyService $service)
    {
        $this->paymentVerifyService = $service;
    }

    public function sslSuccess(VerifySslczSuccessRequest $request)
    {
        try
        {
            $result = $this->paymentVerifyService->verifyPayment(
                $request->input('tran_id'), 
                $request->input('val_id'), 
                $request->input('currency_amount'), 
                $request->input('currency_type'),
                $request->all()
            );
            return AppConstants::apiResponse(200, $result['message'], []);
        }
        catch(Exception $e)
        {
            Log::error('Error in verify payment.', [$e]);
            return AppConstants::apiResponse(404, 'Error to verify payment! Please try again with valid data.');
        }
    }

    public function sslFail(Request $request)
    {
        try
        {
            $result = $this->paymentVerifyService->failPayment($request->all());
            return AppConstants::apiResponse(200, $result['message'], []);
        }
        catch(Exception $e)
        {
            Log::error('Error in verify payment.', [$e]);
            return AppConstants::apiResponse(404, 'Error to verify payment! Please try again with valid data.');
        }
    }
}
