@inject('request', 'Illuminate\Http\Request')

<!-- Main Sidebar Container -->
@php
    $menues = menuAccess(); // Coming From Helper : MenuAccessHelper.php
@endphp
<aside class="main-sidebar sidebar-dark-default elevation-4" >
    <!-- Brand Logo -->
    
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{route('home')}}" class="nav-link {{ request()->is('home') ? 'active' : ''}}">
                        <em class="nav-icon fas fa-tachometer-alt"></em>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if(isset($menues) && !empty($menues))
                @foreach($menues as $parentMenu)

                    @if($parentMenu['display_name'] == 'CHALLANS')  
                        @if(isset($parentMenu['child']) && !empty($parentMenu['child']))                          
                        @foreach($parentMenu['child'] as $childMenu)
                            @if(isset($childMenu['subchild']) && !empty($childMenu['subchild']))
                            @foreach($childMenu['subchild'] as $subchildMenu)
                                @if(($childMenu['display_name'] == 'CHALLAN') && ($subchildMenu['display_name'] == 'CHALLAN LIST'))
                                <!-- Challan Menu -->
                                <li class="nav-item">
                                    <a href="{{url('challans')}}" class="nav-link {{ request()->is('challans') ? 'active' : ''}}">
                                        <em class=" nav-icon fas fa-receipt"></em>
                                        <p>Challans</p>
                                    </a>
                                </li>
                                @endif
                            @endforeach
                            @endif
                        @endforeach
                        @endif
                    @endif

                    @if($parentMenu['display_name'] =='PLANNING')
                    @if(isset($parentMenu['child']) && !empty($parentMenu['child']))                          
                        @foreach($parentMenu['child'] as $childMenu)
                            @if(isset($childMenu['subchild']) && !empty($childMenu['subchild']))
                            @foreach($childMenu['subchild'] as $subchildMenu)
                                @if(($childMenu['display_name'] == 'PLANNING') && ($subchildMenu['display_name'] == 'PLANNING LIST'))
                                <!-- Planning Menu -->
                                <li class="nav-item {{ request()->is('plans') ? 'menu-open' : ''}}">
                                    <a href="javascript:void(0)" class="nav-link ">
                                        <em class=" nav-icon fas fa-list-alt"></em>
                                        <p>Planning</p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item {{ (url()->full()==url('plans')) ? 'menu-open' : ''}} ">
                                            <a href="{{url('plans')}}" class="nav-link {{ (url()->full()==url('plans')) ? 'active' : ''}}">
                                                <em class=" nav-icon fa fa-landmark"></em>
                                                <p>Berth To Plot</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)" class="nav-link">
                                                <em class=" nav-icon fa fa-landmark"></em>
                                                <p>Plot To Plot</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                            @endforeach
                            @endif
                        @endforeach
                        @endif
                    @endif

                    @if($parentMenu['display_name'] =='ADMINISTRATOR')
                        <!-- Admin Menu -->
                        <li class="nav-item {{ $request->segment(1) == 'admin' ? 'menu-open' : ''}} ">
                            <a href="#" class="nav-link {{ $request->segment(1) == 'admin' ? 'active' : ''}}">
                                <em class="nav-icon fas fa-tools"></em>
                                <p>
                                    Administration
                                </p>
                            </a>   
                            @if(isset($parentMenu['child']) && !empty($parentMenu['child']))                          
                            @foreach($parentMenu['child'] as $childMenu)
                                <ul class="nav nav-treeview">
                                    @if(isset($childMenu['subchild']) && !empty($childMenu['subchild']))
                                    @foreach($childMenu['subchild'] as $subchildMenu)
                                        @if(($childMenu['display_name'] == 'ORGANIZATION') && ($subchildMenu['display_name'] == 'DETAILS'))
                                            <li class="nav-item {{ request()->is('organization') ? 'menu-open' : ''}} ">
                                                <a href="{{url('organization')}}" class="nav-link {{ request()->is('organization') ? 'active' : ''}}">
                                                    <em class=" nav-icon fa fa-landmark"></em>
                                                    <p>Organization</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'USERS') && ($subchildMenu['display_name'] == 'LIST'))
                                            <li class="nav-item {{ request()->is('users') ? 'menu-open' : ''}} ">                                    
                                                <a href="{{url('users')}}" class="nav-link {{ (request()->is('users') || request()->is('users')) ? 'active' : ''}}">
                                                    <em class=" nav-icon fas fa-user"></em>
                                                    <p>Users</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'DEPARTMENT') && ($subchildMenu['display_name'] == 'LIST'))                                       
                                            <li class="nav-item {{ request()->is('department') ? 'menu-open' : ''}} "> 
                                                <a href="{{url('department')}}" class="nav-link {{ request()->is('department') ? 'menu-open' : ''}} ">
                                                    <em class=" nav-icon fas fa-users"></em>
                                                    <p>Departments</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'LOCATIONS') && ($subchildMenu['display_name'] == 'LIST'))
                                            <li class="nav-item {{ request()->is('location') ? 'menu-open' : ''}} "> 
                                                <a href="{{url('location')}}" class="nav-link {{ $request->segment(2) == 'location' ? 'active' : ''}}">
                                                    <em class=" nav-icon fas fa-map-marker-alt"></em>
                                                    <p>Locations</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'CARGO') && ($subchildMenu['display_name'] == 'LIST'))
                                            <li class="nav-item {{ request()->is('cargo') ? 'menu-open' : ''}} ">
                                                <a href="{{url('cargo')}}" class="nav-link {{ request()->is('cargo') ? 'active' : ''}}">
                                                    <em class=" nav-icon fas fa-shipping-fast"></em>
                                                    <p>Cargo</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'VESSEL') && ($subchildMenu['display_name'] == 'LIST'))
                                            <li class="nav-item {{ request()->is('vessel') ? 'menu-open' : ''}} ">
                                                <a href="{{url('vessel')}}" class="nav-link {{ $request->segment(2) == 'vessel' ? 'active' : ''}}">
                                                    <em class=" nav-icon fas fa-ship"></em>
                                                    <p>Vessels</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'ROLES & PRIVILEGES') && ($subchildMenu['display_name'] == 'LIST'))

                                            <li class="nav-item {{ request()->is('role') ? 'menu-open' : ''}} ">


                                                <a href="{{url('role')}}" class="nav-link {{ request()->is('role') ? 'active' : ''}}">        
                                                    <em class=" nav-icon fas fa-user-secret"></em>
                                                    <p>Roles & Privileges</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'TRUCKS') && ($subchildMenu['display_name'] == 'LIST'))
                                            <li class="nav-item {{ request()->is('truck') ? 'menu-open' : ''}} ">

                                              
                                                <a href="{{ url('truck')}}" class="nav-link {{ request()->is('truck') ? 'active' : ''}}">
                                                    <em class=" nav-icon fa fa-truck"></em>

                                                    <p>Trucks</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if(($childMenu['display_name'] == 'TRUCKING COMPANY') && ($subchildMenu['display_name'] == 'LIST'))
                                            <li class="nav-item {{ request()->is('truck-company') ? 'menu-open' : ''}} ">

                                                <a href="{{ url('truck-company')}}" class="nav-link {{ request()->is('truck-company') ? 'active' : ''}}">
                                                    <em class=" nav-icon fas fa-truck"></em>

                                                    <p>Trucking Company</p>
                                                </a>
                                            </li>
                                        @endif
                                        <!--Updated by Gaurav Agrawal on 28-10-2020 to modify api for consignee -->
                                        @if(($childMenu['display_name'] == 'CONSIGNEE') && ($subchildMenu['display_name'] == 'LIST'))
                                            <li class="nav-item  {{ request()->is('consignee') ? 'menu-open' : ''}} ">
                                                <a href="{{url('consignee')}}" class="nav-link {{ request()->is('consignee') ? 'active' : ''}}">                       
                                                    <em class=" nav-icon fas fa-receipt"></em>
                                                    <p>Consignee</p>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                    @endif
                                        @if($childMenu['display_name'] == 'CACHELIST')

                                            <li class="nav-item  {{ request()->is('cache/clear') ? 'menu-open' : ''}} ">
                                                <a href="{{url('cache/clear')}}" class="nav-link {{ request()->is('cache/clear') ? 'active' : ''}}">                                       
                                                    <em class="nav-icon fas fa-cog"></em>

                                                    <p>Cache Settings</p>
                                                </a>
                                            </li>
                                        @endif
                                </ul>                                
                            @endforeach
                            @endif
                        </li>
                    @endif
                @endforeach
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
