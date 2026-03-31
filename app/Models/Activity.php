<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'registration_deadline',
        'image',
        'status',
        'user_id',
        'max_participants',
        'edit_payload' 
    ];
    protected $casts = [
        'edit_payload' => 'array',
    ];

    /**
     * บอก Laravel ว่ากิจกรรมนี้ "เป็นของ" User คนไหน
     */
    public function tags()// เอาไว้เพิพ่ม tag
    {
        return $this->belongsToMany(Tag::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}