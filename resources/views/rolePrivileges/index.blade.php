@extends('layouts.layout')

@section('content')
<?php 
     $privilegeArr = isset( $result['privileges']['display_name'])? $result['privileges']['display_name']:array();
?>
<!-------------------------------- 
	Author : Ashish Barick
	Module : Roles & Privileges
 --------------------------------->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container">
            <div class="container-fluid">
            	<div class="row">
		          <div class="col-12">
		            <!-- Role box -->
		            <div class="card card-outline card-primary">
		            	<div class="card-header">
		                	<h2 style="font-size: 1.75rem;" class="card-title">Roles & Privileges</h2>
		                	<div class="card-tools">
			                  	<button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
			                    <i class="fas fa-minus" aria-hidden="true"></i>
			                  	</button>
		                	</div>
		              	</div>
		              	<div class="card-body">
		              		<form id="frmRole" name="frmRole">
				              	<div class="row">
					              	<div class="col-md-2">
					              		<label>Role : </label>
					              	</div>
					              	<div class="col-md-6">
						                <div class="form-group">
						                 	<select class="form-control select2" id="cmbRole" name="cmbRole"  style="width: 100%;">
							                    <option value="">Select Role</option>
							                    @foreach($result[0]['role'] as $data)
							                    <option value="{{ $data->id }}">{{$data->name}}</option>
							                    @endforeach
						                  	</select>
						                </div>
						            </div>
						            <div class= "col-md-4">
						              	<div class="form-group" style="cursor: pointer;">
						              		
					            				<span id="btnRoleAdd"><i class="fas fa-2x fa-plus-circle tooltips text-primary" data-placement="top" title="Add Roles & Privilege" aria-hidden="true"></i></span>&nbsp;&nbsp;
					            			
							            		<span id="btnRoleEdit"><i class="fas fa-2x fa-edit tooltips text-warning" data-placement="top" title="Update Roles & Privilege" id="btnRoleEdit" aria-hidden="true"></i></span> &nbsp;&nbsp;
							            
							            		<span id="btnRoleDelete"><i class="fas fa-2x fa-trash tooltips text-danger" data-placement="top" title="Delete Roles & Privilege" id="btnRoleDelete" aria-hidden="true"></i></span>
							            	
							            </div>
							        </div>
						        </div>	<!-- End Role Row -->	
						          <!--  Vessels Modal for ADD/EDIT -->
					            <div class="modal fade" id="modal-role">
					                <div class="modal-dialog">
					                    <div class="modal-content">
					                        <div class="modal-header">
					                            <h4 class="modal-title" id="mdlRoleTitle">Add Role</h4>
					                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					                                <span aria-hidden="true">&times;</span>
					                            </button>
					                        </div>

					                        <div class="modal-body">
					                            
				                                <input type="hidden" id="id" name="id">
				                                <div class="form-group">
				                                    <label for="VesselsInput">Role <span class="text-danger">*</span></label>
				                                    <input type="text" class="form-control tooltips" id="name" name="name" placeholder="Enter Role" data-placement="top" >
				                                </div>
				                                
				                                <div class="modal-footer justify-content">
				                                	<div class="form-group">
					                                    <button type="button" class="icon-button" data-dismiss="modal" title="Cancel"><i class="fas fa-2x fa-times-circle tooltips text-danger" aria-hidden="true"></i></button>
					                                    <button type="Submit" class="icon-button" data-placement="top" title="Save" id="btnRoleSubmit"><i class="fas fa-2x fa-save tooltips text-success" aria-hidden="true"></i></button>
					                                </div>
				                                </div>
					                        </div>
					                    </div>
					                    <!-- /.modal-content -->
					                </div>
					                <!-- /.modal-dialog -->
					            </div>
					            <!-- /.Vessels Modal -->
					        </form>		            
				        </div>
		              <!-- /. Role card-body -->
		            </div>
		            <!-- /.Role card -->
		          </div>
		        </div><!--/. Role Row -->
		       
		        <!-- Privileges Part -->
		        <form id="frmPrivilege" name="frmPrivilege">
	                <div class="row">
	                    <div class="col-12">
	                        <div class="card card-primary">
	                            <div class="card-header ">
	                                <div class="row">
	                                	<div class="col-6 text-center" style="border-right: solid 1px #D3D3D3;">
	                                		<label>Screen</label>
	                                	</div>
	                                	<div class="col-6 text-center">
	                                		<label>Privilege</label>
	                                	</div>
	                                </div>

	                            </div>
	                            
	                            <!-- /.card-header -->
	                            <div class="card-body">
									<div class="col-12" style="border-bottom: solid 1px #D3D3D3;">
	                                	@foreach($result[1]['menu'] as $parent)
											<div class="card">
												<div class="card-header">
													<div class="card-title">{{ $parent->display_name }}</div>
												</div>
												<div class="card-body">
													@foreach($parent->children as $child)
													<div class="row">
														<!-- Screen Body -->
														<div class="col-6"  style="border-right: solid 1px #D3D3D3;border-bottom: solid 1px #D3D3D3;">
															<label>{{ $child->display_name }}</label>
														</div>
														<!-- /Screen Body End -->
														<!-- Privileges Body -->
														
														<div class="col-6 text-center" style="border-bottom: solid 1px #D3D3D3;">
															@foreach($child->subChild as $subchild)
															<div class="row">
																<div class="col-6">
																	<label>{{ $subchild->display_name }}</label>
																</div>
																<div class="col-6">
																	<div class="form-group clearfix">
																		<div class="icheck-primary d-inline">
																			<input type="checkbox" class="chkPrivilege" id="chk{{$subchild->id}}" name= "chkPrivilege[]" value="{{$parent->id.','. $child->id.','.$subchild->id}}"> 
																		</div>  
																	</div>
																</div>
															</div>
															@endforeach
														</div>
														
														<!-- /Privileges Body End -->
													</div>
													@endforeach
												</div>
											</div>
	                                	@endforeach
	                                </div>
	                                <!-- /Privileges Row -->
	                            </div>
	                            <!-- /.card-body -->
	                        </div>
	                        <!-- /.card -->
	                        <div class="card">
	                        	<div class="card-body">
	                        		<div class="row text-center">
									
	                        			<button class="btn btn-primary btn-block" type="button" id="btnSubmitPrivilege" >Submit</button>
	                        	
									</div>
	                        	</div>
	                        </div>
	                    </div>
	                    <!-- /.col -->
	                </div>
	                <!-- /.row -->
	            </form><!-- /Privilege Form -->
            </div>
            <!-- /.container-fluid -->
          
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection

@push('script')
<script src="{{js('roleprivileges_js')}}"></script>
@endpush