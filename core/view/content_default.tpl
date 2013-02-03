<div class="contentElement {$content->getType()}">
    {if $content->getTitle()}
        <h2>{$content->getTitle()}</h2>
    {/if}
    <div class="contentElementContent">

        {include file="core/view/content.tpl"}

    </div>
</div>