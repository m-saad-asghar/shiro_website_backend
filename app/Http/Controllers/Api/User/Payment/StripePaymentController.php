<?php

namespace App\Http\Controllers\Api\User\Payment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\UserPaymentMethodResource;
use App\Http\Traits\GeneralTrait;
use App\Models\User;
use App\Models\UserPaymentMethod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    use  GeneralTrait;


    public function attachPaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paymentMethod' => 'required|string',
            'paymentMethodName' => 'required|string',

        ]);

        if ($validator->fails()) {
            return $this->requiredField('Invalid payment method ID.');
        }
        try {
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $user = auth('sanctum')->user();
            $user = User::findOrFail($user->id);
//
            $customerId = $user->customer_id;

            $paymentMethod = PaymentMethod::retrieve($request->input('paymentMethod'));
            $paymentMethod->attach(['customer' => $customerId]);

            $paymentMethod = $user->userPaymentMethods()->create([
                'payment_method' => $request->input('paymentMethod'),
                'payment_method_name' => $request->input('paymentMethodName'),
            ]);


            $data['payment_method'] = new UserPaymentMethodResource($paymentMethod);

            return $this->apiResponse($data);
        } catch (Exception $e) {

            return $this->handleException($e);
        }
    }


    public function detach_payment(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $paymentMethodId = $request->input('paymentMethodId');

            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->detach();

            UserPaymentMethod::where('payment_method', $paymentMethodId)->delete();

            $userPaymentMethods = $user->userPaymentMethods()->get();

            $data['payment_methods'] = UserPaymentMethodResource::collection($userPaymentMethods);
            $data['message'] = 'Payment method detached successfully.';

            return $this->apiResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }




    // public function process_payment($request)
    // {
    //     $paymentMethod = $request->input('payment_method');
    //     $amount = $request->input('amount');


    //     $amount_in_cent = $amount * 100;
    //     $currency = 'aed';
    //     $user = auth('sanctum')->user();

    //     $get_stripe_customer_id = UserPaymentMethod::where('user_id', $user->id)
    //                                               ->where('payment_method', $paymentMethod)
    //                                                ->get();

    //     $customerId = $user->stripe_customer_id;
    //     // if(count($get_stripe_customer_id)==0){
    //     //     return false;
    //     // }

    //     // $customerId = $get_stripe_customer_id[0]->stripe_customer_id;

    //     if(!$customerId) {
    //         return false;
    //     }


    //     try {

    //         Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

    //         $paymentIntent = PaymentIntent::create([
    //             'amount' => $amount_in_cent,
    //             'currency' => $currency,
    //             'customer' => $customerId,
    //             'payment_method' => $paymentMethod,
    //             'automatic_payment_methods' => [
    //                 'enabled' => false,
    //             ],
    //             'payment_method_types' => ['card'],
    //             'confirm' => true,
    //         ]);


    //         if ($paymentIntent->status === 'succeeded') {


    //             //payment success
    //             //here we need to add the code to create the order

    //             // return response()->json([
    //             //     'message' => 'Payment successful!',
    //             //     'status' => 'success',
    //             // ], 200);
    //             return 'Payment successful!';


    //         } else {
    //             // return response()->json([
    //             //     'message' => 'Payment failed.',
    //             //     'status' => 'error',
    //             // ], 422);
    //             return 'Payment failed.' ;
    //         }
    //     } catch (\Exception $e) {
    //         // Handle errors
    //         return response()->json([
    //             'message' => 'Failed to process payment: ' . $e->getMessage(),
    //             'status' => 'error',
    //         ], 422);
    //     }


    // }


    public function process_payment($amount, $paymentMethod)
    {
        $amount_in_cent = $amount * 100;
        $currency = 'aed';
        $user = auth('sanctum')->user();

        $get_stripe_customer_id = UserPaymentMethod::where('user_id', $user->id)
            ->where('payment_method', $paymentMethod)
            ->get();

        $customerId = $user->customer_id;

        if (!$customerId) {

            return false;
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));


            $paymentIntent = PaymentIntent::create([
                'amount' => $amount_in_cent,
                'currency' => $currency,
                'customer' => $customerId,
                'payment_method' => $paymentMethod,
                'automatic_payment_methods' => [
                    'enabled' => false,
                ],
                'payment_method_types' => ['card'],
                'confirm' => true,
            ]);


            if ($paymentIntent->status === 'succeeded') {
                return 'Payment successful!';
            } else {
                return 'Payment failed.';
            }
        } catch (Exception $e) {
            return 'Failed to process payment: ' . $e->getMessage();
        }
    }


    public function getUserPaymentMethods(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $userPaymentMethods = $user->userPaymentMethods()->get();

            $data['payment_methods'] = UserPaymentMethodResource::collection($userPaymentMethods);

            return $this->apiResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }


}
