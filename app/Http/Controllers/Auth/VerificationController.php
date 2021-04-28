<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\Contracts\IUser;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

//use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{

    protected $user;

    public function __construct(IUser $user)
    {
        //$this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->users = $user;
    }

    public function verify(Request $request, User $user)
    {
        // check if URL is valid signed
        if (!URL::hasValidSignature($request)) {
            return response()->json(["error" => [
                "message" => "Invalid verification link or signature"
            ]], 422);
        }
        //check  if user already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json(["error" => [
                "message" => "Email address already verified "
            ]], 422);
        }
        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            "message" => "Email verification success"
        ], 200);
    }

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = $this->users->findWhereFirst('email', $request->email)->first();
        if (!$user) {
            return response()->json(["error" => [
                "email" => "Cant find user with this email address"
            ]], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(["error" => [
                "message" => "Email address already verified "
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return  response()->json(['status' => "verification link resend"]);
    }
}
