<!DOCTYPE html>
<html>
<style type="text/css">
    .showLast {
        display: none;
    }
    .pLast .showLast {
        display: block;
    }
    body, body div, .deftext {
        font-size: 12px !important;
        font-family: Helvetica !important;
    }
    p{
        margin-left: 3% !important;
    }
    .smalltext {
        font-size: 12px !important;
    }

    .preWrap {
        white-space:pre-wrap;
    }
    hr {
      border-top: 1px solid rgba(0, 0, 0, 0.88) !important;
    }
</style>
<meta name="viewport" content="text/html" charset="UTF-8">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!-- <link type="text/css" rel="stylesheet" href="{{ URL::to('/') }}/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="{{ URL::to('/') }}/js/jquery-3.4.1.min.js'"></script>
<script type="text/javascript" src="{{ URL::to('/') }}/bootstrap/js/bootstrap.min.js"></script>  -->
<div class="PageClass">
    <div class="showLast" style="height: 100px">
        <div class="col-l smalltext">
            <hr class="my-2">
            &nbsp;{!! @$company_information->name !!}
        </div>
        <div class="row">
           
            <div class="col-4">
                <strong class="smalltext">&nbsp;{!! trans('main.visiting_address') !!}: </strong><br>
                <div class="smalltext preWrap"> {!! @$company_information->company_information !!}</div>
            </div>
            <div class="col-3">
                <strong class="smalltext">&nbsp;{!! trans('main.postadresse') !!}: </strong><br>
                <div class="smalltext preWrap"> {!! @$company_information->post_address !!}</div>
            </div>
            <div class="col-2 smalltext">
                <strong>{!! trans('main.phone') !!}: </strong><br>
                <strong>{!! trans('main.email') !!}: </strong><br>
                <strong>{!! trans('main.vat') !!}: </strong><br>
            </div>
            <div class="col-2 smalltext">
                {!! @$company_information->phone !!}<br>
                {!! @$company_information->company_email !!}<br>
                {!! @$company_information->company_VAT !!}
            </div>
        </div>
    </div>
</div>
<script> 
    (function setPageClass() {
        var vars = {};
        var x = document.location.search.substring(1).split('&');
        for (var i in x) {
            var z = x[i].split('=', 2);
            vars[z[0]] = unescape(z[1]);
        }
        var y = document.getElementsByClassName('PageClass');
        for (var j = 0; j < y.length; ++j) {
            y[j].setAttribute('class', 'PageClass ' + 'p' + vars['page'] + (vars['page'] == 1 ? ' pFirst ' : '') + (vars['page'] == vars['topage'] ? ' pLast ' : ''));
        }
    })();
</script>
</html>
