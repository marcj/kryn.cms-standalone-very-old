<a href="/">{$domain.domain}</a>
{foreach from=$menus[$page.rsn] item=menu}
    » 
    <a href="{$menu|@realUrl}">{$menu.title}</a>
{/foreach}
