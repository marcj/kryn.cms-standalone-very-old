{foreach from=$navigation->getChildren() item=children  name="subnavi"}
<a class="{if $children->isActive()} active{/if}" href="{$children->getFullUrl()}">{$children->getTitle()}  </a>
    {if !$smarty.foreach.subnavi.last}|{/if}
{/foreach}
