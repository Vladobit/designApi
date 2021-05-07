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

    protected $appends = [
        'photo_url'
    ];

    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '.jpg?s=200&d=mm';
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'available_to_hire' => 'boolean'
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
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
    // teams that the user belongs to
    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withTimestamps();
    }

    public function ownedTeams()
    {
        return $this->teams()
            ->where('owner_id', $this->id);
    }

    public function isOwnerOfTeam($team)
    {
        return (bool)$this->teams()
            ->where('id', $team->id)
            ->where('owner_id', $this->id)
            ->count();
    }



    // Relationships for invitations
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'recipient_email', 'email');
    }

    // relationships for chat messaging
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getChatWithUser($user_id)
    {
        $chat = $this->chats()
            ->whereHas('participants', function ($query)  use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->first();
        return $chat;
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
