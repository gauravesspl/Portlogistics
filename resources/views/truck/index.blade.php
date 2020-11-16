
@extends('layouts.layout')

@section('content')
<?php 
    $privilegeArr = isset( $result['privileges']['display_name'])? $result['privileges']['display_name']:array();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header"></div>
    <!-- /.content-header -->
    @if($status!='failed')
    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="container-fluid">
                <div class="row">
                    <input type="hidden" id="truck_id">
                    <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="header-details">
                                <div class="name-area">
                                <h2 class="m-0 text-dark">Trucks</h2>
                                </div>
                                <div class="action-area">                              
                                    <form action="{{url('csv-export')}}" method="post">
                                        @csrf
                                        <input type="hidden" name="key" value = "Trucks">
                                        <button  type="submit" class="btn btn-primary float-right tooltips "><i aria-hidden="true" class="fa fa-file-excel"></i> Export</button>
                                    </form>                              
                                    <button id="add_trucks_btn" type="button" class="btn btn-success tooltips " ><i aria-hidden="true" class="fa fa-plus"></i> Add New Truck</button> 
                                </div> 
                            </div>
                        </div>
                    <!-- /.card-header -->
                    <div class="col-md-12 table p-4">
                        <table id="trucks" class="table  table-striped">
                            <caption class="d-none">Truck List</caption>
                            <thead style="text-align: center;">
                                <tr>
                                    <th scope="col">Truck/Dumper No</th>
                                    <th scope="col">Trucking Company</th>
                                    <th scope="col" hidden="">Action</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody style="text-align: center;">  
                            @foreach($result['trucks'] as $truck)
                            <tr>
                                <td>{{ strtoupper($truck->truck_no) }}</td>                                    
                                <td>{{ $truck->name }}</td> 
                                <td hidden="">{{$truck->id}}</td>    
                                <td>
                                    <a href="javascript:void(0);" onclick="return editTruck('{{ $truck->id }}')" data-toggle="tooltip" class="edit tooltips" title='Update Truck'><i aria-hidden="true" class="fas fa-2x fa-edit text-success"></i></a> &nbsp;&nbsp;                                 
                                    <i class='fas fa-2x fa-trash text-danger tooltips delete' data-placement='top' title='Delete Truck' style='cursor:pointer' aria-hidden="true"></i>                                  
                                </td>    
                             </tr>
                            @endforeach                
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
    </section>
    <!-- /.content -->
</div>
@endif
<div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add Trucks</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
              <form id="add_trucks" method="post">
                  <input type="hidden" name="id" id="hidden_id">
            <div class="modal-body"> 
                <div class="card-body">
                    <div class="form-group">
                        <label for="truck_no">Truck/Dumper No<span class="text-danger">*</span></label>
                        <input type="text" class="form-control uppercase" id="truck_no" name="truck_no" placeholder="Enter Trucks/Dumper No">
                    </div>
                    <div class="form-group">
                        <label for="truck_company_id">Trucking Company<span class="text-danger">*</span></label>
                        <select class="form-control" id="truck_company_id" name="truck_company_id">
                            <option value="">Select Trucking Company</option>
                            @if(!empty($result['trucking_company']))
                            @foreach($result['trucking_company'] as $trucking_company)
                                <option value="{{$trucking_company->id}}" {{(isset($result['trucks']->truck_company_id) && !empty($result['trucks']->truck_company_id) && ($result['trucks']->truck_company_id == $trucking_company->id)) ? 'selected' : ''}}>{{$trucking_company->name}}</option>
                            @endforeach
                            @endif                                                                  
                        </select>
                    </div>
                </div>
                <!-- /.card-body -->                
            </div>
            <div class="modal-footer justify-content">
              <button type="button" class="icon-button" data-dismiss="modal" title="Cancel"><i aria-hidden="true" class="fas fa-2x fa-times-circle tooltips text-danger"></i></button>
              <button id="trucks_btn" type="submit" class="icon-button" title="Save"><i aria-hidden="true" class="fas fa-2x fa-save tooltips text-success"></i></button>
            </div>
              </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<!-- /.content-wrapper -->

@endsection
@push('script')
<script src="{{ js('truck_js') }}"></script>
@endpush