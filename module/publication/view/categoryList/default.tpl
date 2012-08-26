<div class="publicationCategoryList">

    <div>
        <a {if $request.e2 eq ""}class="active"{/if} href="{$pConf.listPage|realUrl}/">&raquo; [[All entries]]</a>
    </div>
    {foreach from=$categories item=category}
        <div>
            <a {if $request.e2 == $category.url}class="active"{/if} href="{$pConf.listPage|realUrl}/category/{$category.url}">&raquo; {$category.title} ({$category.count})</a>
        </div>
    {/foreach}

</div>