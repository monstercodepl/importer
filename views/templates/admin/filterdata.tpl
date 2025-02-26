{* 
* @Module Name: Leo Parts Filter
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2022 Leotheme
*}

<div class="panel">
    <h4>{l s='Set Product Parts Filter Data (Please Save Parts data after save product First)' mod='leopartsfilter'}</h4>
    <div class="separation"></div>
    <table id="attribute"  class="table product">
      <thead>
        <tr>
          {if $allow_search_form >= 1}<td class="left"><b>{l s='Level 1' mod='leopartsfilter'}</b></td>{/if}
          {if $allow_search_form >= 2}<td class="left"><b>{l s='Level 2' mod='leopartsfilter'}</b></td>{/if}
          {if $allow_search_form >= 3}<td class="left"><b>{l s='Level 3' mod='leopartsfilter'}</b></td>{/if}
          {if $allow_search_form >= 4}<td class="left"><b>{l s='Level 4' mod='leopartsfilter'}</b></td>{/if}
          {if $allow_search_form >= 5}<td class="left"><b>{l s='Level 5' mod='leopartsfilter'}</b></td>{/if}
          <td style="float:right;">
            <button onclick="addAttribute();" type="button" data-size="s" data-style="expand-right" class="btn btn-default">
                <span class="ladda-label">
                    <i class="icon-plus-sign"></i>
                    Add New
                </span>
                <span class="ladda-spinner"></span>
            </button>
          </td>
        </tr>
      </thead>
      
      {if $fdata != ''}
      	{foreach from=$fdata key=id item=value}
					<tbody id="attribute-row{$id}">
						<tr>
							{if $allow_search_form >= 1}
							<td class="left">
								<select class="filter_make_attribute" id="make_{$id}" name="partsfilter[{$id}][make]" onchange="loadModel(this,{$id});">
									<option value="">{$make_ddl_default_text}</option>
									{foreach from=$make_list key=m_id item=m_value}
										<option {if $m_value.mid == $value.id_leopartsfilter_make}selected='selected'{/if} value="{$m_value.mid}">{$m_value.name}</option>
									{/foreach}
								</select>
							</td>
							{/if}
							{if $allow_search_form >= 2}
							<td class="left">
								<select class="filter_model_attribute" id="model_{$id}" name="partsfilter[{$id}][model]" onchange="loadYear(this,{$id});">
									<option value="">{$model_ddl_default_text}</option>
									{foreach from=$value.model key=model_id item=model_value}
										<option {if $model_value.id_leopartsfilter_model == $value.id_leopartsfilter_model}selected{/if} value="{$model_value.id_leopartsfilter_model}">{$model_value.name}</option>
									{/foreach}
								</select>
							</td>
							{/if}
							{if $allow_search_form >= 3}
							<td class="left">
								<select class="filter_year_attribute" id="year_{$id}" name="partsfilter[{$id}][year]" onchange="loadDevice(this,{$id});">
									<option value="">{$year_ddl_default_text}</option>
									{foreach from=$value.year key=year_id item=year_value}
										<option {if $year_value.id_leopartsfilter_year == $value.id_leopartsfilter_year}selected{/if} value="{$year_value.id_leopartsfilter_year}">{$year_value.name}</option>
									{/foreach}
								</select>
							</td>
							{/if}
							{if $allow_search_form >= 4}
							<td class="left">
								<select class="filter_device_attribute" id="device_{$id}" name="partsfilter[{$id}][device]" onchange="loadLevel5(this,{$id});">
									<option value="">{$device_ddl_default_text}</option>
									{foreach from=$value.device key=device_id item=device_value}
										<option {if $device_value.id_leopartsfilter_device == $value.id_leopartsfilter_device}selected{/if} value="{$device_value.id_leopartsfilter_device}">{$device_value.name}</option>
									{/foreach}
								</select>
							</td>
							{/if}

							{if $allow_search_form >= 5}
							<td class="left">
								<select class="filter_level5_attribute" id="level5_{$id}" name="partsfilter[{$id}][level5]">
									<option value="">{$level5_ddl_default_text}</option>
									{foreach from=$value.level5 key=level5 item=level5_value}
										<option {if $level5_value.id_leopartsfilter_level5 == $value.id_leopartsfilter_level5}selected{/if} value="{$level5_value.id_leopartsfilter_level5}">{$level5_value.name}</option>
									{/foreach}
								</select>
							</td>
							{/if}

							

							<td class="left">
								<span onclick="$('#attribute-row{$id}').remove();" style="cursor:pointer;" class="pull-right btn btn-default"><i class="icon-trash"></i> Remove</span>
							</td>
						</tr>
					</tbody>

				{/foreach}
			{/if}

      <tfoot>
    </table>
    <br/>
    <br/>
    <div class="panel-footer">
      {*<button class="btn btn-default pull-right" name="submitAddproduct" type="submit"><i class="process-icon-save"></i> Save</button>*}
      <button class="btn btn-default pull-right" style="margin-right:5px;" name="submitAddproductAndStay" type="submit"><i class="process-icon-save"></i> Save and stay</button>
    </div>
</div>


