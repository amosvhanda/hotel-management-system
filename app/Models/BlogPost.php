<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function blog(){
        return $this->belongsTo(BlogCategory::class, 'blogcat_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /** Approved comments shown under the post. */
    public function approvedComments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id')->where('status', '1');
    }
}
