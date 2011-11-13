<a href="{$domain.path}">{$domain.domain}</a>
{foreach from=$breadcrumbs item=crumb}
    » <a href="{$crumb|@realUrl}">{$crumb.title}</a>
{/foreach}
