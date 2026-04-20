<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $fillable = ['app_id', 'version_code', 'version_name', 'apk_url', 'file_hash', 'changelog', 'is_active', 'is_force', 'file_size'];
    public function app() { return $this->belongsTo(App::class); }
}
