<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    public $table="visitors";
    public $primaryKey="id";
    public $incrementing=true;
    public $keyType="int";
    public $timestamp=false;
    use HasFactory;
}
