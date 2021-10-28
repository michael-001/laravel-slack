<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    //
    protected $table = 'mentions';

    protected $fillable = [
      'user_id', 'message_id', 'channel_id'
    ];

    protected $appends = [];

    public function messages()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}
