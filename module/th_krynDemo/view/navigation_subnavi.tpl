{foreach from=$navigation->getLinks() item=children  name="subnavi"}
<a class="{if $children->isActive()} active{/if}" href="object://Core.Node/{$children->getId()}">{$children->getTitle()}</a>
    {if !$smarty.foreach.subnavi.last}|{/if}
{/foreach}