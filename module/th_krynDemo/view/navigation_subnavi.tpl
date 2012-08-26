{foreach from=$navigation->getLinks() item=children  name="subnavi"}
<a class="{if $children->isActive()} active{/if}" href="{$children->getFullUrl()}">{$children->getTitle()}</a>
    {if !$smarty.foreach.subnavi.last}|{/if}
{/foreach}