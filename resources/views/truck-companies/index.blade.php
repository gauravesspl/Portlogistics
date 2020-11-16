
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

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="container-fluid">
                <div class="row">
                    <input type="hidden" id="truckComp_id">   
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-0">
                            <div class="header-details">
                            <div class="name-area">
                            <h2 class="m-0 text-dark">Trucking Companies</h2>
                            </div>
                            <div class="action-area">
                                <form action="{{url('csv-export')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="key" value = "Truck Company">
                                    <button  type="submit" class="btn btn-primary float-right tooltips "><i class="fa fa-file-excel" aria-hidden="true"></i> Export</button>
                                </form>
                                <button id="add_truck_company_btn" type="button" class="btn btn-success tooltips " ><i class="fa fa-plus" aria-hidden="true"></i> Add New Company</button>                             
                            </div> 
                            </div>
                            </div>
                
                <!-- /.card-header -->
                <div class="col-md-12 table p-4">
                    <table id="truck_company" class="table  table-striped">
                    <caption class="d-none">Trucking Company List</caption>
                        <thead style="text-align: center;">
                            <tr>
                                <th scope="col">Trucking Company</th>
                                <th scope="col">Email</th>                                            
                                <th scope="col">Phone</th>
                                <th scope="col">Contact Name</th>
                                <th scope="col">Contact Phone</th>
                                <th scope="col" hidden="">Action</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                            @foreach($result['truck_company'] as $result)
                            <tr>
                                <td>{{ $result->name }}</td>                                    
                                <td>{{ $result->email }}</td>    
                                <td>{{ $result->mobile_no }}</td>    
                                <td>{{ $result->contact_name }}</td>    
                                <td>{{ $result->contact_mobile_no == 0 ? '' : $result->contact_mobile_no}}</td>   
                                <td hidden="">{{$result->id}}</td>     
                                <td>
                                    <a href="javascript:void(0);" onclick="editTruckCompany('{{ $result->id }}')" data-toggle="tooltip" class="edit tooltips" title="Update Trucking Company"><i aria-hidden="true" class="fas fa-2x fa-edit text-success"></i></a> &nbsp;&nbsp;
                                    <i aria-hidden="true" class='fas fa-2x fa-trash text-danger tooltips delete' data-placement='top' title='Delete Trucking Company' style='cursor:pointer'></i>
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
<div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add Trucking Company</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
              <form id="add_truck_company" method="post">
                  <input type="hidden" name="id" id="hidden_id">
            <div class="modal-body">                
                <div class="card-body">
                    <div class="form-group">
                        <label for="truck_company">Trucking Company<span class="text-danger">*</span> </label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Trucking Company">
                    </div>
                    <div class="form-group">
                        <label for="email">Email<span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                    </div>
                    <div class="form-group">
                        <label for="mobile_no">Phone<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mobile_no" name="mobile_no" placeholder="Enter Phone Number">
                    </div>
                    <div class="form-group">
                        <label for="contact_name">Contact Person</label>
                        <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Enter Contact Person">
                    </div>
                    <div class="form-group">
                        <label for="contact_mobile_no">Contact Phone</label>
                        <input type="text" class="form-control" id="contact_mobile_no" name="contact_mobile_no" placeholder="Enter Contact Phone">
                    </div>
                </div>
                <!-- /.card-body --> 
                
              
            </div>
            <div class="modal-footer justify-content">
              <button type="button" class="icon-button" data-dismiss="modal" title="Cancel"><i aria-hidden="true" class="fas fa-2x fa-times-circle tooltips text-danger"></i></button>
              <button id="truck_company_btn" type="submit" class="icon-button" title="Save"><i aria-hidden="true" class="fas fa-2x fa-save tooltips text-success"></i></button>
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
<script src="{{ js('truck_company_js') }}"></script>
@endpush
