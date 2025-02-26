{* 
* @Module Name: Leo Parts Filter
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2022 Leotheme
*}

<fieldset>
    <div id="leopartsfilterimport">
    	{if isset($status) && $status == '0'}
    		<div class="messenger error">{l s='Your your file error. please view guide how to create' mod='leopartsfilter'}</div>
    	{/if}
    	
    		<form action="" method="post" enctype="multipart/form-data">
		    	<div>
		    		<input type="checkbox" name="removedata" value="1"> {l s='Remove all data before import' mod='leopartsfilter'}
		    	</div>
		    	<br/>
	    		<div>
					<label>{l s='Upload file xls' mod='leopartsfilter'} (<a class="goto-guide" href="https://www.leo___theme.com/guides/prestashop16/leo_parts_filter/#!/create-import-file" target="blank">{l s='How to create xls file?' mod='leopartsfilter'}</a>)</label>
					<input type="file" name="file_import"/>
				</div>
				<br/>

				<div>
					<button type="submit">{l s='Upload' mod='leopartsfilter'}</button>
				</div>
	    	</form>
	    	<br/>
	    	<br/>
	    	
	    	<div class="run-import">
				<button class="import">Import</button>
				<button class="delete">Delete</button>
			</div>
			<br/>
	    	<br/>
    		<table class="leo-imports">
    			<thead>
    				<tr>
    					<th><input type="checkbox" name="checkall" id="checkall"/></th>
    					<th>{l s='Product ID' mod='leopartsfilter'}</th>
    					<th>{l s='Product name' mod='leopartsfilter'}</th>
    					<th colspan="{$lang}">{l s='Level 1' mod='leopartsfilter'}</th>
    					{if $level >=2}<th colspan="{$lang}">{l s='Level 2' mod='leopartsfilter'}</th>{/if}
    					{if $level >=3}<th colspan="{$lang}">{l s='Level 3' mod='leopartsfilter'}</th>{/if}
    					{if $level >=4}<th colspan="{$lang}">{l s='Level 4' mod='leopartsfilter'}</th>{/if}
    					{if $level >=5}<th colspan="{$lang}">{l s='Level 5' mod='leopartsfilter'}</th>{/if}
    					<th>{l s='Status' mod='leopartsfilter'}</th>
    					<th></th>
    				</tr>
    			</thead>
    			<tbody>
	    		{foreach from=$data item=items}
	    			<tr class="{if $items.status == 1}row-imported{/if} row-import-{$items.id}">
	    				{foreach from=$items key=k item=value}
	    					{if $k == 'id'}
	    						<td>
		    						<input class="select-row" type="checkbox" name="ids[]" value="{$value}"/>
	    						</td>
	    					{else}
		    					{if $k == 'level1' || $k == 'level2' || $k == 'level3' || $k == 'level4' || $k == 'level5'}
		    						{foreach from=$value item=lang}
		    							<td>{$lang}</td>
		    						{/foreach}
		    					{else}
		    						<td>
			    						{if $k == 'status'}
			    							<span class="complete-import-mess" data-content="{l s='Complete' mod='leopartsfilter'}">
			    							{if $value == 1}
			    								{l s='Complete' mod='leopartsfilter'}
			    							{/if}
			    							</span>
			    						{else}
			    							{$value}
			    						{/if}
		    						</td>
		    					{/if}
		    				{/if}
	    				{/foreach}
	    				<td><a target="_blank" href="{$edit_link}&id={$items.id}">Edit</a></td>
	    			</tr>
				{/foreach}
				</tbody>
			</table>

			{if $pagination}
				<div class="gold-pagination">
					<form class="limit-pagination" action="{$url}" method="GET">
						<input type="hidden" name="controller" value="AdminLeopartsfilterImport">
						<input type="hidden" name="token" value="{$token}">
						<select class="limit-select" name="limit">
							<option {if $pagination.limit == 50} selected="selected" {/if} value="50">50</option>
							<option {if $pagination.limit == 100} selected="selected" {/if} value="100">100</option>
							<option {if $pagination.limit == 200} selected="selected" {/if} value="200">200</option>
							<option {if $pagination.limit == 300} selected="selected" {/if} value="300">300</option>
							<option {if $pagination.limit == 500} selected="selected" {/if} value="500">500</option>
							<option {if $pagination.limit == -1} selected="selected" {/if} value="-1">All</option>
						</select>
						
					</form>
					<ul>
						<li class="start"><a href="{$url}&page=1&limit={$pagination.limit}">{l s='Start' mod='leopartsfilter'}</a></li>
						{foreach from=$pagination.listpage item=item}
							<li {if $item == $pagination.page} class="active" {/if}><a href="{$url}&page={$item}&limit={$pagination.limit}">{$item}</a></li>
						{/foreach}
						<li class="end"><a href="{$url}&page={$pagination.totalpage}&limit={$pagination.limit}">{l s='End' mod='leopartsfilter'}</a></li>
					</ul>
					<div class="count-page">{l s='Page' mod='leopartsfilter'} {$pagination.page}/{$pagination.totalpage}</div>
				</div>
			{/if}
			
			<div class="run-import">
				<button class="import">Import</button>
				<button class="delete">Delete</button>
				<input class="url" type="hidden" value="{$url}">
			</div>
    </div>
</fieldset>
<div class="clear"><br/></div>