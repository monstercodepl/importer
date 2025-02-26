{* 
* @Module Name: Leo Parts Filter
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2022 Leotheme
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}
        {if $input.type == 'cw_link'}
            {if isset($input.options)}
                <ol class="breadcrumb leo-redirect">
                {foreach $input.options AS $option}
                    <li><a target="{$option.target}" href="{$option.link}">{$option.title|escape:'html':'UTF-8'}</a></li>
                {/foreach}
                </ol>
            {/if}
        {/if}
        {if $input.type == 'cw_update'}
            <a style="display: inline-block;padding: 10px 15px;background: #2eacce;color: #fff;border-radius: 3px;" href="{$input.link}">{$input.title}</a>
        {/if}
	{$smarty.block.parent}
{/block}