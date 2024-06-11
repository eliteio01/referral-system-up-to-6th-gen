<?php

use App\Models\User;
use App\Models\User\UsersWalletDetails;
use Illuminate\Support\Facades\Hash;

if (!function_exists('createNewUser')) {
    function createNewUser(array $userData)
    {
        $hashedPassword = Hash::make($userData['password']);
        return User::create([
            'name' => $userData['name'],
            'phone_number' => $userData['phoneNumber'],
            'image' => 'assets\user-check.png',
            'referral_code' => generateRandomCode(8, 'Ref-'),
            'password' => $hashedPassword,
            'elite_id' => generateRandomCode(6, 'Elite|id'),
            'email' => $userData['email']

        ]);
    }
}


if (!function_exists('createNewUserWallet')) {
    function createNewUserWallet($userId)
    {
        return UsersWalletDetails::create([
            'user_id' => $userId
        ]);
    }
}


if (!function_exists('generateRandomCode')) {
    function generateRandomCode($lenght, $name)
    {
        $char = '1234567890abcdefghjklmnopqrstuvwxyz';
        $charLength = strlen($char);
        $currentTime = time();

        $firstThree = substr($currentTime, 0, 3);

        $lastThree = substr($currentTime . bin2hex(random_bytes(4)), -3);


        $currentTime = str_replace('.', '', $currentTime);

        $code = '';

        for ($i = 0; $i < $lenght; $i++) {
            $code .= $char[rand(0, $charLength - 1)];
        }
        $code .= $firstThree . $lastThree;

        return $code;
    }
}
