<?php

use App\Models\User;
use App\Models\User\ReferralHistory;
use App\Models\User\UsersWalletDetails;
use Carbon\Carbon;

if (!function_exists('referralSystem')) {
    function referralSystem($referralCode, $userId)
    {
        $amount = [
            'first_gen' => 6,
            'second_gen' => 5,
            'third_gen' => 4,
            'fourth_gen' => 3,
            'fifth_gen' => 2,
            'sixth_gen' => 1,
        ];

        if ($referralCode === 'SYSTEM') {
            $newReferral = new ReferralHistory();
            $newReferral->user_id = $userId;
            $newReferral->first_gen = 'SYSTEM';
            $newReferral->save();
            return true;
        }
        $referral = User::where('referral_code', $referralCode)->first();

        if (!$referral) {
            return false;
        }

        $referralWalletDetails = UsersWalletDetails::where('user_id', $referral->id)->first();


        if (!$referralWalletDetails) {
            return false;
        }
        $referralHistories = ReferralHistory::where('user_id', $referral->id)->first();

        $newReferral = new ReferralHistory();
        $newReferral->user_id = $userId;
        $newReferral->first_gen = $referralCode;

        updateWallet($referral->id, $amount['first_gen']);

        $referralLevel = [
            'first_gen',
            'second_gen',
            'third_gen',
            'fourth_gen',
            'fifth_gen',
            'sixth_gen',
        ];

        foreach ($referralLevel as $index => $level) {

            if (isset($referralHistories->$level)) {
                $genLevel =  $index + 1;

                if (isset($referralLevel[$genLevel])) {
                    $nextLevel = $referralLevel[$genLevel];
                    $newReferral->$nextLevel = $referralHistories->$level;

                    $ances = User::where('referral_code', $referralHistories->$level)->first();

                    if ($ances) {
                        $amount = $amount[$nextLevel];
                        updateWallet($ances->id, $amount);
                    }
                }
            } else {
                break;
            }
        }

        return $newReferral->save();
    }
}



if (!function_exists('updateWallet')) {
    function updateWallet($userId, $amount)
    {

        $WalletDetails = UsersWalletDetails::where('user_id', $userId)->first();
        $user = User::find($userId);

        if ($WalletDetails && $user) {
            $WalletDetails->acct_bal += $amount;
            $WalletDetails->last_credited = Carbon::now()->format('h:ia, F j,Y');
            $WalletDetails->save();
        }
    }
}
