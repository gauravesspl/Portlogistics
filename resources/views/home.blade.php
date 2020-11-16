@extends('layouts.layout')

@section('content')
<!-- Content Wrapper. Contains page content -->
<link rel="stylesheet" href="{{css('dashboard_css')}}">


<div class="content-wrapper">
    @if (\Session::has('success'))
    <div class="alert alert-success">
        {!! \Session::get('success') !!}
    </div>
    @endif
    @if (\Session::has('error'))
    <div class="alert alert-danger">
        {!! \Session::get('error') !!}
    </div>
    @endif

    <div class="container-fluid">

        <div class="row">
            <div class="filterArea">
                <form id="filterForm" style="margin-top: 0.5rem">

                    <h5 class="m-0">Filter</h5>

                    <div class="dashboard-filter-content">
                        <div class="input-group">
                            <input type="text" id="plotDate" name="filter.plotDate" class="form-control datetimepicker-input" />
                            <div class="input-group-append datetimepicker" data-target="#plotDate" data-toggle="datetimepicker">
                                <div class="input-group-text"><em class="fa fa-calendar"></em></div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-filter-content">
                        <select class="multiselect" multiple="multiple" id="cmbVessel" name="filter.cmbVessel[]" data-placeholder="Select Vessel" style="width: 100%;">
                            @foreach($result[0]['vessel'] as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>



                    </div>
                    <div class="dashboard-filter-content">
                        <select class="multiselect" multiple="multiple" data-placeholder="Select Customer" style="width: 100%;" id="cmbCustomer" name="filter.cmbCustomer[]">
                            @foreach($result[2]['data'] as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dashboard-filter-content">
                        <select class="multiselect" multiple="multiple" data-placeholder="Select Cargo" style="width: 100%;" id="cmbCargo" name="filter.cmbCargo[]">
                            @foreach($result[1]['cargo'] as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>









                </form>
            </div>
            <div title="Filter" class="filterBtn float">
                <span class="bg-danger filter-notification">0</span>
                <i class="fa fa-filter  my-float" aria-hidden="true"></i>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card-header border-0 col-md-12">
                    <div class="header-details dashboard-filter bg-navy">
                        <span>Berth to Plot </span>
                        <img class="img img-reponsive" src="{{images('berthtoplot')}}" alt="BTP">

                    </div>
                    <div class="header-details dashboard-filter p-0">

                        <table summary="Berth to plot statistics" class="table" style="width: 100%;">
                            <thead class="bg-grey">
                                <th scope="col">Shift</th>
                                <th scope="col" class="text-center">No Of Trucks</th>
                                <th scope="col"class="text-center">Trips Completed</th>
                            </thead>
                            <tbody id="btp">


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-header border-0 col-md-12">
                    <div class="header-details dashboard-filter bg-navy">
                        <span>Plot to Plot</span>
                        <img class="img img-reponsive" src="{{images('plottoplot')}}" alt="PTP">

                    </div>
                    <div class="header-details dashboard-filter p-0">

                        <table summary="Plot to plot statistics" class="table" style="width: 100%;">
                            <thead class="bg-grey">
                                <th scope="col" class="text-center">Shift</th>
                                <th scope="col" class="text-center">No Of Trucks</th>
                                <th scope="col" class="text-center">Trips Completed</th>
                            </thead>
                            <tbody id="ptp">


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card-header border-0 col-md-12">
                    <div class="header-details dashboard-filter bg-navy">
                        <span>No of Trucks </span>
                        <img class="img img-reponsive" src="{{images('nooftrucks')}}" alt="No Of Trucks">

                    </div>
                    <div class="header-details dashboard-filter">
                        <div class="col-md-8 ntu">
                            <p><span class="text-label-1">Shift A</span> : <span class="default grapharea-nooftrucks-A">0</span></p>
                        </div>
                        <div class="col-md-4">
                            <canvas class="grapharea" id="grapharea-nooftrucks" width="100%" height="100%"></canvas>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-header border-0 col-md-12">
                    <div class="header-details dashboard-filter bg-navy">

                        <span>Trips Completed </span>
                        <img class="img img-reponsive" src="{{images('tripscompleted')}}" alt="No Of Trips Completed">


                    </div>
                    <div class="header-details dashboard-filter">
                        <div class="col-md-8 ttc">
                            <p><span class="text-label-1">Shift A</span> : <span class="default grapharea-tripscompleted-A">0</span></p>
                            <p><span class="text-label-2">Shift B</span> : <span class="default grapharea-tripscompleted-B">0</span></p>
                            <p><span class="text-label-3">Shift C</span> : <span class="default grapharea-tripscompleted-C">0</span></p>
                        </div>
                        <div class="col-md-4">
                            <canvas class="grapharea" id="grapharea-tripscompleted" width="100%" height="100%"></canvas>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-header border-0 col-md-12">
                    <div class="header-details dashboard-filter bg-navy">
                        <span>Challans Deposited </span>
                        <img class="img img-reponsive" src="{{images('challandep')}}" alt="Challans Deposited">

                    </div>
                    <div class="header-details dashboard-filter">
                        <div class="col-md-8 ca">
                            <p><span class="text-label-1">Shift A</span> : <span class="default grapharea-challansdep-A">0</span></p>
                            <p><span class="text-label-2">Shift B</span> : <span class="default grapharea-challansdep-B">0</span></p>
                            <p><span class="text-label-3">Shift C</span> : <span class="default grapharea-challansdep-C">0</span></p>
                        </div>
                        <div class="col-md-4">
                            <canvas class="grapharea" id="grapharea-challansdep" width="100%" height="100%"></canvas>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

</div>











@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script type='text/javascript'>
    var token = '{{ csrf_token() }}';
</script>
<script src="{{ js('dashboard_js') }}"></script>
@endpush
