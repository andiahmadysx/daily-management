<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes, HasFactory, HasUuids;

    protected $guarded = [];



    public function getPriorityColorAttribute()
    {
        return match ($this->attributes['priority']) {
            'high' => 'tomato',
            'medium' => 'orange',
            'low' => 'seagreen',
            default => 'var(--primary)',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Get the child tasks.
     */
    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }
}
