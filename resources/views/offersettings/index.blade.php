@extends('layouts.layouts')
@section('title',__('main.offersettings'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.offersettings') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.offersettings') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop 
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header cutomerOrderContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link"  href="{{ url('/offersettings') }}?type=1" id="offer_link">
                        <span>{!! __('main.offer') !!}</span>
                    </a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="{{ url('/offersettings') }}?type=2" id="order_link">
                        <span>{!! __('main.order') !!}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/offersettings') }}?type=3" id="purchase_order_link">
                        <span>{!! __('main.supplier_order') !!}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" width="100%">
                        <thead class="thead-a-color">
                            <tr>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ @$offerComment->data ? $offerComment->data : '-'  }}</td>
                                <td>  <a href="#" data-toggle="modal" data-target="#offer_settings_model" data-toggle="modal"><span class="fa fa-pencil"></span></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="offer_settings_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <!-- Modal Header -->
           <div class="modal-header">
                <h4 class="modal-title">{!!__('main.standard_text') !!}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
           </div>

          <!-- Modal body -->
          <div class="modal-body">
                {!! Form::open(array('route' => array('main.offersettings.update', @$offerComment->id), 'method' => 'PUT', 'id' => 'standard_offer_text_form', 'name' => 'standard_offer_text_form', 'class' => 'form-vertical')) !!}
                    <div class="form-group">
                        {!! Form::textarea('data', @$offerComment->data, array('class' => 'form-control', 'id' => 'standard_offer_text', 'required' => 'required', 'max' => 500)) !!}
                    </div>
                    <input type="hidden" name="id" value="{{ @$offerComment->id }}" />
              {!! Form::close() !!}
           </div>

          <!-- Modal footer -->
           <div class="modal-footer">
                <button type="button" class="btn btn-primary updateCommentText" id="updateCommentText">{{ trans('main.update') }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('main.close') }}</button>
           </div>

        </div>
    </div>
</div>
@endsection
@section('page_js')
    <script type="text/javascript">
        var type = "{{ $type }}";
        $('#updateCommentText').on('click', function() {
            $('#standard_offer_text_form').submit();
        });
        if (type == 1) {
            $('#offer_link').addClass('active');
        } else if (type == 2) {
            $('#order_link').addClass('active');
        } else {
            $('#purchase_order_link').addClass('active');
        }
    </script>
@endsection
