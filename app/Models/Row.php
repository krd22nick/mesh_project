<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'date'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Format the date attribute to d.m.Y format.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d.m.Y');
    }

    /**
     * Scope to group rows by date and aggregate names.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupedByDate($query)
    {
        return $query->select('date', \DB::raw('GROUP_CONCAT(name) as names'))
            ->groupBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->date->format('d.m.Y'),
                    'names' => explode(',', $row->names),
                ];
            });
    }

}
