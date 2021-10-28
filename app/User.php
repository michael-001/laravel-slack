<?php

namespace App;

use App\Models\Channel;
use App\Models\Mention;
use App\Models\Message;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'handle'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userMessages()
    {
        return $this->hasMany(Message::class);
//        return Message::where('user_id', $this->attribute['id']);
    }

    public function userChannels()
    {
        return $this->hasMany(Channel::class, 'owner_id');
    }

    public function userMentions()
    {
        return $this->hasMany(Mention::class)->with('messages');
    }
}
