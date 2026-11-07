<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// الطلبات التي كتبها المدير في صفحة التقرير المخصص
class ReportRequest extends Model
{
    protected $table = 'report_requests';

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'user_id',
        'report_query',
    ];
}