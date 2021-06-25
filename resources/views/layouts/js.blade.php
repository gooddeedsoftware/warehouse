<!-- customs js -->
{!! Html::script('js/dataTimePickerIcons.js') !!}

<!-- //plugins -->
{!! Html::script('js/jquery-3.4.1.min.js') !!}
{!! Html::script('js/popper.js') !!}
{!! Html::script('bootstrap/js/bootstrap.min.js') !!}
{!! Html::script('tablesorter-master/js/jquery.tablesorter.js') !!}
{!! Html::script('tablesorter-master/js/jquery.tablesorter.widgets.js') !!}
{!! Html::script('js/select2.min.js') !!}
{!! Html::script('js/jquery.validate.min.js') !!}
{!! Html::script('js/bootbox.js') !!}
{!! Html::script('js/bootstrap4-toggle.min.js') !!}
{!! Html::script('js/jquery.blockUI.js') !!}
{!! Html::script('js/moment.js') !!}
{!! Html::script('js/datetimepicker_language/en-gb.js') !!}
{!! Html::script('js/bootstrap-datetimepicker.js') !!}


<!-- CustomJS -->
{!! Html::script('js/pnotify.custom.js') !!}
{!! Html::script('js/signature_pad.js') !!}
{!! Html::script('js/admin.v1.js') !!}
{!! Html::script('js/util.js') !!}
{!! Html::script('js/constants.js') !!}
{!! Html::script('js/datatables.min.js') !!}
<script type = "text/javascript" >
    var url = "{!! URL::to('/') !!}";
    var token = "{!! csrf_token() !!}";
    var message_text =  message =  "{!! trans('main.message') !!}";
    var archived_status_text = "{!! trans('main.the_order_has_been_archived_successfully') !!}";
    var save_btn = "{!! trans('main.save') !!}";
    var js_select_text = "{!! trans('main.selected') !!}";
    var invalid_zip = "{!! trans('main.invalid_zip') !!}";
    var material_save_msg = "{!! trans('main.material_save_msg') !!}";

    var select_product = "{!! trans('main.select_product') !!}";
    var not_found = "{!! trans('main.not_found') !!}";
    var searching = "{!! trans('main.searching') !!}";
    var invoice_no_added = "{!! trans('main.invoice_no_added') !!}";
    var continue_without_shipment_message = "{!! trans('main.continue_without_shipment_message') !!}";

    $(function() {
        // ------------------------------------------------------- //
        // Multi Level dropdowns
        // ------------------------------------------------------ //
        $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).siblings().toggleClass("show");
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                $('.dropdown-submenu .show').removeClass("show");
            });

        });
    });

    $('.dropdown-submenu a.settingsMenu').on("click", function(e) {
        $('.langMenu').next('ul').hide();
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });

    $('.dropdown-submenu a.langMenu').on("click", function(e) {
        $('.settingsMenu').next('ul').hide();
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });


    $(".dropdown-menu > li > a.trigger").on("click", function(e) {
        $(".sub-menu").hide();
        var current = $(this).next();
        var grandparent = $(this).parent().parent();
        if ($(this).hasClass('left-caret') || $(this).hasClass('right-caret')) {
            $(this).toggleClass('right-caret left-caret');
        }
        grandparent.find('.right-caret').not(this).toggleClass('right-caret left-caret');
        grandparent.find(".sub-menu:visible").not(current).hide();
        current.show();
        e.stopPropagation();
    });

    window.localStorage.setItem("back_detect_val", "1");
    $(document).ready(function() {
        $(".paginate_size_select").change(function() {
            var paginate_size = $(this).val();
            var url = "{!! URL::to('/') !!}";
            var token = "{!! csrf_token() !!}";
            var user_id = "{{ Session::get('currentUserID') }}";
            $.ajax({
                url: url + "/changePagination/" + paginate_size + "/" + user_id,
                type: 'get',
                success: function(response) {
                    setTimeout($.unblockUI, 1000);
                    var current_url = $(location).attr('href');
                    var updated_url = updateQueryStringParameter(current_url, 'page', '1');
                    window.location.href = updated_url;
                },
                error: function(response) {
                    setTimeout($.unblockUI, 1000);
                    console.log(response);
                }
            });
        });

        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }
    });

    //Logic to avoid the form getting submit multiple times
    $('.settingsCrud, .formSaveBtn').on("click", function(e) {
        displayBlockUI();
        $(this).attr('disabled', 'disabled')
        if (!$('#' + $(this).attr('form-name')).valid()) {
            $(this).removeAttr('disabled')
            $.unblockUI();
            return false;
        }
        $('#' + $(this).attr('form-name')).submit();
    });

    function displayBlockUI() {
        $.blockUI({
            message: "Loading...",
            baseZ: 1000,
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                'z-index': '20000',
                color: '#fff'
            }
        });
    }

    // fix the search issue in select2 in modal in firefox
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    // hide keyboard in mobile for select2
    // $(".select2, .select2-multiple").on('select2:open', function (e) {
    //      $(this).select2('open');
    //      $('.select2-search input').prop('focus',false);
    // });

    //Added on 4.12.2017
    var select2_open;

    $(document).on('focus', '.select2.select2-container', function(e) {

        var isOriginalEvent = e.originalEvent // don't re-open on closing focus event
        var isSingleSelect = $(this).find(".select2-selection--single").length > 0

        if (isOriginalEvent && isSingleSelect) {
            $(this).siblings('select:enabled').select2('open');
        }

    });

    // fix for ie11
    if (/rv:11.0/i.test(navigator.userAgent)) {
        $(document).on('blur', '.select2-search__field', function(e) {
            select2_open.select2('close');
        });
    }
    /*$('select').select2({
      placeholder: "{!! trans('main.selected') !!}"
        });*/
    $('.select2').select2({
        'locale': 'no',
        'width': "100%"
    });

    $(".collapse.show").each(function() {
        $(this).prev(".card-header").find(".fa").addClass("fa-minus").removeClass("fa-plus");
    });
    $(".collapse").on('show.bs.collapse', function() {
        $(this).prev(".card-header").find(".fa").removeClass("fa-plus").addClass("fa-minus");
    }).on('hide.bs.collapse', function() {
        $(this).prev(".card-header").find(".fa").removeClass("fa-minus").addClass("fa-plus");
    });

    if ($('.searchField:visible').val() != '' && $('.searchField:visible').val() != undefined) {
        var elem = $('.searchField:visible');
        var val = elem.val();
        elem.focus().val('').val(val);
    }

    $(document).on('click', '.syncUNIData', function(e) {
        displayBlockUI();
    });
    
</script>
@yield('page_js')

