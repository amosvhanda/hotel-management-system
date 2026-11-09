<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    use \App\Models\Concerns\Auditable;

    use HasFactory;
    protected $guarded = [];
}
