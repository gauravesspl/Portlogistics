<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Vessel\VesselRepository;
use App\Http\Requests\VesselRequest;
use Illuminate\Http\Request;
use App\Http\Requests\VesselAutocompleteRequest;

class VesselController extends BaseController
{
    protected $vesselRepository;
    public function __construct(VesselRepository $vesselRepository)
    {
        $this->vesselRepository = $vesselRepository;
    }
    /**
    * @OA\Get(
    *   path="/vessel",
    *   tags={"Vessel"},
    *   summary="Get list of vessels",
    *   description="Returns list of vessels",
    *   @OA\Response(
    *       response=200,
    *       description="Success",
    *       @OA\MediaType(
    *           mediaType="application/json",
    *        )
    *       ),
    *   security={{ "apiAuth": {} }}
    *     )
    */
    public function getAllVessel(Request $request)
    {
        $allInput = $request->all();
        $param  = $this->getAuth($allInput);
        $param['view'] = 'vessel.index';
        $vessel = $this->vesselRepository->getAllVessel($param);
        $vessel['privileges'] =  isset($allInput['privilege_array'])?$allInput['privilege_array'] : "";
        if(!$vessel['status']){
           return $this->sendError($vessel,$vessel['message'], $param);
        }
        return $this->sendResponse($vessel,$vessel['message'], $param);
    }
    /**
     * @OA\Post(
     ** path="/vessel",
     *  tags={"Vessel"},
     *  summary="Vessel",
     *  description="Add vessel",
     *  @OA\RequestBody(
     *  required=true,
     *  description="Pass vessel details",
     *  @OA\JsonContent(
     *  required={"name","description","loa","beam","draft"},
     *  @OA\Property(property="name", type="string", format="text", example="Iron Ore Vessel"),
     *  @OA\Property(property="description", type="string", format="text", example="Granite vessel"),
     *  @OA\Property(property="loa", type="number", format="double", example=132.2),
     *  @OA\Property(property="beam", type="number", format="double", example=111.5),
     *  @OA\Property(property="draft", type="number", format="double", example=452.8),
     *  ),
     * ),
     *  @OA\Response(
     *  response=200,
     *  description="Success",
     *  @OA\MediaType(
     *  mediaType="application/json",
     *  )
     *  ),
     * security={{ "apiAuth": {} }}
     *)
     */
    public function storeVessel(VesselRequest $request)
    {
        $allInput = $request->all();
        $param  = $this->getAuth( $allInput);
        $allInput['created_by'] = $param['user_id'];
        $allInput['connection'] = $param['connection'];
        $response = $this->vesselRepository->store($allInput);
        if(!$response['status']){
            return $this->sendError($response,$response['message'],$param);
        }
        return $this->sendResponse([],$response['message'],$param);  
    }
    /**
     * @OA\Put(
     ** path="/vessel/{id}",
     *  tags={"Vessel"},
     *  summary="Vessel",
     *  description="Update vessel details",
     *  @OA\Parameter(
     *    description="ID of vessel",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *  @OA\Schema(
     *    type="integer",
     *    format="int64"
     *   )
     * ),
     *  @OA\RequestBody(
     *  required=true,
     *  description="Pass vessel details",
     *  @OA\JsonContent(
     *  required={"name","description","loa","beam","draft"},
     *  @OA\Property(property="name", type="string", format="text", example="Iron Ore Vessel"),
     *  @OA\Property(property="description", type="string", format="text", example="Granite vessel"),
     *  @OA\Property(property="loa", type="number", format="double", example=132.2),
     *  @OA\Property(property="beam", type="number", format="double", example=111.5),
     *  @OA\Property(property="draft", type="number", format="double", example=452.8),
     *  ),
     * ),
     *  @OA\Response(
     *  response=200,
     *  description="Success",
     *  @OA\MediaType(
     *  mediaType="application/json",
     *  )
     *  ),
     * security={{ "apiAuth": {} }}
     *)
     */
    public function updateVessel(VesselRequest $request)
    {
        $allInput = $request->all();
        $param  = $this->getAuth($allInput);
        $allInput['updated_by'] = $param['user_id'];
        $allInput['connection'] = $param['connection'];
        if(empty($allInput['description'])){
            $allInput['description']=null;
        }
        if(empty($allInput['loa'])){
            $allInput['loa']=null;
        }
        if(empty($allInput['beam'])){
            $allInput['beam']=null;
        }
        if(empty($allInput['draft'])){
            $allInput['draft']=null;
        }
        $response = $this->vesselRepository->update($allInput);
        if(!$response['status']){
            return $this->sendError($response['result'],$response['message'],$param);
        }
        return $this->sendResponse($response['result'],$response['message'],$param);           
    }
    /**
     * @OA\Delete(
     ** path="/vessel/{id}",
     *  tags={"Vessel"},
     *  summary="Vessel",
     *  description="Delete Vessel by id",
     *  @OA\Parameter(
     *    description="ID of vessel",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *  @OA\Schema(
     *    type="integer",
     *    format="int64"
     * )
     * ),
     *  @OA\Response(
     *  response=200,
     *  description="Success",
     *  @OA\MediaType(
     *  mediaType="application/json",
     *  )
     *  ),
     * security={{ "apiAuth": {} }}
     *)
     */ 
    public function destroyVessel(VesselRequest $request)
    {
        $allInput = $request->all();
        $param  = $this->getAuth($allInput);//in post you will need to pass like $this->getAuth($request->all());
        $allInput['connection'] = $param['connection'];
        $response = $this->vesselRepository->destroy($allInput);
        if(!$response['status']){
            return $this->sendError($response['result'],$response['message'],$param);
        }
        return $this->sendResponse($response['result'],$response['message'],$param);          
    }
     /**
     * @OA\Get(
     ** path="/vessel/{id}",
     *  tags={"Vessel"},
     *  summary="Vessel",
     *  description="Edit Vessel by id",
     * *  @OA\Parameter(
     *    description="ID of vessel",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *  @OA\Schema(
     *    type="integer",
     *    format="int64"
     * )
     * ),
     *  @OA\Response(
     *  response=200,
     *  description="Success",
     *  @OA\MediaType(
     *  mediaType="application/json",
     *  )
     *  ),
     * security={{ "apiAuth": {} }}
     *)
     */  
    public function editVessel(VesselRequest $request)
    {
        $allInput = $request->all();
        $param  = $this->getAuth($allInput); //in post you will need to pass like $this->getAuth($request->all());
        $allInput['connection'] = $param['connection'];
        $response = $this->vesselRepository->edit($allInput);
        if(!$response['status']){
            return $this->sendError($response['result'],$response['message'],$param);
        }
        return $this->sendResponse($response['result'],$response['message'],$param);
    }
    /**
    * @OA\GET(
    * path="/vessel/sorted-list/{keyword}",
    * tags={"Vessel"},
    * summary="Get suggested vessel list by keyword",
    * @OA\RequestBody(
    *    required=true,
    *    description="Pass search keyword",
    *    @OA\JsonContent(
    *       required={"keyword"},
    *       @OA\Property(property="keyword", type="string", format="string", example="abc"),
    *    ),
    * ),
    * @OA\Response(
    *      response=200,
    *      description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
    * ),
    * security={{ "apiAuth": {} }}
    *)
    **/
    public function autocomplete(VesselAutocompleteRequest $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        if(isset($inputs['keyword']) && !empty($inputs['keyword'])) {
            $response = $this->vesselRepository->searchVesselsByKeyword($inputs);
            if($response['status']) {
                return $this->sendResponse($response['result'],'Vessel lists fetched successfully',$auth);
            } else {
                return $this->sendError($response['result'],$response['result'],$auth);
            }
        } else {
            return $this->sendError('No keyword found','No keyword found',$auth);
        }
    }
}
