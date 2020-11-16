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
                    <div class="col-12">                   
                        <div class="card">
                        <h2 style="
    padding: 1rem;
">Organization Information</h2>
                            <!--name and information card header-->                       
                            <form id="organisationForm"  novalidate="novalidate">
                                 <input type="hidden" name="org_type" value="org_info">
                                @csrf
                                <div class="card-header">
                                    <div class="col-sm-6">
                                        <h4>Name and Information &nbsp;&nbsp;
                                            
                                                <button type="button" class="btn tooltips" id="orgNameEdit" name="orgNameEdit" visibility="hidden" title="Update Organization" data-target="right"><i class="fas fa-pencil-alt" aria-hidden="true"></i></button>
                                                <button type="submit" class="btn tooltips" id="orgNameSave" name="orgNameSave" visibility="hidden" title="Update Organization" data-target="right"><i class="fas fa-save" aria-hidden="true"></i></button>
                                           
                                        </h4>    
                                    </div>
                                </div>
                                <!-- /.name and information card header -->
                                <fieldset id="disableOrg" disabled="disabled">
                                    <legend></legend>
                                    <!-- /.name and information card body -->
                                    <div class="row">
                                        <div class="col-sm-4">
                                    <div class="card-body">
                                        <input type="text" class="text-line" id="id" name="id" value="{{ $result['organization']['organization']->id }}" hidden/> 
                                        <div class="form-group">
                                            <label for="name">Organization Name</label><br>
                                            <input type="text" class="text-line w-75" id="name" name="name" value="{{ $result['organization']['organization']->name }}"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="mobile_no">Phone</label><br>
                                            <input type="text" class="text-line w-75" id="mobile_no" name="mobile_no" value="{{ $result['organization']['organization']->mobile_no }}"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label><br>
                                            <input type="text" class="text-line w-75" id="email" name="email" value="{{ $result['organization']['organization']->email }}" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Address</label><br>
                                            <input type="text" class="text-line w-75" id="address" name="address" value="{{ $result['organization']['organization']->address}}"/>
                                        </div>
                                    </div>  
                                        </div>
                                    <div class="col-sm-4">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="name">Primary Contact</label>
                                            </div>
                                            <div class="form-group">                                                
                                                <label for="primary_contact">Name</label><br>
                                                <input type="text" class="text-line w-75" id="primary_contact" name="primary_contact" value="{{ $result['organization']['organization']->primary_contact }}"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="primary_mobile_no">Phone</label><br>
                                                <input type="text" class="text-line w-75" id="primary_mobile_no" name="primary_mobile_no" value="{{ $result['organization']['organization']->primary_mobile_no }}"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="primary_email">Email</label><br>
                                                <input type="text" class="text-line w-75" id="primary_email" name="primary_email" value="{{ $result['organization']['organization']->primary_email }}"/>
                                            </div>
                                        </div>  
                                    </div>
                                        
                                        <div class="col-sm-4">
                                        <div class="card-body">
                                            <div class="form-group">
                                              <label for="name">Secondary Contact</label>  
                                            </div>                                            
                                            <div class="form-group">
                                                <label for="secondary_contact">Name</label><br>
                                                <input type="text" class="text-line w-75" id="secondary_contact" name="secondary_contact" value="{{ $result['organization']['organization']->secondary_contact }}"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="secondary_mobile_no">Phone</label><br>
                                                <input type="text" class="text-line w-75" id="secondary_mobile_no" name="secondary_mobile_no" value="{{ $result['organization']['organization']->secondary_mobile_no }}"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="secondary_email">Email</label><br>
                                                <input type="text" class="text-line w-75" id="secondary_email" name="secondary_email" value="{{ $result['organization']['organization']->secondary_email }}"/>
                                            </div>
                                        </div>  
                                    </div>
                                        
                                    </div>
                                    
                                    <!-- /.name and information card body -->
                                </fieldset>
                                
                                
                            </form>
                            <form id="currency" novalidate="novalidate">
                                @csrf
                                <input type="hidden" name="org_type" value="org_rate">
                                <!--currency and rates card header-->
                                <div class="card-header">
                                    <div class="col-sm-6">
                                        <h4>Currency and Rates &nbsp;&nbsp;
                                          
                                                <button type="button" class="btn tooltips" id="currencyEdit" name="currencyEdit" title="Update Organization Currency Rate" data-target="right"><i class="fas fa-pencil-alt" aria-hidden="true"></i></button>
                                                <button type="submit" class="btn tooltips" id="currencySave" name="currencySave" visibility="hidden" title="Update Organization Currency Rate" data-target="right"><i class="fas fa-save" aria-hidden="true"></i></button>
                                            
                                        </h4>                                       
                                    </div>
                                </div>
                                <!-- /.currency and rates card header -->
                                <fieldset id="disableCurrency" disabled="disabled">
                                    <legend></legend>
                                    <!-- /.currency and rates card body -->
                                    <div class="card-body">
                                    
                                        <div class="form-group">
                                            <input type="text" class="text-line" id="id" name="id" value="{{ $result['organization']['organization']->id}}" hidden/>
                                            <label for="currency_id">Currency</label><br>
                                            <select class="text-line" id="currency_id" name="currency_id">
                                                <option></option>
                                                @if(!empty($result['currencies']['currencies']))
                                                @foreach($result['currencies']['currencies'] as $currencies)
                                                    <option value="{{ $currencies ->id }}" {{(isset($result['organization']['organization']->currency_id) && !empty($result['organization']['organization']->currency_id) && ($result['organization']['organization']->currency_id == $currencies ->id)) ? 'selected' : ''}}>{{ $currencies ->currency }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="currency_code">Rates per Trip</label><br>
                                            <select  id="currency_code" name="currency_code" class="text-line readonly" disabled>
                                                <option></option>
                                                @if(!empty($result['currencies']['currencies']))
                                                @foreach($result['currencies']['currencies'] as $currencies)
                                                    <option value="{{ $currencies ->id }}" {{(isset($result['organization']['organization']->currency_id) && !empty($result['organization']['organization']->currency_id) && ($result['organization']['organization']->currency_id == $currencies ->id)) ? 'selected' : ''}}>{{ $currencies ->currency_code }}</option>
                                                @endforeach
                                                @endif
                                            </select> 
                                            <input type="text" class="text-line" id="rate_per_trip" name="rate_per_trip" value="{{ $result['organization']['organization']->rate_per_trip}}"/>  
                                        </div>
                                    </div>
                                    <!-- /.currency and rates card body -->
                                </fieldset>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
    </section>
    <!-- /.content -->
    @endif
</div>
@endsection

@push('style')
<link href="{{ css('text_line_css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{js('organization_js')}}"></script>
@endpush
