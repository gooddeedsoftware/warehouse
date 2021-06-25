@extends('layouts.layouts')
 @section('header')
    <h3><i class="icon-message"></i>{!! $is_offer == 0 ? __('main.order') : __('main.offer') !!}</h3>
@stop
@section('help')
    <p class="lead">{!! $is_offer == 0 ? __('main.order') : __('main.offer') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header cutomerOrderContainer-Header">
            <div class="row">
                <div class="col-8">
                    <ul class="nav nav-tabs card-header-tabs">

                        @if ($is_offer == 0)
                            <li class="nav-item">
                                <a class="nav-link active" href="#">
                                    <span class="d-none d-sm-block">{!! __('main.order') !!}</span>
                                    <i class="d-block d-sm-none fa fa-file"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('main.ordermaterial.listOrderMaterials', @$orders->id) !!}">
                                    <span class="d-none d-sm-block">{!! trans('main.materials') !!}</span>
                                    <i class="d-block d-sm-none fa fa-wrench"></i>
                                </a>
                            </li>
                           <li class="nav-item">
                                <a class="nav-link" href="{!! route('main.order.billingData', @$orders->id) !!}">
                                    <span class="d-none d-sm-block">{!! __('main.billing_data') !!}</span>
                                    <i class="d-block d-sm-none fa fa-wrench"></i>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link active" href="#">
                                    <span class="d-none d-sm-block">{!! __('main.offer') !!}</span>
                                    <i class="d-block d-sm-none fa fa-file"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('main.ordermaterial.listOfferMaterials', @$orders->id) !!}">
                                    <span class="d-none d-sm-block">{!! trans('main.materials') !!}</span>
                                    <i class="d-block d-sm-none fa fa-wrench"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-4 text-right">
                    <a  href="#" id="download_order_report_btn" style="padding: 0.100rem .50rem !important" class="download_order_report_btn btn btn-primary" value="{!! @$orders->id !!}" type=1><i class="fa fa-download "></i></a>
                    @if (@$orders->customer && @$orders->customer->email)
                        @if (@$orders->is_offer == 0 || ($orders->is_offer == 1 && @$orders->status > 1))
                            <a  href="#" id="send_order_mail_btn" style="padding: 0.100rem .50rem !important" class="send_order_mail_btn btn btn-primary" value="{!! @$orders->id !!}" type=2><i class="fa fa-envelope"></i></a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body"> 
            {!! Html::style('css/signature-pad.css') !!}

            @if($is_offer == 1)
                {!! Form::open(array('route' => array('main.offer.update',$orders->id),'method'=>'PUT','id'=>'orderform','name'=>'orderform','class' => 'form-horizontal  row-border','data-toggle'=>"validator", 'files' => true)) !!}
            @else
            {!! Form::open(array('route' => array('main.order.update',$orders->id),'method'=>'PUT','id'=>'orderform','name'=>'orderform','class' => 'form-horizontal  row-border','data-toggle'=>"validator", 'files' => true)) !!}
            @endif
                @include('order/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.order'),'btn'=>trans('main.update')])
            {!! Form::close() !!}


            {!! Form::open( array('route' => 'main.order.sendOfferOrderMail','class'=>'form','id'=>'send_offer_order_mail_form') ) !!}
                {{ Form::hidden('order_id','',array('id'=>'order_id_for_mail'))}}
                {{ Form::hidden('order_status','',array('id'=>'order_status_hidden'))}}
            {!! Form::close() !!}

            {!! Form::open( array('route' => 'main.order.sendOrDownloadOrder','class'=>'form','id'=>'order_mail_report_form') ) !!}
                {{ Form::hidden('order_id','',array('id'=>'report_order_id'))}}
                {{ Form::hidden('type','',array('id'=>'type'))}}
            {!! Form::close() !!}

        </div>
    </div>
</div>
@stop
