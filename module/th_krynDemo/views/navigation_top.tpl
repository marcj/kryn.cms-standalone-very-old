
{foreach from=$navigation->getChildren() item=children}
   <a class="{if $children->isActive()} active{/if}" href="{$children->getFullUrl()}">{$children->getTitle()} 2</a>
{/foreach}