<script type="text/javascript">
var attribute_row = {if isset($value)}{$value}{/if};
function addAttribute() {
	html  = '<tbody id="attribute-row' + attribute_row + '">';
    html += '  <tr>';
	
	html += '    <td class="left"><select class="filter_make_attribute" id="make_' + attribute_row + '" name="partsfilter[' + attribute_row + '][make]" onchange="loadModel(this,' + attribute_row + ');">';
	html += "      <option value=\"\">{$make_ddl_default_text}</option>";
	{foreach from=$make_list item=foo}
	html += '      <option value="{$foo.mid}">{$foo.name}</option>';
	{/foreach}
	html += '    </select></td>';
	{if $allow_search_form >= 2}
	html += '    <td class="left"><select class="filter_model_attribute" disabled="disabled" id="model_' + attribute_row + '" name="partsfilter[' + attribute_row + '][model]" onchange="loadYear(this,' + attribute_row + ');">';
	html += "      <option value=\"\">{$model_ddl_default_text}</option>";
	html += '    </select></td>';
	{/if}
	{if $allow_search_form >= 3}
	html += '    <td class="left"><select class="filter_year_attribute" id="year_' + attribute_row + '" disabled="disabled" name="partsfilter[' + attribute_row + '][year]" onchange="loadDevice(this,' + attribute_row + ');">';
	html += "      <option value=\"\">{$year_ddl_default_text}</option>";
	html += '    </select></td>';
	{/if}
	{if $allow_search_form >= 4}
	html += '    <td class="left"><select class="filter_device_attribute" id="device_' + attribute_row + '" disabled="disabled" name="partsfilter[' + attribute_row + '][device]" onchange="loadLevel5(this,' + attribute_row + ');">';
	html += "      <option value=\"\">{$device_ddl_default_text}</option>";
	html += '    </select></td>';
	{/if}

	{if $allow_search_form >= 5}
	html += '    <td class="left"><select class="filter_level5_attribute" id="level5_' + attribute_row + '" disabled="disabled" name="partsfilter[' + attribute_row + '][level5]">';
	html += "      <option value=\"\">{$level5_ddl_default_text}</option>";
	html += '    </select></td>';
	{/if}

	html += '    <td class="left"><span onclick="$(\'#attribute-row' + attribute_row + '\').remove();" style="cursor:pointer;" class="pull-right btn btn-default"><i class="icon-trash"></i> Remove</span></td>';
    html += '  </tr>';	
    html += '</tbody>';
	
	$('#attribute tfoot').before(html);
	
	attribute_row++;
}
function loadModel(obj,row){
	$.ajax({
		type: "POST",
		url: "{$ajax_url}",
		data: "action=adminajax&active=all&makeid=" +  obj.value,
		success: function(response) {
			if (response != "-99"){
				var carModel = JSON.parse(response);
				var str = "";
				for(i=0;i<carModel.length;i++){
					str += "<option value='" + carModel[i]['id_leopartsfilter_model'] + "'>" + carModel[i]['name'] + "</option>";
				}
				$("#model_" + row).html(str);
				$("#model_" + row).prop("disabled", false);
			}
			else{
				alert("Wrong Request");
			}
		},
        });
}
function loadYear(obj,row){
	$.ajax({
		type: "POST",
		url: "{$ajax_url}",
		data: "action=adminajax&active=all&makeid=" + $("#make_" + row).val() + "&modelid=" + obj.value,
		success: function(response) {
			if (response != "-99"){
				var carModel = JSON.parse(response);
				var str = "";
				for(i=0;i<carModel.length;i++){
					str += "<option value='" + carModel[i]['id_leopartsfilter_year'] + "'>" + carModel[i]['name'] + "</option>";
				}
				$("#year_" + row).html(str);
				$("#year_" + row).prop("disabled", false);
			}
			else{
				alert("Wrong Request");
			}
		},
        });
}
function loadDevice(obj,row){
	$.ajax({
		type: "POST",
		url: "{$ajax_url}",
		data: "action=adminajax&active=all&makeid=" + $("#make_" + row).val() + "&modelid=" + $("#model_" + row).val() + '&yearid=' + obj.value,
		success: function(response) {
			if (response != "-99"){
				var carModel = JSON.parse(response);
				var str = "";
				for(i=0;i<carModel.length;i++){
					str += "<option value='" + carModel[i]['id_leopartsfilter_device'] + "'>" + carModel[i]['name'] + "</option>";
				}
				$("#device_" + row).html(str);
				$("#device_" + row).prop("disabled", false);
			}
			else{
				alert("Wrong Request");
			}
		},
        });
}

function loadLevel5(obj,row){
	$.ajax({
		type: "POST",
		url: "{$ajax_url}",
		data: "action=adminajax&active=all&makeid=" + $("#make_" + row).val() + "&modelid=" + $("#model_" + row).val() + '&yearid=' +  $("#year_" + row).val() + '&deviceid=' + obj.value,
		success: function(response) {
			if (response != "-99"){
				var carModel = JSON.parse(response);
				var str = "";
				for(i=0;i<carModel.length;i++){
					str += "<option value='" + carModel[i]['id_leopartsfilter_level5'] + "'>" + carModel[i]['name'] + "</option>";
				}
				$("#level5_" + row).html(str);
				$("#level5_" + row).prop("disabled", false);
			}
			else{
				alert("Wrong Request");
			}
		},
    });
}


</script>
<style type="text/css">
#attribute{
	font-size:12px !important;
}
#attribute tr td {
	padding-top:7px !important;
	padding-left:7px !important;
}
</style>
