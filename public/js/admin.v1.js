$(function() {
    // $('a[rel=tooltip]').tooltip();
    //     //Apply twitter bootstrap alike style to select element
    // $('.select2').select2({
    //     width :'element',
    //     placeholder : 'Select'
    // });
    // // Convert text input in create permission view into tags mode
    // $('#permission-tags').select2({
    //     tags: ['view','create','update','delete'],
    //     width: 'element'
    // });
});
/**
 * Create a confirm modal
 * We want to send an HTTP DELETE request
 *
 * @usage  <a href="posts/2" data-method="delete"
 *         	data-modal-text="Are you sure you want to delete"
 *         >
 *
 *
 * @author Steve Montambeault
 * @link   http://stevemo.ca
 *
 */
(function() {
    laravel = {
        initialize: function() {
            this.methodLinks = $('a[data-method]');
            this.registerEvents();
        },
        registerEvents: function() {
            this.methodLinks.unbind("click");
            this.methodLinks.unbind("dblclick");
            this.methodLinks.on('click', this.handleMethod);
        },
        handleMethod: function(e) {
            bootbox.hideAll();
            e.preventDefault();
            var link = $(this);
            var httpMethod = link.data('method').toUpperCase();
            var allowedMethods = ['PUT', 'DELETE', 'GET'];
            var extraMsg = link.data('modal-text');
            var msg = '<i class="icon-warning-sign modal-icon"></i>' + extraMsg;
            // If the data-method attribute is not PUT or DELETE,
            // then we don't know what to do. Just ignore.
            if ($.inArray(httpMethod, allowedMethods) === -1) {
                return;
            }
            bootbox.dialog(msg, [{
                "label": "OK",
                "class": "btn-danger",
                "callback": function() {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': link.attr('href')
                    });
                    var hiddenInput = $('<input>', {
                        'name': '_method',
                        'type': 'hidden',
                        'value': link.data('method')
                    });
                    var hiddencsrf = $('<input>', {
                        'name': '_token',
                        'type': 'hidden',
                        'value': link.data('csrf')
                    });
                    if (httpMethod == "GET") {
                        window.location.href = url + '/' + link.attr('data-url');
                    } else {
                        form.append(hiddenInput).append(hiddencsrf).appendTo('body').submit();
                    }
                }
            }, {
                "label": "Cancel",
                "class": "btn-light"
            }], {
                "header": "Please Confirm"
            });
        }
    };
    laravel.initialize();
})();