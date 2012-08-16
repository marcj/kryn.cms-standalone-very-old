{function name=tree depth=0}
<ul>
  {foreach from=$navi._children item=link name="subnavi"}
  <li>
    <a class="{if $link|@active} active{/if}" title="{$link.title}" href="{$link|@realUrl}">{$link.title}</a>
    {if $link._children}{call name=tree navi=$link depth=$depth+1}{/if}
  </li>
  {/foreach}
</ul>
{/function}
{call name=tree navi=$navi depth=0}
​