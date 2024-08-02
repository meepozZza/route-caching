<?php

namespace App\Models;

use App\Models\Traits\HasRouteCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    use HasRouteCache;
}
