{addJs file='publication/news/js/list/default.js'}
{addCss file='publication/news/css/list/default.css'}

{capture name=publicationNavi}
    {if $pages > 1 }
    <div class="publicationNewsListDefaultNavi">
        {section name=newspage start=1 loop=$pages+1 max=$pConf.maxPages}
            {if $currentNewsPage == $smarty.section.newspage.index }
                <span>{$smarty.section.newspage.index}</span>
            {else}
                <a href="{$page|@realUrl}/{if $request.e2}category/{$request.e2}/{/if}{$smarty.section.newspage.index}/">{$smarty.section.newspage.index}</a>
            {/if}
        {/section}
    </div>
    {/if}
{/capture}

{if $category_title}
    <h3 style="text-align: center;color: gray">All in category <i>{$category_title}</i></h3>
{/if}

{$smarty.capture.publicationNavi}


<div class="publicationNewsListDefault">
{foreach from=$items item=item name="newsloop"}

    <div class="publicationNewsListDefaultItem" {if $smarty.foreach.newsloop.last}style="border: 0px;"{/if}>
    
        
        <h2><a class="publicationNewsListDefaultItemLink" href="{$pConf.detailPage|realUrl}/{$item.title|escape:"rewrite"}/{$item.id}/" >{$item.title}</a></h2>
        <div class="publicationNewsListDefaultItemDate">at
        <a>{$item.releasedate|date_format:"%B %d, %Y"}</a></div>
        {* %H:%M *}
        
        <div class="publicationNewsListDefaultItemIntro">
            {if $item.introimage ne ""}
                <img src="{$item.introimage}" class="publicationNewsListDefaultItemIntroImage" align="left" />
            {/if}
            {$item.intro}
            <div style="clear: both;"></div>
        </div>
        
        <div class="publicationNewsListDefaultItemBottom">
            [[Category]] <a href="{$pConf.detailPage|realUrl}/category/{$item.category_url}">{$item.category_title}</a>
            {if $item.tags}
                
                [[with tags]] <span style="color: gray">{$item.tags}</span>
            {else}
            
                [[with no tags]]
            {/if}
            
            <a style="float: right;" href="{$pConf.detailPage|realUrl}/{$item.title|escape:"rewrite"}/{$item.id}/">[[read more]]</a>
            
        </div>
    </div>
    
{/foreach}
</div>

{$smarty.capture.publicationNavi}


{if $pConf.enableRss == 1}
{unsearchable}
<div class="publicationNewsListDefaultRss">
     <a href="{$page|@realUrl}/publication_rss:1" class="publicationNewsListDefaultRssLink">[[RSS-Feed]]</a>
</div>
{/unsearchable}
{/if}