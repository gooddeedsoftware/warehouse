@extends('layouts.layouts')
@section('title',__('main.equipment'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.equipment') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.equipment') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop 

@section('content')

<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!__('main.equipment') !!}</b>
        </div>
        <div class="card-body">
            @php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
            {!! Form::open(array('route' => array('main.equipment.search', @$query_string), 'id' => 'equipment_search_form')) !!}
            <div class="row">
                <div class="col-3 col-sm-6 col-md-8">
                    <div class="form-group">
                        <a class="btn btn-primary" href="{!! route('main.equipment.create',['btn_value' => 1]) !!}">
                            <i class="d-block d-sm-none fa fa-plus"></i>
                            <div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.equipment')) !!} </div>
                        </a>
                    </div>
                </div>
                <div class="col-9 col-sm-6 col-md-4">
                    <div class="form-group input-group">
                        {!! Form::text('search', @Session::get('equipment_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.equipment')) )) !!}
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="equipmentcategory_search_btn"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="table-responsive">
                <table class="table table-striped table-hover" width="100%">
                    <thead class="thead-a-color">
                        <tr>
                            <th>
                                <a>@sortablelink('internalnbr', __('main.internalnbr'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('reginnid', __('main.reginnid'))</a>
                            </th>

                            <th>
                                <a>@sortablelink('customer', __('main.customer'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('description', __('main.name_model'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('category', __('main.category'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('install_date', __('main.install_date'))</a>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count(@$equipments) > 0)
                            @foreach(@$equipments as $equipment)
                                <tr>
                                    <td><a href="{{route('main.equipment.edit',array($equipment->id))}}">{{ @$equipment->internalnbr}}</a></td>
                                    <td>{{ @$equipment->reginnid}}</td>
                                    <td>{{ htmlspecialchars(@$equipment->name) }}</td>
                                    <td>
                                        {{ htmlspecialchars(@$equipment->description)  }}
                                    </td>
                                    <td>
                                        {{ @$equipment_categories[@$equipment->equipment_category] }}
                                    </td>
                                    <td>
                                         @if(@$equipment->install_date)
                                                {!! date('d.m.Y', strtotime(@$equipment->install_date) )!!}&nbsp;&nbsp;&nbsp;
                                         @endif
                                    </td>
                                    <td>
                                        <a href="{{route('main.order.createOrderFromEquipment',array($equipment->id, @$equipment->customer->id))}}" class="btn-link fa fa-plus"></a>
                                    </td>
                                    <td><a href="{{route('main.equipment.edit',array($equipment->id))}}" class="fa fa-edit"></a></td>
                                    <td class="delete-td">
                                        @if(count(@$equipment->order) < 1 && count($equipment->equipmentChild) < 1)
                                            <a href="{{ route('main.equipment.destroy', array($equipment->id)) }}"
                                            data-method="delete"
                                            data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.equipment')) !!}?" data-csrf="{!! csrf_token() !!}">
                                                <i class="fas fa-trash-alt delete-icon"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach 
                        @endif
                    </tbody>
                </table>
            </div>
            @include('common.pagination',array('paginator'=>@$equipments, 'formaction' => 'equipment_search_form'))
        </div>
    </div>
</div> 
@endsection
