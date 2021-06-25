@if ( Session::has('errors') )

<script type="text/javascript">
    if (window.localStorage.getItem("back_detect_val") == 1) {
        $(function(){
           new PNotify({
              title : "{!! trans('main.validation.form_validation') !!}",
              text : "{!! trans('main.validation.try_again') !!}  @foreach ($errors->all() as $error){{ $error }}@endforeach",
              type : "error",
              delay: 2000,
           });
        });
    }
</script>
@endif

@if ( Session::has('success') )
<script type="text/javascript">
    if (window.localStorage.getItem("back_detect_val") == 1) {
        $(function(){
           new PNotify({
              title : "{!! trans('main.message') !!}",
              text : " {{ Session::get('success') }}",
              type : "success",
              delay: 2000,
           });
        }); 
    }
</script>
@endif

@if ( Session::has('warning') )
<script type="text/javascript">
    if (window.localStorage.getItem("back_detect_val") == 1) {
        $(function(){
           new PNotify({
              title : "{!! trans('main.warning') !!}",
              text : "{{ Session::get('warning') }}",
              type : "info",
              delay: 4000,
           });
        }); 
}
</script>
@endif

@if ( Session::has('error') )
<script type="text/javascript">
    if (window.localStorage.getItem("back_detect_val") == 1) {
        $(function(){
           new PNotify({
              title : "{!! trans('main.error') !!}",
              text : "{{ Session::get('error') }}",
              type : "error",
              delay: 4000,
           });
        }); 
}
</script>
@endif

@if ( Session::has('info') )
<script type="text/javascript">
    if (window.localStorage.getItem("back_detect_val") == 1) {
        $(function(){
           new PNotify({
              title : "{!! trans('main.info') !!}",
              text : "{{ Session::get('info') }}",
              type : "info",
              delay: 10000,
           });
        }); 
    }
</script>
@endif
