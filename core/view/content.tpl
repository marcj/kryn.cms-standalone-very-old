{if $content->getTitle()}<h2>{$content->getTitle()}</h2>{/if}
{if $content->getType() eq 'text' OR $content->getType() eq 'html'}
    {$content->getContent()|replace:'[[':'[<!-- -->['}
{/if}
{if $content->getType() eq 'navigation'}
    {assign var="options" value=$content->getContent()|json_decode}

    {navigation id=$options.entryPoint}
{/if}