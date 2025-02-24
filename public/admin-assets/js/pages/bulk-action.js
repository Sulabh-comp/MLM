window.addEventListener("app:mounted",(function() {
	if($('#checkAll').length && $('.checkSingle').length) {

		const config = {
	        placement: "bottom-start",
	        modifiers: [
	          {
	            name: "offset",
	            options: {
	              offset: [0, 4],
	            },
	          },
	        ],
	    };

	   	new Popper("#bulkActionDropDown", ".popper-ref", ".popper-root", config);

		$('#checkAll').click(function () {
		   $('.checkSingle').prop('checked', $(this).prop('checked'));
		});

		$('.checkSingle').click(function () {
		   var checked = $('.checkSingle:checked').length == $('.checkSingle').length;
		   $('#checkAll').prop('checked', checked);
		});

		$('.bulkAction').click(function() {

			var model_ids = $('.bulkModelId input:checkbox:checked').map(function () {
		        return $(this).data('model-id');
		   	}).get();

		    if(!model_ids.length) {
		      notifier("Please select an input.", "error");
		      return false;
		    }

		    var actionType = $(this).data('action-type');
		    var actionText = $(this).data('action-text');

		    $('#modelIds').val(model_ids);
		    $('#actionType').val(actionType);
		    var confirmation =  confirm(actionText);
		    if(confirmation) {
		    	$("#bulkActionForm").submit();
		    }
		});
	}
}), {
once: !0
});