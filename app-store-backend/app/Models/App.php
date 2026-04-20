<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $fillable = ['name', 'package_name', 'description', 'icon_url', 'is_active'];

    public function versions() { return $this->hasMany(AppVersion::class); }
    public function latestVersion() { return $this->hasOne(AppVersion::class)->latest('version_code'); }
}
