<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $table = "messages";

    protected $fillable = [
        'user_id', 'channel_id', 'text'
    ];




    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
