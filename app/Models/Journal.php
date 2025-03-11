<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Journal extends Model
{

    use HasUuids, SoftDeletes;

    protected $guarded = [];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'journal_tags', 'journal_id', 'tag_id');
    }

    public function getCoverUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return env('APP_ENV') == 'local' ? url('storage/' . $value) : url('public/storage/' . $value);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('M j, Y');
    }

    public function getContentAttribute($value)
    {
        return Str::markdown($value);
    }
}
