<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, SpatialTrait;


    protected $fillable = [
        'name',
        'email',
        'password',
        'tagline',
        'username',
        'about',
        'formatted_address',
        'available_to_hire',
        'location'
    ];
    protected $spatialFields = [
        'location'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendPasswordResetNotification($token)
    {
        // use closure function to change the url of the notification email
        $ResetPasswordNotifier = new ResetPassword($token);
        $ResetPasswordNotifier::$createUrlCallback = function ($notifiable, $token) {
            return url(config('app.client_url') . route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        };
        $this->notify($ResetPasswordNotifier);
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function comment()
    {
        return  $this->hasMany(Comment::class);
    }
}
