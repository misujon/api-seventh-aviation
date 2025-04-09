<?php

namespace App\Services;
use App\Constants\AppConstants;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\FlightBooking;
use Exception;


class SslcPaymentVerifyService
{
    public function verifyPayment(string $bookingId, string $sslcPayId, float $amount, string $currency, array $orderData):array
    {
        $getBookingData = FlightBooking::where('booking_id', $bookingId)
                            ->where('status', AppConstants::BOOKING_STATUS_BOOKED)
                            ->first();
        if (!$getBookingData) throw new Exception('Error to find flight data!');

        $getBookingData->payment_full_response = $orderData;
        $getBookingData->save();

        if ($amount != $getBookingData->grand_total_price) throw new Exception('Amount verification failed!');
        if ($currency != $getBookingData->billing_currency) throw new Exception('Currency verification failed!');

        if ($getBookingData->payment_status == 'PROCESSING') 
        {
            $sslc = new SslCommerzNotification();
            $validation = $sslc->orderValidate($orderData, $bookingId, $amount, $currency);
            if ($validation) 
            {
                $getBookingData->payment_id = $sslcPayId;
                $getBookingData->payment_status = AppConstants::PAY_STATUS_COMPLETE;
                $getBookingData->save();

                // The ticket generation process will be done here
                
                return [
                    'message' => 'Payment successful!'
                ];
            }
        }
        else if ($getBookingData->payment_status == 'COMPLETE' || $getBookingData->payment_status == 'SUCCESS') 
        {
            return [
                'message' => 'Payment already suceeded!'
            ];
        }
        
        throw new Exception('Error to verify payment! txn: ' . $bookingId);
    }

    public function failPayment(array $post_data): array
    {
        $getBookingData = FlightBooking::where('booking_id', $post_data['tran_id'])
                            ->where('status', AppConstants::BOOKING_STATUS_BOOKED)
                            ->first();
        if (!$getBookingData) throw new Exception('Error to find flight data!');

        $getBookingData->payment_full_response = $post_data;
        $getBookingData->save();

        if ($getBookingData->payment_status == 'COMPLETE' || $getBookingData->payment_status == 'SUCCESS') 
        {
            return [
                'message' => 'Payment already suceeded!'
            ];
        }
        else
        {
            $getBookingData->payment_status = AppConstants::PAY_STATUS_FAILED;
            $getBookingData->status = AppConstants::BOOKING_STATUS_CANCELLED;
            $getBookingData->payment_failed_reason = $post_data['error'];
            $getBookingData->save();

            return [
                'message' => 'Request Payment is failed!'
            ];
        }
    }
}