<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function type(){
        return $this->belongsTo(RoomType::class, 'roomtype_id', 'id');
    }

    public function facilities(){
        return $this->hasMany(Facility::class, 'rooms_id');
    }

    public function room_numbers(){
        return $this->hasMany(RoomNumber::class, 'rooms_id')->where('status','Active');
    }

    /** Published guest reviews, newest first. */
    public function reviews(){
        return $this->hasMany(Review::class, 'rooms_id')->where('status', Review::APPROVED)->latest('approved_at');
    }


}
