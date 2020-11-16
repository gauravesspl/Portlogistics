<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true"),
 * @OA\Property(property="created_by", type="integer", format="int", description="Truck created by user_id", readOnly="true"),
 * @OA\Property(property="updated_by", type="integer", format="int", description="Truck updated by user_id", readOnly="true"),
 * @OA\Property(property="truck_no", type="string", format="text", description="Truck number", readOnly="true"),
 * @OA\Property(property="truck_company_id", type="integer", format="int", description="Trucking company id", readOnly="true"),
 * @OA\Property(property="is_active", type="integer", format="int", description="Truck status", readOnly="true"),
 * )
 * Class Truck
 *
 * @package App\Models
 */

class Truck extends Model
{
    /**
     * @var array
     */

    protected $fillable = ['truck_company_id', 'truck_no', 'created_at', 'created_by', 'updated_at', 'updated_by'];
    protected $hidden = ['pivot'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */


   
    
    public function challan() {
        return $this->hasMany('App\Models\Challan');

    }
    public function truckCompany()
    {
        return $this->belongsTo('App\Models\TruckCompany');
    }
    public function plannedTrucks() {
        return $this->hasMany('App\Models\PlanTruck');

    }
    
    
}
