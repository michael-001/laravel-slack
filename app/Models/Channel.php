<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    //
    protected $table = "channels";

    protected $fillable = [
        'name', 'owner_id'
    ];


    public function messages()
    {
        return $this->hasMany(Message::class);
    }


}
