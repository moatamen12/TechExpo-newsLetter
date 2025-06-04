<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    // Table name
    protected $table = 'contact_messages';
    // Primary key
    protected $primaryKey = 'id';
    const UPDATED_AT = null; // Disable the updated_at column

    // Fillable attributes
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'subject',
        'message_category',
        'message_statue',
        'message',
        'replayedTO_at' 
    ];

    protected $dates = [
        'created_at',
        'replayedTO_at'
    ];

    //relation with the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}