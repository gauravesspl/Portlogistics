
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
                <div class="card">
                    <div class="card-header border-0">
                        <div class="header-details">
                            <div class="name-area">
                            <h2 class="m-0 text-dark">Departments</h2>
                            </div>
                            <div class="action-area">
                                <form action="{{url('csv-export')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="key" value = "Department">
                                    <button  type="submit" class="btn btn-primary tooltips "><i aria-hidden="true" class="fa fa-file-excel"></i> Export</button>
                                </form>
                                <button type="button" id="add_department_btn" class="btn btn-success float-right tooltips "><i aria-hidden="true" class="fa fa-plus"></i> Add Department</a>                              
                            </div> 
                            </div>
                        </div>
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" id="dept_id">
                            <!-- /.card-header -->
                            <div class="col-md-12 table p-4">
                                <table id="departments" class="table table-striped">
                                <caption class="d-none">Department List</caption>
                                    <thead style="text-align: center;">
                                        <tr>
                                            <th scope="col">Department</th>
                                            <th scope="col">Description</th>
                                            <th scope="col" hidden="">id</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: center;">
                                    @foreach($result['department'] as $department)
                                    <tr>
                                        <td>{{ $department->name }}</td>                                    
                                        <td>{{ $department->description }}</td> 
                                        <td hidden="">{{$department->id}}</td>
                                        <td>
                                            <a href="javascript:void(0);" onclick="return editDepartment('{{ $department->id }}')" data-target="top" class="edit tooltips" title="Update Department"><i aria-hidden="true" class="fas fa-2x fa-edit text-success"></i></a> &nbsp;&nbsp;
                                            <i aria-hidden="true" class='fas fa-trash fa-2x text-danger tooltips delete' data-placement='top' title='Delete Department' style='cursor:pointer'></i>                           
                                        </td>    
                                    </tr>
                                    @endforeach                             
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
    </section>
    @endif
    <!-- /.content -->
</div>
<div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add Department</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="add_department" method="post">
                <input type="hidden" name="id" id="hidden_id">
                <div class="modal-body">                
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Department<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Department">
                        </div>
                        <div class="form-group">
                            <label for="description">Description<span class="text-danger"></span></label>
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter Description"></textarea>
                        </div>
                    </div>
                    <!-- /.card-body -->              
                </div>
                <div class="modal-footer justify-content">
                    <button type="button" class="icon-button" data-dismiss="modal" title="Cancel"><i aria-hidden="true" class="fas fa-2x fa-times-circle tooltips text-danger"></i></button>
                    <button id="department_btn" type="submit" class="icon-button" title="Save"><i aria-hidden="true" class="fas fa-2x fa-save tooltips text-success"></i></button>
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
<script src="{{ js('department_js') }}"></script>
@endpush
