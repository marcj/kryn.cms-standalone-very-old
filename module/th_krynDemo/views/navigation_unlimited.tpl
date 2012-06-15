{function name=tree depth=0}
<ul>
  {foreach from=$navi.links item=link name="subnavi"}
  <li>
    <a class="{if $link|@active} active{/if}" title="{$link.title}" href="{$link|@realUrl}">{$link.title}</a>
    {if $link.links}{call name=tree navi=$link depth=$depth+1}{/if}
  </li>
  {/foreach}
</ul>
{/function}
{call name=tree navi=$navi depth=0}
​