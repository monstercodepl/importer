{* 
* @Module Name: Leo Parts Filter
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2022 Leotheme
*}

<fieldset>
    <div class="leopartsfilterexport">
    	<div class="leopartsfilterexport-messenger">
    		<form id="leo-head-filter" action="" method="get" enctype="multipart/form-data">
    			<input type="hidden" name="controller" value="AdminLeopartsfilterExport">
	    		<input type="hidden" name="token" value="{$token}">

	    		<table>
	    			<thead>
	    				<tr>
	    					<th>{l s='Level 1' mod='leopartsfilter'}</th>
	    					{if $level >=2}<th>{l s='Level 2' mod='leopartsfilter'}</th>{/if}
	    					{if $level >=3}<th>{l s='Level 3' mod='leopartsfilter'}</th>{/if}
	    					{if $level >=4}<th>{l s='Level 4' mod='leopartsfilter'}</th>{/if}
	    					{if $level >=5}<th>{l s='Level 5' mod='leopartsfilter'}</th>{/if}
	    					<th></th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
	    					<td>
	    						{if $leopartsfilter_make}
					    			<select name="lv1" id="leopartsfilter_make" class="import_column_item">
					    				<option value="">{l s='All level 1' mod='leopartsfilter'}</option>
					    				{foreach from=$leopartsfilter_make key=k item=value}
					    					<option {if $lv1 == $value.id_leopartsfilter_make}selected="selected"{/if} value="{$value.id_leopartsfilter_make}">{$value.name}</option>
					    				{/foreach}
					    			</select>
					    		{/if}
	    					</td>
	    					{if $level >=2}
	    						<td>
	    							<select name="lv2" id="leopartsfilter_model" class="import_column_item">
					    				<option>{l s='All level 2' mod='leopartsfilter'}</option>
					    				{if $leopartsfilter_model}
						    				{foreach from=$leopartsfilter_model key=k item=value}
						    					<option {if $lv2 == $value.id_leopartsfilter_model}selected="selected"{/if} value="{$value.id_leopartsfilter_model}">{$value.name}</option>
						    				{/foreach}
					    				{/if}
					    			</select>
	    						</td>
	    					{/if}
	    					{if $level >=3}
	    						<td>
	    							<select name="lv3" id="leopartsfilter_year" class="import_column_item">
					    				<option value="">{l s='All level 3' mod='leopartsfilter'}</option>
					    				{if $leopartsfilter_year}
						    				{foreach from=$leopartsfilter_year key=k item=value}
						    					<option {if $lv3 == $value.id_leopartsfilter_year}selected="selected"{/if} value="{$value.id_leopartsfilter_year}">{$value.name}</option>
						    				{/foreach}
					    				{/if}
					    			</select>
	    						</td>
	    					{/if}
	    					{if $level >=4}
	    						<td>
	    							<select name="lv4" id="leopartsfilter_device" class="import_column_item">
					    				<option value="">{l s='All level 4' mod='leopartsfilter'}</option>
					    				{if $leopartsfilter_device}
						    				{foreach from=$leopartsfilter_device key=k item=value}
						    					<option {if $lv4 == $value.id_leopartsfilter_device}selected="selected"{/if} value="{$value.id_leopartsfilter_device}">{$value.name}</option>
						    				{/foreach}
					    				{/if}
					    			</select>
	    						</td>
	    					{/if}
	    					{if $level >=5}
	    						<td>
	    							<select name="lv5" id="leopartsfilter_level5" class="import_column_item">
					    				<option value="">{l s='All level 5' mod='leopartsfilter'}</option>
					    				{if $leopartsfilter_level5}
						    				{foreach from=$leopartsfilter_level5 key=k item=value}
						    					<option {if $lv5 == $value.id_leopartsfilter_level5}selected="selected"{/if} value="{$value.id_leopartsfilter_level5}">{$value.name}</option>
						    				{/foreach}
					    				{/if}
					    			</select>
	    						</td>
	    					{/if}
	    				</tr>
	    			</tbody>
	    		</table>
    		</form>
    		<form action="" method="POST" enctype="multipart/form-data">
    			<input type="hidden" name="controller" value="AdminLeopartsfilterExport">
	    		<input type="hidden" name="token" value="{$token}">
	    		<input type="hidden" name="lv1" value="{$lv1}">
	    		{if $level >=2}<input type="hidden" name="lv2" value="{$lv2}">{/if}
	    		{if $level >=3}<input type="hidden" name="lv3" value="{$lv3}">{/if}
	    		{if $level >=4}<input type="hidden" name="lv4" value="{$lv4}">{/if}
	    		{if $level >=5}<input type="hidden" name="lv5" value="{$lv5}">{/if}
	    		<input type="hidden" name="act" value="export">
	    		<button type="submit" class="leo-export-button">{l s='Export' mod='leopartsfilter'}</button>
	    	</form>
	    </div>
	    <br/>
    	{if $file_name}
	    	<div class="leopartsfilterexport-messenger">
		    	<div class="leo-messenger-export">{l s='Export file success' mod='leopartsfilter'}</div>
		    	<div>
					<a href="{$base_url}modules/leopartsfilter/export_file/{$file_name}" class="leo-download-file">{l s='Download' mod='leopartsfilter'}</a>
				</div>
			</div>
		{/if}

		{if $file_delete}
	    	<div class="leopartsfilterexport-messenger">
		    	<div class="leo-messenger-delete">{l s='Deleteed success file' mod='leopartsfilter'}{$file_delete}</div>
			</div>
		{/if}
		

		<div class="leopartsfilterexport-list">
			<h4>List file export</h4>
			<div>
				{if $export_files}
				<table>
					<thead>
						<tr>
							<th>{l s='Create date' mod='leopartsfilter'}</th>
							<th>{l s='File name' mod='leopartsfilter'}</th>
							<th>{l s='Download' mod='leopartsfilter'}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$export_files key=k item=value}
							<tr>
								<td>{$value.create}</td>
								<td>{$value.name}</td>
								<td><a href="{$value.url}">{l s='Download' mod='leopartsfilter'}</a></td>
								<td style="width: 80px;"><a href="{$url}&filename={$value.name}&action=deletefile">{l s='Delete' mod='leopartsfilter'}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
				{/if}
			</div>
		</div>
    </div>
</fieldset>
