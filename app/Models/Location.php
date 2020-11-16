<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true"),
 * @OA\Property(property="created_by", type="integer", format="int", description="Location created by user_id", readOnly="true"),
 * @OA\Property(property="updated_by", type="integer", format="int", description="Location updated by user_id", readOnly="true"),
 * @OA\Property(property="location", type="string", format="text", description="Location name", readOnly="true"),
 * @OA\Property(property="description", type="string", format="text", description="Location description", readOnly="true"),
 * @OA\Property(property="type", type="string", format="text", description="Location type", readOnly="true"),
 * @OA\Property(property="is_active", type="integer", format="int", description="Location status", readOnly="true"),
 * )
 * Class Shift
 *
 * @package App\Models
 */
class Location extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['location', 'description', 'type', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by'];

    protected $hidden = ['pivot'];
    
    public function challan() {
        return $this->hasMany('App\Models\Challan', 'origin_id');
    }

    
}
