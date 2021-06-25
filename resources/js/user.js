// ready event
$(document).ready(function () {

	if (filter_by_active_user > 0) {
		$("#filter_by_active_user1").parent().removeClass("btn-default off");
		$("#filter_by_active_user1").parent().addClass("btn-primary");
		$("#filter_by_active_user1").attr("checked", true);
		$("#filter_by_active_user1").val(1);
	}
});
 
// submit form when slider is chnaged
$("#filter_by_active_user1").change(function () {
	var filter_by_active_user_val = $('#filter_by_active_user').val();
	if (filter_by_active_user_val == 1) {
		$(this).val(0);
		$('#filter_by_active_user').val(0);
	} else {
		$(this).val(1);
		$('#filter_by_active_user').val(1);
	}
	$("#user_search_form").submit();
});

