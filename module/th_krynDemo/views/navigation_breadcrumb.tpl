<a href="{Kryn::getBaseUrl()}">{Kryn::$domain->getDomain()}</a>

{foreach from=$breadcrumbs item=crumb}
    » <a href="{$crumb|@realUrl}">{$crumb.title}</a>
{/foreach}
