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
        font-family: Arial !important;
        font-size: 11px !important;
    }
    .table-borderless tbody tr td, .table-borderless tbody tr th, .table-borderless thead tr th,.table-borderless tr td {
        border: none !important;
    }
    .table td {
        border-top: none !important; 
        border-bottom: none  !important; 
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
    <div class="showLast" style="height: 80px">
     <table class="table table-sm" style='font-size: 13px !important;'>
        <thead>
            <tr style="background-color: lightgrey !important; font-style:italic !important">
                <th width="30%">Tilleggsavgift.</th>
                <th class="text-right" width="20%">Netto</th>
                <th class="text-right" width="20%">Mva.</th>
                <th class="text-right" width="20%">Sum inkl. mva.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td align="right">{!! @$orders->sum ? number_format(@$orders->sum,2, ',', '') : ''; !!}</td>
                <td align="right">{!! @$orders->mva ? number_format(@$orders->mva,2, ',', '') : ''; !!}</td>
                <td align="right">{!! @$orders->total ? number_format(@$orders->total,2, ',', '') : ''; !!}</td>
            </tr>
        </tbody>
    </table>
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
