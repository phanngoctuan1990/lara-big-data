<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingExport extends Model
{
    use HasFactory;

    const EXPORTED_STATUS = 1;

    protected $table = 'tracking_export';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_name',
        'status',
        'batch_process_chunk_id',
        'batch_write_csv_id',
    ];
}
