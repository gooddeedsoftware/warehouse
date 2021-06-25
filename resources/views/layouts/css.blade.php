<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<!-- Custom Style files and plugins -->
{!! Html::style('bootstrap/css/bootstrap.min.css') !!}
{!! Html::style('css/pnotify.custom.css') !!}
{!! Html::style('css/bootstrap4-toggle.min.css') !!}
{!! Html::style('css/bootstrap-datetimepicker.css') !!}
{!! Html::style('css/select2.v1.min.css') !!}

{!! Html::style(mix('css/customStyle.css')) !!}
{!! Html::style('css/datatables.min.css') !!}
{!! Html::style('tablesorter-master/css/theme.default.css') !!}

@if (config('app.env') == 'production')
	{!! Html::style(mix('css/production.css')) !!}
@else
	{!! Html::style(mix('css/staging.css')) !!}
@endif


 @yield('page_style')