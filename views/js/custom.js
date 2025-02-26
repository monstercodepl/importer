/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
$('select.selectpicker').selectpicker({
	caretIcon: 'glyphicon glyphicon-menu-down'
});

$( document ).ready(function() {
	$('select.leo-filter-order').selectpicker("refresh");
	$("#left-column .field-search .value-search").css('max-width', $("#left-column .leo-partsfilter").width());
	$("select.carMake").change(function(){
		var el = $(this);
		$("select.carMake").val(el.val());
		if($(".allow_search_form").val() == 1){
			if(typeof($(".leo-partsfilter-submit").html()) == "undefined"){
				changeAjax(el);
			}
		}else{
			$(".leo-filter-display .leo-filter-content").html('');
			$(".leo-filter-display").removeClass('active');
			if(el.val() != ''){
				$(".leo-process").show();
				$(".leo-partsfilter-submit").addClass('loading');
				if($(".filter_url").val() && 0){
					var post_url = $(".filter_url").val();
				}else{
					var post_url = 'index.php?fc=module&module=leopartsfilter&controller=search';
				}
				
				$(".carModel-loading").show();
				$.ajax({
					type: "POST",
					url: post_url,
					data: 'action=adminajax&makeid=' + el.val(),
					success: function(response) {
						if(response != '-99'){
							var carModel = JSON.parse(response);
							var str = "";
							for(i=0;i<carModel.length;i++){
								str += "<option value='" + carModel[i]['id_leopartsfilter_model'] + "'>" + carModel[i]['name'] + "</option>";
							}
							$("select.carModel").html(str);
							$("select.carModel").val('');
							$("select.carYear").val('');
							$("select.carDevice").val('');
							$("select#carlevel5").val('');

							$("select.carYear").html('<option value="">'+$(".carYear").data('option')+'</option>');
							$("select.carDevice").html('<option value="">'+$(".carDevice").data('option')+'</option>');
							$("select#carlevel5").html('<option value="">'+$("#carlevel5").data('option')+'</option>');
							$("select.carModel").prop('disabled', false);
							$("select.carYear").prop('disabled', true);
							$("select.carDevice").prop('disabled', true);
							$("select#carlevel5").prop('disabled', true);
						}
						else{
							alert('Wrong Request');
							$(".carModel").prop('disabled', true);
							$(".carYear").prop('disabled', true);
						}
						$(".leo-process").hide();
						$(".leo-partsfilter-submit").removeClass('loading');
						$(".carModel-loading").hide();
						refreshSelectFilter();
					},
				});
			}
			else{
				$("select.carModel").prop('disabled', true);
				$("select.carYear").prop('disabled', true);
				$("select.carDevice").prop('disabled', true);
				$("select.carModel").val('');
				$("select.carYear").val('');
				$("select.carDevice").val('');
				$("select#carlevel5").val('');
				$("select#carlevel5").prop('disabled', true);
			}
		}
		refreshSelectFilter();
	});
	
	
	$("select.carModel").change(function(){
		var el = $(this);
		$("select.carModel").val(el.val());

		if($(".allow_search_form").val() == 2){
			if(typeof($(".leo-partsfilter-submit").html()) == "undefined"){
				changeAjax(el);
			}
		} else{
			$(".leo-filter-display .leo-filter-content").html('');
			$(".leo-filter-display").removeClass('active');
			if(el.val() != ''){
				$(".leo-process").show();
				$(".leo-partsfilter-submit").addClass('loading');
				if($(".filter_url").val() && 0){
					var post_url = $(".filter_url").val();
				}else{
					var post_url = 'index.php?fc=module&module=leopartsfilter&controller=search';
				}
				$(".carYear-loading").show();
				$.ajax({
					type: "POST",
					url: post_url,
					data: 'action=adminajax&modelid=' + $("select.carModel").val() + '&makeid=' + $("select.carMake").val(),
					success: function(response) {
						if(response != '-99'){
							var carYear = JSON.parse(response);
							var str = "";
							for(i=0;i<carYear.length;i++){
								str += "<option value='" + carYear[i]['id_leopartsfilter_year'] + "'>" + carYear[i]['name'] + "</option>";
							}

							$("select.carYear").html(str);
							$("select.carYear").val('');
							$("select.carDevice").val();
							$("select.carlevel5").val();
							$("select.carYear").prop('disabled', false);

							$("select.carDevice").html('<option value="">'+$("select.carDevice").data('option')+'</option>');
							$("select#carlevel5").html('<option value="">'+$("#carlevel5").data('option')+'</option>');

							$("select.carDevice").prop('disabled', true);
							$("select.carlevel5").prop('disabled', true);

							refreshSelectFilter();
						}
						else{
							alert('Wrong Request');
							$("select.carYear").prop('disabled', true);
						}
						$(".leo-process").hide();
						$(".leo-partsfilter-submit").removeClass('loading');
						$(".carYear-loading").hide();
						refreshSelectFilter();
					},
				});
			}
			else{
				$("select.carYear").prop('disabled', true);
				$("select.carYear").val('');
				$("select#carlevel5").val('');
				$("select#carlevel5").prop('disabled', true);
			}
			
		}
		refreshSelectFilter();

	});
	
	$("select.carYear").change(function(){
		var el = $(this);
		$("select.carYear").val(el.val());

		if($(".allow_search_form").val() == 3){
			if(typeof($(".leo-partsfilter-submit").html()) == "undefined"){
				changeAjax(el);
			}
		}else{
			$(".leo-filter-display .leo-filter-content").html('');
			$(".leo-filter-display").removeClass('active');
			if(el.val() != ''){
				$(".leo-process").show();
				$(".leo-partsfilter-submit").addClass('loading');
				if($(".filter_url").val() && 0){
					var post_url = $(".filter_url").val();
				}else{
					var post_url = 'index.php?fc=module&module=leopartsfilter&controller=search';
				}
				$(".carDevice-loading").show();
				$.ajax({
					type: "POST",
					url: post_url,
					data: 'action=adminajax&modelid=' + $("select.carModel").val() + '&makeid=' + $("select.carMake").val() + '&yearid='+ $("select.carYear").val(),
					success: function(response) {
						if(response != '-99'){
							var carDevice = JSON.parse(response);
							var str = "";
							for(i=0;i<carDevice.length;i++){
								str += "<option value='" + carDevice[i]['id_leopartsfilter_device'] + "'>" + carDevice[i]['name'] + "</option>";
							}
							$("select.carDevice").html(str);
							$("select.carDevice").val('');
							$("select.carDevice").prop('disabled', false);
							$('select.carDevice').selectpicker("refresh");

							$("select.carlevel5").val();
							$("select#carlevel5").html('<option value="">'+$("#carlevel5").data('option')+'</option>');
							$("select.carlevel5").prop('disabled', true);

						}
						else{
							$("select.carDevice").prop('disabled', true);
							$("select.carlevel5").prop('disabled', true);
						}
						$(".leo-process").hide();
						$(".leo-partsfilter-submit").removeClass('loading');
						$(".carDevice-loading").hide();
						refreshSelectFilter();
					},
				});
			}
			else{
				$("select.carDevice").prop('disabled', true);
				$("select.carDevice").val('');
			}
		}
		refreshSelectFilter();
	});
	

	$("select.carDevice").change(function(){
		var el = $(this);
		$("select.carDevice").val(el.val());

		if($(".allow_search_form").val() == 4){
			if(typeof($(".leo-partsfilter-submit").html()) == "undefined"){
				changeAjax(el);
			}
		}else{
			$(".leo-filter-display .leo-filter-content").html('');
			$(".leo-filter-display").removeClass('active');
			if(el.val() != ''){
				$(".leo-process").show();
				$(".leo-partsfilter-submit").addClass('loading');
				if($(".filter_url").val() && 0){
					var post_url = $(".filter_url").val();
				}else{
					var post_url = 'index.php?fc=module&module=leopartsfilter&controller=search';
				}
				$(".carlevel5-loading").show();

				$.ajax({
					type: "POST",
					url: post_url,
					data: 'action=adminajax&modelid=' + $("select.carModel").val() + '&makeid=' + $("select.carMake").val() + '&yearid='+ $("select.carYear").val() + '&deviceid='+ $("select.carDevice").val(),
					success: function(response) {
						if(response != '-99'){
							var carlevel5 = JSON.parse(response);
							console.log(carlevel5);
							var str = "";
							for(i=0;i<carlevel5.length;i++){
								str += "<option value='" + carlevel5[i]['id_leopartsfilter_level5'] + "'>" + carlevel5[i]['name'] + "</option>";
							}
							$("select.carlevel5").html(str);
							$("select.carlevel5").val('');
							$("select.carlevel5").prop('disabled', false);
							$('select.carlevel5').selectpicker("refresh");
						}
						else{
							$("select.carlevel5").prop('disabled', true);
						}
						$(".leo-process").hide();
						$(".leo-partsfilter-submit").removeClass('loading');
						$(".carlevel5-loading").hide();
						refreshSelectFilter();
					},
				});
			}
			else{
				$("select.carlevel5").prop('disabled', true);
				$("select.carlevel5").val('');
			}
		}
		refreshSelectFilter();
	});


	if($(".allow_search_form").val() == 5){
		$("select.carlevel5").change(function(){
			var el = $(this);
			$("select.carlevel5").val(el.val());
			refreshSelectFilter();
			if(typeof($(".leo-partsfilter-submit-1").html()) == "undefined"){
				changeAjax(el);
				refreshSelectFilter();
			}
		});	
	}

	$(".leo-filter-order").change(function(){
		changeAjax($(this));
	});

	$(".leo-partsfilter-submit-1 .label-primary").click(function(){
		$status = true;
		$('.leo-partsfilter select').each(function(){
			if (!$(this).val()) {
				$status = false;
			}
		});
		if ($status) {
			changeAjax($(this));
		} else {
			$('.leo-filter-content').html($('.noneSelectedText').val());
			$('.leo-filter-display').addClass('active');
		}
	});

	function refreshSelectFilter(){
		$('select.carMake').selectpicker("refresh");
		$('select.carModel').selectpicker("refresh");
		$('select.carYear').selectpicker("refresh");
		$('select.carDevice').selectpicker("refresh");
		$('select#carlevel5').selectpicker("refresh");
	}
	function changeAjax(el){
		if($(".filter_url").val() && 0){
			var url = $(".filter_url").val();
		}else{
			var url = 'index.php?fc=module&module=leopartsfilter&controller=search';
		}
	    if($("#carMake").length)  url += "&make=" + $("#carMake").val();
	    if($("#carModel").length) url += "&model=" + $("#carModel").val();
	    if($("#carYear").length)  url += "&year=" + $("#carYear").val();
	    if($("#carDevice").length)  url += "&device=" + $("#carDevice").val();
	    if($("#carlevel5").length)  url += "&level5=" + $("#carlevel5").val();
	    if($(".leo-filter-order").length)  url += "&order=" + $(".leo-filter-order").val();

	    if($(".ajaxsearch").val() > 0 && typeof(el.closest('#left-column').html()) == 'undefined'){
	    	$('.leo-filter-display').addClass('active');
	        $('.leo-filter-content').html('<img class="loadding" src="' + $(".base_url_module").val() + 'views/img/loader_3.gif">');

		    $.ajax({
		    	url: url, 
		    	dataType: 'html', 
		    	success: function(response) { 
		    		$('.leo-filter-content').html($(response).find('#js-product-list').html()); 
		    	} 
		    });
	    }else{
	    	window.location.href = url.replace('ajaxsearch','noneajax');
	    }
	    
	}
	
	$(".filter-type-1").click(function(){
		$(this).closest(".container-filter").find('.filter-type-1-content').addClass('active');
		$(this).closest(".container-filter").find('.filter-type-2-content').removeClass('active');
		$(this).closest(".container-filter").find('.filter-type-1').addClass('active');
		$(this).closest(".container-filter").find('.filter-type-2').removeClass('active');
		$(".filter-type-1").addClass('active');
		$(".filter-type-2").removeClass('active');
		$('#filter-type-1').addClass('active');
		$('#filter-type-2').removeClass('active');
		return false;
	});
	
	$(".filter-type-2").click(function(){
		$(this).closest(".container-filter").find('.filter-type-2-content').addClass('active');
		$(this).closest(".container-filter").find('.filter-type-1-content').removeClass('active');
		$(this).closest(".container-filter").find('.filter-type-2').addClass('active');
		$(this).closest(".container-filter").find('.filter-type-1').removeClass('active');
		return false;
	});
 	
 	var timeout = null;
	$(".free-search-input").keydown(function(){
		if($(".ajaxsearch").val() > 0){
			clearTimeout(timeout);
		    timeout = setTimeout(function () {
		        if($('.free-search-input').val().length >=3){
		        	if($(".ajaxsearch").val() && $(".ajaxsearch").val() > 0){
		        		$('.leo-filter-display').addClass('active');
			        	$('.leo-filter-content').html('<img class="loadding" src="' + $(".base_url_module").val() + 'views/img/loader_3.gif">');

			        	if($(".filter_url").val() && 0){
							var url = $(".filter_url").val();
						}else{
							var url = 'index.php?fc=module&module=leopartsfilter&controller=search';
						}

			        	$.ajax({
					    	url: url + '&s=' + $('.free-search-input').val(), 
					    	dataType: 'html', 
					    	success: function(response) { 
					    		$('.leo-filter-content').html($(response).find('#js-product-list').html()); 	
					    	} 
					    });
		        	}else{
		        		window.location.href = url + '&s=' + $('.free-search-input').val();
		        	}
		        	
		        }
		    }, 500);
		}
	});

	$(".leo-partsfilter-submit-2 .label-primary").click(function(){
		if($(".filter_url").val() && 0){
			var url = $(".filter_url").val();
			url = url.replace('ajaxsearch','');
		}else{
			var url = 'index.php?fc=module&module=leopartsfilter&controller=search';
		}
		window.location.href = url + '&s=' + $('.free-search-input').val();
	});

	$("#left-column .leo-filter-display").remove();
});


