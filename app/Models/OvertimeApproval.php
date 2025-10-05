<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'overtime_id',
        'approver_id',
        'status',
        'notes'
    ];

    public function overtime()
    {
        return $this->belongsTo(Overtime::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}