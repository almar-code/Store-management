<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedReport extends Model
{
    protected $table = 'saved_reports';

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'title',
        'report_query',
        'file_path',
        'user_id',
    ];

    // رابط ملف PDF العام
    public function getPdfUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}