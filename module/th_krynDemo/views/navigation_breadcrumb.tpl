<a href="{Kryn::$domain->getUrl()}">{Kryn::$domain->getDomain()}</a>

{foreach from=$breadcrumbs item=crumb}
    » <a href="{$crumb->getFullUrl()}">{$crumb->getTitle()}</a>
{/foreach}
