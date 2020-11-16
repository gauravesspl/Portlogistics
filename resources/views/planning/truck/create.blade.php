@extends('layouts.layout')
@section('content')

<div class="content-wrapper">
     <div class="content-header">
      <!--  <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                </div>

            </div>
        </div>-->
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card">
                            <div class="card-header border-0">
                            <div class="header-details">
                            <div class="name-area">
                            <h2 class="m-0 text-dark">Add Truck</h2>
                            </div>
                            <div class="action-area">
                            </div>
                            </div>
                    </div>
                        <form id="addTruckForm" method="post" action="{{ url('plan/truck') }}">
                            @csrf
                            <input type="hidden" name="plan_id" id="plan_id" value="{{ $response['planning_details']['result']['id'] }}">
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Vessel</th>
                                            <th>Berth</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Cargo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $response['planning_details']['result']['vessel']['name'] }}</td>
                                            <td>{{ $response['planning_details']['result']['location']['location'] }}</td>
                                            <td>{{ date('d/m/Y H:i',strtotime($response['planning_details']['result']['date_from'])) }}</td>
                                            <td>{{ date('d/m/Y H:i',strtotime($response['planning_details']['result']['date_to'])) }}</td>
                                            <td>{{ $response['planning_details']['result']['cargo']['name'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>


                                <table class="table" style="width: 60%;" id="planTruckDetailTbl">
                                    <thead>
                                        <tr>
                                            <th width="40%">Truck Number</th>
                                            <th width="40%">Trucking Company</th>
                                            <th width="20%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($response['truck_details']['result']) > 0)
                                        @foreach($response['truck_details']['result'] as $key => $truck_details)
                                        <tr class="planTruckDetailTr">
                                            <td>
                                                <select class="form-control select2 cmb_truck" disabled="">
                                                    <option value="">Select Truck</option>
                                                    @if(count($response['all_truck_details']['trucks']) > 0)
                                                        @foreach($response['all_truck_details']['trucks'] as $all_truck)
                                                        <option trucking_company_id="{{ $all_truck->truck_company_id }}" trucking_company_name="{{ $all_truck->name }}" value="{{ $all_truck->id }}" {{(isset($truck_details->truck_id) && !empty($truck_details->truck_id) && ($truck_details->truck_id == $all_truck->id)) ? 'selected' : ''}}>{{ $all_truck->truck_no }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>                                                
                                                <input type="hidden" class="hdn_truck_id" id="hdn_{{ $key }}" name="trucks[{{ $key }}][truck_id]" value="{{(isset($truck_details->truck_id) && !empty($truck_details->truck_id)) ? $truck_details->truck_id : ''}}" />
                                                <input type="hidden" class="truck_company_id" name="trucks[{{ $key }}][truck_company_id]" value="{{ $truck_details->truck->truckCompany->id }}">
                                            </td>
                                            <td>
                                                <span class="pl-2 trucking_company">{{ $truck_details->truck->truckCompany->name }}</span>
                                            </td>
                                            <td>
                                                <input type="hidden" value="{{(isset($truck_details['id']) && !empty($truck_details['id'])) ? $truck_details['id'] : ''}}" name="trucks[{{$key}}][id]" class="hdnPlanDetailId"/>
                                                <a href="javascript:void(0);" class="add_plan_details pl-4"><i class="fa fa-2x fa-plus-circle text-success" aria-hidden="true"></i></a>
                                                <a href="javascript:void(0);" class="remove_plan_details pl-2"  status_id="{{$truck_details->status}}"><i class="fas fa-2x fa-trash text-danger" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr class="planTruckDetailTr">
                                            <td>
                                                <select class="form-control uppercase select2 cmb_truck truck" name="trucks[0][truck_id]">
                                                    <option value="">Select Truck</option>
                                                    @if(count($response['all_truck_details']['trucks']) > 0)
                                                        @foreach($response['all_truck_details']['trucks'] as $all_truck)
                                                        <option  trucking_company_id="{{ $all_truck->truck_company_id }}" trucking_company_name="{{ $all_truck->name }}" value="{{ $all_truck->id }}">{{ $all_truck->truck_no }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <input type="hidden" class="truck_company_id" name="trucks[0][truck_company_id]">
                                            </td>
                                            <td>
                                                <span class="trucking_company"></span>
                                                <span class="truckDetails"></span>
                                            </td>
                                            <td>
                                                <input type="hidden" value="" name="trucks[0][id]" class="hdnPlanDetailId"/>
                                                <a href="javascript:void(0);" class="add_plan_details pl-4"><i class="fa fa-2x fa-plus-circle text-success" aria-hidden="true" title="Add More"></i></a>
                                                <a href="javascript:void(0);" class="remove_plan_details pl-2" status_id=""><i class="fas fa-2x fa-trash text-danger" aria-hidden="true" title="Delete"></i></a>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>

                                </table>
                                <div class="modal-footer justify-end">
                                <button id="btn_add_truck" type="submit" class="icon-button btn-transparent"><i class="fas fa-2x fa-save tooltips text-success"></i></button>
                                <a href="{{url('plans')}}" class="icon-button btn-transparent"><i class="fas fa-2x fa-times-circle tooltips text-danger "></i></a>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@push('script')
<script type='text/javascript'>
    var token = '{{ csrf_token() }}';
    var truck_company = <?php echo json_encode($response['truck_companies']['truck_company']) ?>;
    console.log(truck_company);
</script>
<script src="{{ js('add_truck_js') }}"></script>
@endpush

