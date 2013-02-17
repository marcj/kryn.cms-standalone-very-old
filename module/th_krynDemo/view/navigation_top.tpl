{if $navigation}
{foreach from=$navigation->getLinks() item=children}
   <a class="{if $children->isActive()} active{/if}" href="object://Core.Node/{$children->getId()}">{$children->getTitle()}</a>
{/foreach}
{/if}