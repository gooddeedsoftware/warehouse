<nav class="navbar navbar-expand-md navbar-dark" id="mainNavbar" style="background-color: {{ config('app.env') == 'production' ? "#28a745" : "#020069"  }} !important">
    <a href="{!! URL::to('/home') !!}" class="navbar-brand font-weight-bold">{{ config("app.name") }}</a>
    <label class="nav-link order_customer_label d-block d-md-none"></label>
    <button type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
      <span class="navbar-toggler-icon"></span>
    </button>
    @if (Auth::check())
    <div id="navbarContent" class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item" id="customer_li_menu"><a class="nav-link @if(Request::segment(1)=='customer') active @endif" href="{{ url('/customer') }}">{{ trans('main.customers') }}</a></li>
            <li class="nav-item">
                <a class="nav-link @if(Request::segment(1)=='supplier') active @endif " href="{{ url('/supplier') }}">{{ trans('main.suppliers') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  @if(Request::segment(1)=='order') active @endif " href="{{ url('/order') }}">{{ trans('main.sales') }}</a>
            </li>
            @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
                <li class="nav-item"><a class="nav-link  @if(Request::segment(1)=='warehousedetails') active @endif " href="{{ url('/warehousedetails') }}">{{ trans('main.warehouse') }}</a></li>
            @endif
            <li class="nav-item">
                <a class="nav-link @if(Request::segment(1)=='product') active @endif" href="{!! route('main.product.index') !!}">{!! trans('main.products') !!}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::segment(1)=='equipment') active @endif" href="{{ url('/equipment') }}">{!! trans('main.equipments') !!}</a>
            </li>

            @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
                <li class="nav-item">
                    <a class="nav-link @if(Request::segment(1)=='reporting') active @endif" href="{{ url('/reporting') }}">{!! trans('main.reports') !!}</a>
                </li>
            @endif
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <label class="nav-link order_customer_label d-none d-md-block"></label>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-user" aria-hidden="true"></i>&nbsp; {{ Auth::User()->first_name.' '.Auth::User()->last_name  }}
                </a>
                <ul aria-labelledby="dropdownMenu1" class="dropdown-menu border-0 shadow">
                    {{-- <li><a href="#" class="dropdown-item"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;{{ __('main.profile') }}</a></li> --}}
                    <li class="dropdown-submenu">
                        <a id="langMenu" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item langMenu"><i class="fa fa-caret-left" aria-hidden="true"></i>&nbsp;{!! trans('main.language') !!}</a>
                        <ul aria-labelledby="langMenu" class="dropdown-menu border-0 shadow">
                            <li><a href="{!!URL::to('ln/no')!!}" class="dropdown-item">Norsk</a></li>
                            <li><a href="{!!URL::to('ln/en')!!}" class="dropdown-item">English</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a id="settingsMenu" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item settingsMenu"><i class="fa fa-caret-left"  aria-hidden="true"></i>&nbsp;{!! trans('main.settings') !!}</a>
                        <ul aria-labelledby="settingsMenu" class="dropdown-menu border-0 shadow">
                            <li><a href="{{ url('/user') }}" class="dropdown-item">{!! trans('main.users') !!} </a></li>

                            @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
                                <li><a href="{{ url('/department') }}" class="dropdown-item">{!! trans('main.department') !!}</a></li>
                                <li><a href="{{ url('/activities') }}" class="dropdown-item">{!! trans('main.activities') !!}</a></li>
                                <li><a href="{{ url('/equipmentcategory') }}" class="dropdown-item">{!! trans('main.equipmentcategory') !!}</a></li>
                            @endif
                            @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative" )
                                <li><a href="{{ url('/currency') }}" class="dropdown-item">{!! trans('main.currency') !!}</a></li>
                                <li><a href="{{ url('/company') }}" class="dropdown-item">{!! trans('main.company') !!}</a></li>
                                <li><a href="{{ url('/accplan') }}" class="dropdown-item">{!! trans('main.accplan') !!}</a></li>
                            @endif
                            <li><a href="{{ url('/warehouse') }}" class="dropdown-item">{!! trans('main.warehouse') !!}</a></li>
                            <li><a href="{{ url('/location') }}" class="dropdown-item">{!! trans('main.location') !!}</a></li>
                            <li><a href="{{ url('/country') }}" class="dropdown-item">{!! trans('main.country') !!}</a></li>
                            <li><a href="{{ url('/offersettings') }}?type=1" class="dropdown-item">{!! trans('main.standard_text') !!}</a></li>
                            @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
                                <li><a href="{{ url('/logistraDetails') }}" class="dropdown-item">{!! trans('main.logistraDetails') !!}</a></li>
                                <li><a href="{{ url('/printer_detail') }}" class="dropdown-item">{!! trans('main.printer_detail') !!}</a></li>
                            @endif
                            <li><a href="{{ url('/productGroup') }}" class="dropdown-item">{!! trans('main.productGroup') !!}</a></li>
                            <li><a href="{{ url('/grossMargin') }}" class="dropdown-item">{!! trans('main.grossMargin') !!}</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"  class="dropdown-item"><i class="fas fa-sign-out-alt"></i>&nbsp; {{ __('main.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    @endif
</nav>

<div class="modal fade" id="addnew_contact" role="dialog" aria-labelledby="addNewModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            @section('addnew_contact')
                <p>No form for this section.</p>
            @show
        </div>
    </div>
</div>
