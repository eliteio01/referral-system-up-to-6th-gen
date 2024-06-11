<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {

        //   Log::info($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:3',
                'email' => 'required|unique:users',
                'phoneNumber' => 'required|unique:users,phone_number',
                'password' => 'required|min:8|confirmed',
                'referredBy' => 'nullable|exists:users,referral_code'
            ],
            [
                'name.required' => 'Name is required',
                'name.min' => 'Name must be more than three (3)',
                'email.required' => 'Email is required',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'phoneNumber.min' => 'Invalid phone number.',
                'email.unique' => 'Email is already registerd.',
                'password.confirmed' => 'Password confirmation does not match.',
                'referredBy.exists' => 'Invalid referral code'
            ]
        );

        if ($validator->fails()) {
            return response([
                'result' => false,
                'error' => [
                    'message' => $validator->errors()->first(),
                    'code' => 102
                ]
            ], 400);
        }

        try {

            DB::beginTransaction();

            $userSavedData = createNewUser($request->all());

            if ($request->referredBy == null) {
                referralSystem('SYSTEM', $userSavedData->id);
            } else {

                referralSystem($request->referredBy, $userSavedData->id);
            }
            createNewUserWallet($userSavedData->id);

            $token = $userSavedData->createToken($userSavedData->email)->accessToken;

            DB::commit();
            return response([
                'result' => true,
                'data' => [
                    'token' => $token
                ]
            ], 200);
        } catch (Exception $e) {

            Log::error($e);

            return response([
                'result' => false,
                'error' => [
                    'message' => 'Somethiing went wrong.pls try again',
                    'code' => 105
                ]
            ], 500);
        }
    }
}
