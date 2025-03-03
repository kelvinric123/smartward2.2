<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'specialty',
        'contact_number',
        'status',
        'notes',
        'email',
        'office_location',
    ];
    
    /**
     * Get the possible status values for a consultant.
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            'Available',
            'On Call',
            'In Surgery',
            'Unavailable',
            'On Leave',
        ];
    }
    
    /**
     * Get the patients associated with this consultant.
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'consultant_patient')
            ->withTimestamps();
    }
}
