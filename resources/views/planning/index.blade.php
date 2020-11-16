
@extends('layouts.layout')

@section('content')
<link rel="stylesheet" href="{{css('planning_css')}}">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <!-- <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Planning</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Administration</a></li>
                        <li class="breadcrumb-item active">Planning</li>
                    </ol>
                </div>
            </div> 
        </div> -->
    </div>
    <!-- /.content-header -->


    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-0">
                                <div class="header-details">
                                    <div class="name-area">
                                        <h2 class="m-0 text-dark">
                                            Planning (Berth to Plot)
                                        </h2>
                                    </div>
                                    <div class="action-area">
                                        <button class="btn btn-info btn-flat filter-btn">Filter&nbsp;&nbsp;<i class="fa fa-filter" aria-hidden="true"></i></button>
                                        <a href="javascript:void(0)" class="btn btn-primary float-right tooltips "><i class="fa fa-file-excel"></i> Export</a>
                                        <a href="{{url('/plan')}}" class="btn btn-success float-right tooltips "><i class="fa fa-plus"></i> Add New Plan</a>
                                    </div> 
                                </div>
                                <div class="filter-area hide">
                                    <form id="filterForm">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <label for="plans.vessel_id" class="col-sm-3 col-form-label">Vessel</label>
                                                    <div class="col-sm-9">
                                                        <select class="form-control select2" name="plans.vessel_id" id="vessel_id">
                                                            <option value="">--Select--</option>
                                                            @if(isset($vessels) && !empty($vessels))
                                                            @foreach($vessels as $vessel)
                                                            <option value="{{$vessel->id}}">{{$vessel->name}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2"></div>
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <label for="plans.date_from" class="col-sm-3 col-form-label">From</label>
                                                    <div class="col-sm-9">
                                                        <div class="input-group date" id="from" data-target-input="nearest">
                                                            <input type="text" class="form-control datetimepicker-input" data-target="#from" name="plans.date_from" id="date_from" readonly/>
                                                            <div class="input-group-append" data-target="#from" data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <label for="plans.origin_id" class="col-sm-3 col-form-label">Berth</label>
                                                    <div class="col-sm-9">
                                                        <select class="form-control select2" name="plans.origin_id" id="origin_id">
                                                            <option value="">--Select--</option>
                                                            @if(isset($berths) && !empty($berths))
                                                            @foreach($berths as $berth)
                                                            <option value="{{$berth->id}}">{{$berth->location}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2"></div>
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <label for="plans.date_to" class="col-sm-3 col-form-label">To</label>
                                                    <div class="col-sm-9">
                                                        <div class="input-group date" id="to" data-target-input="nearest">
                                                            <input type="text" class="form-control datetimepicker-input" data-target="#to" name="plans.date_to" id="date_to" readonly/>
                                                            <div class="input-group-append" data-target="#to" data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <label for="plans.cargo_id" class="col-sm-3 col-form-label">Cargo</label>
                                                    <div class="col-sm-9">
                                                        <select class="form-control select2" name="plans.cargo_id" id="cargo_id">
                                                            <option value="">--Select--</option>
                                                            @if(isset($cargos) && !empty($cargos))
                                                            @foreach($cargos as $cargo)
                                                            <option value="{{$cargo->id}}">{{$cargo->name}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2"></div>
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <label for="truck_count" class="col-sm-3 col-form-label">Truck List</label>
                                                    <div class="col-sm-9">
                                                        <select class="form-control select2" name="truck_count">
                                                            <option value="">--Select--</option>
                                                            <option value="1">Yes</option>
                                                            <option value="0">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-flat btn-primary" onclick="return filterPlanning();">Search</button>
                                                <button type="button" class="btn btn-flat btn-default" onclick="return resetFilter();">Reset</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12">
                                <div class="card card-tabs shadow-none">
                                    <!-- <div class="card-header p-0 pt-1">
                                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                            <li class="nav-item w-50">
                                                <a class="nav-link active bg-primary text-center" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Berth To Plot Transfers</a>
                                            </li>
                                            <li class="nav-item w-50">
                                                <a class="nav-link bg-secondary text-center" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Plot to Plot Transfers</a>
                                            </li>
                                        </ul>
                                    </div> -->
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="btopPlanningTbl" class="table">
                                            <thead>
                                                <tr>
                                                    <th ></th>
                                                    <th>Vessel</th>
                                                    <th>Berth</th>
                                                    <th style="width: 17%;">From</th>                                            
                                                    <th style="width: 17%;">To</th>
                                                    <th>Cargo</th>
                                                    <th style="width: 17%;">Created At</th>
                                                    <th style="width: 12%; text-align:right; padding-right: 0;">Truck List</th>
                                                    <th style="width: 17%; text-align:center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Data Loaded Via AJAX --}}
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
                </div>
            </div>
        </div>
    </section>
</div>
@endsection    
@push('script')
<script type='text/javascript'>
    var token = '{{ csrf_token() }}';
</script>
<script src="{{ js('planning_js') }}"></script>
@endpush
