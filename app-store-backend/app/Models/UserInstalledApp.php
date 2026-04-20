<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInstalledApp extends Model
{
    protected $fillable = ['user_id', 'app_id', 'current_version_code'];
}
