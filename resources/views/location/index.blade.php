@extends('layouts.layout')

@section('content')
<?php 
     $privilegeArr = isset( $result['privileges']['display_name'])? $result['privileges']['display_name']:array();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
    </div>
    <!-- /.content-header -->
  @if($status!='failed')
    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="container-fluid">
                <div class="row">

                    <input type="hidden" id="location_id">        
                    <div class="col-md-12">
                    <div class="card">
                            <div class="card-header border-0">
                            <div class="header-details">
                            <div class="name-area">
                            <h2 class="m-0 text-dark">Locations</h2>
                            </div>
                            <div class="action-area">
                                <form action="{{url('csv-export')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="key" value = "Location">
                                    <button  type="submit" class="btn btn-primary float-right tooltips "><i class="fa fa-file-excel" aria-hidden="true"></i> Export</button>
                                </form>

                                <button id="btnLocationsAdd" type="button" class="btn btn-success tooltips " data-toggle="modal" data-target="#modal-locations" data-placement="top" ><i class="fa fa-plus" aria-hidden="true"></i> Add New Location</button>   
                            </div> 
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="col-md-12 table p-4">
                        <table id="dtLocation" class="table  table-striped">
                            <caption>Location Lists</caption>
                            <thead style="text-align: center;">
                                <tr>
                                    <th scope="column">Location</th>
                                    <th scope="column">Description</th>
                                    <th scope="column">Type</th>
                                    <th hidden="" scope="column">Action</th>
                                    <th scope="column">Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody style="text-align: center;">
                            @foreach($result['location'] as $result)
                            <tr>
                                <td>{{ $result->location }}</td>                                    
                                <td>{{ $result->description }}</td>                                                                        
                                <td>{{ $result->type == 'P'?'Plot':'Berth' }}</td>
                                <td hidden="">{{$result->id}}</td>    
                                <td style="text-align: center;">

                                     <a href="javascript:void(0);" onclick="return editLocation('{{ $result->id }}')" data-target="top" class="edit tooltips" title="Update Location"><i class="fas fa-2x fa-edit text-success" aria-hidden="true"></i></a> &nbsp;&nbsp;

                                     <i class='fas fa-2x fa-trash text-danger tooltips delete' data-placement='top' title='Delete Location' aria-hidden="true" style='cursor:pointer'></i>

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
            </div>
            <!-- /.container-fluid -->
            <!--  Locations Modal for ADD/EDIT -->
            <div class="modal fade" id="modal-locations">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="mdlLocationsTitle">Add Location</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form id="frmLocations" name="frmLocations">
                                <input type="hidden" id="hidden_id" name="id">
                                <div class="form-group">
                                    <label for="LocationsInput">Location <span class="text-danger">*</span> </label>
                                    <input type="text" class="form-control " id="location" name="location" placeholder="Enter Location">
                                </div>
                                <div class="form-group">
                                    <label for="DescriptionInput">Description</label>
                                    <textarea class="form-control " id="description" name="description" placeholder="Enter Description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="TypeInput">Type <span class="text-danger">*</span></label>
                                    <div class="radio">
                                        <label for="TypeInputValue">
                                        <input type="radio" id="typeB" name="type"  value="B">  Berth
                                        <input type="radio" id="typeP" name="type"  value="P">  Plot
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content">
                                    <button type="button" class="icon-button" data-dismiss="modal" title="Cancel"><i class="fas fa-2x fa-times-circle tooltips text-danger" aria-hidden="true"></i></button>
                                    <button type="Submit" class="icon-button" data-placement="top" id="btnLocationsSubmit" title="Save"><i class="fas fa-2x fa-save tooltips text-success" aria-hidden="true"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.Locations Modal -->
        </div>
    </section>
    <!-- /.content -->
    @endif
</div>
<!-- /.content-wrapper -->
@endsection

@push('script')
<script src="{{js('location_js')}}"></script>
@endpush