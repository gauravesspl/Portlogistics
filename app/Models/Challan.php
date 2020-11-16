<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * @OA\Property(property="plan_id", type="integer", format="int", description="Plan Id"),
 * @OA\Property(property="origin_id", type="integer", format="int", description="Origin Id"),
 * @OA\Property(property="destination_id", type="integer", format="int", description="Destination Id"),
 * @OA\Property(property="truck_id", type="integer", format="int", description="Truck Id"),
 * @OA\Property(property="shift_id", type="integer", format="int", description="Shift Id"),
 * @OA\Property(property="cargo_id", type="integer", format="int", description="Cargo Id"),
 * @OA\Property(property="consignee_id", type="integer", format="int", description="Consignee Id"),
 * @OA\Property(property="challan_no", type="string", format="string", description="Challan No."),
 * @OA\Property(property="barcode_path", type="string", format="string", description="Path of Barcode"),
 * @OA\Property(property="pdf_path", type="string", format="string", description="Path of Challan Pdf"),
 * @OA\Property(property="type", type="integer", format="int", description="BtoP or PtoP Plan Type"),
 * @OA\Property(property="status", type="integer", format="int", description="Unload Pending/Unloaded"),
 * @OA\Property(property="is_deposit", type="integer", format="int", description="Reconciled Status"),
 * @OA\Property(property="is_scanned", type="integer", format="int", description="Scanned Status"),
 * @OA\Property(property="loaded_at", type="string", format="date-time", description="Challan Loaded At", readOnly="true"),
 * @OA\Property(property="loaded_by", type="integer", format="int", description="Challan Loaded By User Id", readOnly="true"),
 * @OA\Property(property="unloaded_at", type="string", format="date-time", description="Challan Unloade At", readOnly="true"),
 * @OA\Property(property="unloaded_by", type="integer", format="int", description="Challan Unloaded By User Id", readOnly="true"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true"),
 * @OA\Property(property="created_by", type="integer", format="int", description="Shift created by user_id", readOnly="true"),
 * @OA\Property(property="updated_by", type="integer", format="int", description="Shift updated by user_id", readOnly="true"),
 * )
 * Class Challan
 *
 * @package App\Models
 */

class Challan extends Model
{
    /**
     * @var array
     */
    public $fillable = ['plan_id', 'origin_id', 'destination_id', 'truck_id', 'shift_id', 'cargo_id', 'trip_started_at', 'trip_ended_at', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cargo()
    {
        return $this->belongsTo('App\Models\Cargo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
        return $this->belongsTo('App\Models\Shift');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function truck()
    {
        return $this->belongsTo('App\Models\Truck');
    }

    public function origin()
    {
        return $this->belongsTo('App\Models\Location', 'origin_id');
    }
    public function destination()
    {
        return $this->belongsTo('App\Models\Location', 'destination_id');
    }

    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }
}