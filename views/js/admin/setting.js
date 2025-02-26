/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
$(document).ready(function() {
    $('.import_column_item').change(function(){
        $( "#leo-head-filter" ).submit();
    });
    
    
    $(".limit-select").change(function(){
        $(".limit-pagination").submit();
    });
    $(".list-level select").change(function(){
        var cl = $(this).val();
        $(this).closest('.form-group').find('.level-row-content').hide();
        $(this).closest('.form-group').find('.'+cl).show();
    });
    
    $(".goto-guide").click(function(){
        window.open($(this).attr('href').replace('___',''), '_blank ')
        return false;
    });
    $("#checkall").click(function(){
    	if ($('#checkall').is(":checked")){
    		$(".select-row").prop('checked', true);
    	}else{
    		$(".select-row").prop('checked', false);
    	}
    });

    var insertLeoImportRow = function(id) {
		$.ajax({
			type: "POST",
			url: $(".url").val(),
			data: {
				id: $arr[id],
				action: 'insert'
			}
			}).done(function(msg) {
				$(".row-import-"+$arr[id]).addClass('row-imported');
				$(".row-import-"+$arr[id]).find('.select-row').remove();
				$(".row-import-"+$arr[id]).find(".complete-import-mess").html($(".complete-import-mess").data('content'));
			}).fail(function() {
			  console.log('error');
			}).always(function(){
				id++;
				if (id < $arr.length){
					insertLeoImportRow(id);
				} else {
					location.reload();
				}
		});
	}

    $(".run-import button.import").click(function(){
    	$arr = [];
    	$(".select-row").each(function(){
    		if ($(this).is(":checked")){
    			$arr.push($(this).val());
     		}
    	});

    	var id = 0;
		insertLeoImportRow(id);

    });

    $(".run-import button.delete").click(function(){
    	$arr = [];
    	$(".select-row").each(function(){
    		if ($(this).is(":checked")){
    			$arr.push($(this).val());
     		}
    	});

    	$.ajax({
			type: "POST",
			url: $(".url").val(),
			data: {
				id: $arr.toString(),
				action: 'delete'
			}
			}).done(function(msg) {
				for(i=0;i<$arr.length;i++){
					$(".row-import-"+$arr[i]).remove();
				}
			}).fail(function() {
			  console.log('error');
			}).always(function(){
		});

    });

    $("#id_leopartsfilter_make").change(function()
    {
        var value_make = $(this).val();
        if (value_make != "")
        {
            $.ajax({
			type: "POST",
			url: $("#baseurl").val() + 'index.php',
            data: "fc=module&module=leopartsfilter&controller=search&action=adminajax&active=all&makeid=" + value_make + "&modelid=" + value_model,
			success: function(response) {
				if (response != "-99"){
					var carModel = JSON.parse(response);
					var str = "";
					for(i=0;i<carModel.length;i++){
                        if (carModel[i]['name']) {
						  str += "<option value='" + carModel[i]['id_leopartsfilter_model'] + "'>" + carModel[i]['name'] + "</option>";
                        }
					}

					$("#id_leopartsfilter_model").html(str);
					$("#id_leopartsfilter_model").prop("disabled", false);

					$("#id_leopartsfilter_year").html('<option value="">Select Year</option>')
				}
				else{
					alert("Wrong Request");
					$("#id_leopartsfilter_model").prop("disabled", true);
				}
			},
            });
        }
    });


    $("#id_leopartsfilter_model").change(function()
    {
        var value_make = $("#id_leopartsfilter_make").val();
        var value_model = $(this).val();
        if (value_make != "")
        {
            $.ajax({
            type: "POST",
            url: $("#baseurl").val() + 'index.php',
            data: "fc=module&module=leopartsfilter&controller=search&action=adminajax&active=all&makeid=" + value_make + "&modelid=" + value_model,
            success: function(response) {
                if (response != "-99"){
                	var carModel = JSON.parse(response);
					var str = "";
					for(i=0;i<carModel.length;i++){
                        if (carModel[i]['name']) {
						  str += "<option value='" + carModel[i]['id_leopartsfilter_year'] + "'>" + carModel[i]['name'] + "</option>";
                        }
					}
                    $("#id_leopartsfilter_year").html(str);
                    $("#id_leopartsfilter_year").prop("disabled", false);
                }
                else{
                    alert("Wrong Request");
                    $("#id_leopartsfilter_year").prop("disabled", true);
                }
            },
            });
        }
    });
});
