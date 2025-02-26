{* 

* @Module Name: Leo Parts Filter

* @Website: leotheme.com.com - prestashop template provider

* @author Leotheme <leotheme@gmail.com>

* @copyright  2007-2022 Leotheme

*}

{extends file=$layout}

{block name='head_microdata_special'}
  {include file='_partials/microdata/product-list-jsonld.tpl' listing=$listing}
{/block}

{block name='content'}
  <section id="main"{if isset($profile_params.c_config) && isset($profile_params.c_config.class)}class="{$profile_params.c_config.class}"{/if}>
    
    {if (isset($profile_params.c_config) && $profile_params.c_config.category_position == 1) || !isset($profile_params.c_config)}
    {block name='product_list_header'}
      <h1 id="js-product-list-header" class="h2">{$listing.label}</h1>
    {/block}
    {/if}

    {if (isset($profile_params.c_config) && $profile_params.c_config.scategory_position == 1) || !isset($profile_params.c_config)}
    {block name='subcategory_list'}
      {if isset($subcategories) && $subcategories|@count > 0}
        {include file='catalog/_partials/subcategories.tpl' subcategories=$subcategories}
      {/if}
    {/block}
    {/if}
    
    {hook h="displayHeaderCategory"}

    {if isset($profile_params.c_config) && $profile_params.c_config.filter_position == 2 && isset($listing.rendered_facets) && $listing.rendered_facets}
      <div id="horizontal_filters">
        {if $layout == 'layouts/layout-full-width.tpl' || $layout == 'layouts/layout-right-column.tpl'}
        {$listing.rendered_facets nofilter}
        {/if}
      </div>
    {/if}
    {if (isset($profile_params.c_config) && $profile_params.c_config.filter_position != 2) || !isset($profile_params.c_config)}
    <section id="products">
      {if $listing.products|count}

        {block name='product_list_top'}
          {include file='catalog/_partials/products-top.tpl' listing=$listing}
        {/block}

        {block name='product_list_active_filters'}
          <div class="hidden-sm-down">
            {$listing.rendered_active_filters nofilter}
          </div>
        {/block}

        {block name='product_list'}
          {include file='catalog/_partials/products.tpl' listing=$listing productClass="col-xs-6 col-xl-4"}
        {/block}

        {block name='product_list_bottom'}
          {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
        {/block}

      {else}
        <div id="js-product-list-top"></div>

        <div id="js-product-list">
          {capture assign="errorContent"}
            <h4>{l s='No products available yet' d='Shop.Theme.Catalog'}</h4>
            <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>
          {/capture}

          {include file='errors/not-found.tpl' errorContent=$errorContent}
        </div>

        <div id="js-product-list-bottom"></div>
      {/if}
    </section>
    {/if}
    {if (isset($profile_params.c_config) && $profile_params.c_config.category_position == 2)}
    {block name='product_list_header'}
      <h1 id="js-product-list-header" class="h2">{$listing.label}</h1>
    {/block}
    {/if}

    {if (isset($profile_params.c_config) && $profile_params.c_config.scategory_position == 2)}
    {block name='subcategory_list'}
      {if isset($subcategories) && $subcategories|@count > 0}
        {include file='catalog/_partials/subcategories.tpl' subcategories=$subcategories}
      {/if}
    {/block}
    {/if}


    {block name='product_list_footer'}{/block}
    {hook h="displayFooterCategory"}

  </section>
{/block}