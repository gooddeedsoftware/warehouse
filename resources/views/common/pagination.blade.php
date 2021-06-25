<div class="row">
	<div class="col-12 col-sm-6 col-md-8 col-lg-9">
		@if (@$paginator && $paginator->total() === 0)
			<h4><p align="center"> {!!trans('main.norecords') !!}</p></h4>
		@else($paginator->total() >= 1)
			<div class="d-block d-sm-none">
				<select id="paginate_size_select" class="paginate_size_select">
					<option value="10" {!! Session::get('paginate_size') == 10 ? 'selected' : '' !!}>10</option>
					<option value="20" {!! Session::get('paginate_size') == 20 ? 'selected' : '' !!} >20</option>
					<option value="30" {!! Session::get('paginate_size') == 30 ? 'selected' : '' !!}>30</option>
					<option value="50" {!! Session::get('paginate_size') == 50 ? 'selected' : '' !!}>50</option>
					<option value="100" {!! Session::get('paginate_size') == 100 ? 'selected' : '' !!}>100</option>
					<option value="300" {!! Session::get('paginate_size') == 300 ? 'selected' : '' !!}>300</option>
				</select>
			</div>
			<div class="d-none d-sm-block">
				{!! trans('main.show') !!}
				<select id="paginate_size_select" class="paginate_size_select">
					<option value="10" {!! Session::get('paginate_size') == 10 ? 'selected' : '' !!}>10</option>
					<option value="20" {!! Session::get('paginate_size') == 20 ? 'selected' : '' !!} >20</option>
					<option value="30" {!! Session::get('paginate_size') == 30 ? 'selected' : '' !!}>30</option>
					<option value="50" {!! Session::get('paginate_size') == 50 ? 'selected' : '' !!}>50</option>
					<option value="100" {!! Session::get('paginate_size') == 100 ? 'selected' : '' !!}>100</option>
					<option value="300" {!! Session::get('paginate_size') == 300 ? 'selected' : '' !!}>300</option>
				</select>
				{!! trans('main.entries') !!}
			</div>
			{!!trans('main.noofrecords') !!}  {!! $paginator->total() !!}
		@endif
	</div>
	<?php
		$extedorderby = '';
		if (isset($orderby)) {
		 	foreach ($orderby as $k=>$by) {
		 		if ($k<>'page') {
		  			$extedorderby .='&'.$k.'='.$by;
		 		}
		 	}
		}
		$extedorderby = str_replace("??", "", $extedorderby);
		$extedorderby = str_replace("&?", "&", $extedorderby);
	 ?>

	<div class="col-12 col-sm-6 col-md-4 col-lg-3" style="margin-top: 12px;">
	  	@if ($paginator->lastPage() > 1)
			<ul class="pagination text-sm-float-right">
				<li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }} page-item">
					@if (@$formaction)
						<a  class="page-link" onclick="paginate('{{ $paginator->url(1) }}{{$extedorderby}}', '{{@$formaction}}')" href="#">{!!trans('main.first') !!}</a>
					@else
						<a  class="page-link" href="{{ $paginator->url(1) }}{{$extedorderby}}">{!!trans('main.first') !!}</a>
					@endif
			 	</li>
				@for ($i = 1; $i <= $paginator->lastPage(); $i++)
					<?php
						$half_total_links = floor(Config::get('pagination.limit') / 2);
						$from = $paginator->currentPage() - $half_total_links;
						$to = $paginator->currentPage() + $half_total_links;
						if ($paginator->currentPage() < $half_total_links) {
						   $to += $half_total_links - $paginator->currentPage();
						}
						if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
							$from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
						}
					?>
					@if ($from < $i && $i < $to)
						<li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }} page-item">
							@if (@$formaction)
								<a  class="page-link" onclick="paginate('{{ $paginator->url($i) }}{{$extedorderby}}','{!! @$formaction!!}')" href="#">{{ $i }}</a>
							@else
								<a  class="page-link" href="{{ $paginator->url($i) }}{{$extedorderby}}">{{ $i }}</a>
							@endif
						</li>
					@endif
				@endfor
				<li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }} page-item">
					@if (@$formaction)
						<a  class="page-link" onclick="paginate('{{ $paginator->url($paginator->lastPage()) }}{{$extedorderby}}', '{!! @$formaction!!}')" href="#">{!!trans('main.last') !!}</a>
					@else
						<a  class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}{{$extedorderby}}">{!!trans('main.last') !!}</a>
					@endif
				</li>
			</ul>
		@endif
	</div>

	
</div>
<script type="text/javascript">
	function paginate(url, formid) {
		console.log(url, "sadfdsfsdfasdffsdfdf")
		console.log(formid, "formid")
		if (url.indexOf("index") >= 0){
			var query_string = splitQueryString($("#"+formid).attr('action'));
			$("#"+formid).attr('action', url+query_string).submit();
		} else if (url.indexOf("?") >= 0) {
			var query_string = splitQueryString($("#"+formid).attr('action'));
		    window.location.href = url+query_string;
		} else {
			window.location.href = url;
		}
	}

	/**
	 * [splitQueryString description]
	 * @param  {[type]} data [description]
	 * @return {[type]}      [description]
	 */
	function splitQueryString(data) {
		query_string = '';
		if (data.indexOf('?') > 0) {
			var hashes =  data.slice(data.indexOf('?') + 1).split('&');
			if (hashes.length) {
			    for(var i = 0; i < hashes.length; i++)
			    {
			    	replaced = hashes[i].replace('?','')
			        hash = replaced.split('=');
			        if (hash[0] != 'page' && hash[0].length)
			        {
			        	var neworder = hash[0].replace("?","");
			        	query_string += '&'+neworder+'='+hash[1];

			        }

			    }
			}
		}
	   return query_string;
	}

	
</script>
