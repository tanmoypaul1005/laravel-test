<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    public $table="services";
    public $primaryKey="id";
    public $incrementing=true;
    public $keyType="int";
    public $timestamp=false;
    use HasFactory;
}
