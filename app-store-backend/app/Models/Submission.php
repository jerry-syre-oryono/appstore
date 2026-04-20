<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['user_id', 'app_name', 'package_name', 'description', 'apk_url', 'temp_path', 'status', 'reviewer_notes', 'reviewed_by'];
    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
