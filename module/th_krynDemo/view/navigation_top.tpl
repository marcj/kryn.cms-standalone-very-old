
{foreach from=$navigation->getLinks() item=children}
   <a class="{if $children->isActive()} active{/if}" href="{$children->getFullUrl()}">{$children->getTitle()}</a>
{/foreach}
