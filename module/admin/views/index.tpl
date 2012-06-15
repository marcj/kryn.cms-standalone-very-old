<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
    <title>{if $cfg.systemtitle}{$cfg.systemtitle} | {/if}kryn.cms administration</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


    <script type="text/javascript" src="{$cfg.templatepath}/kryn/mootools-core.js" ></script>
    <script type="text/javascript" src="{$cfg.templatepath}/kryn/mootools-more.js" ></script>


    <script type="text/javascript" src="{$cfg.path}lib/mooeditable/Source/MooEditable/MooEditable.js"></script>
    <script type="text/javascript" src="{$cfg.path}lib/mooeditable/Source/MooEditable/MooEditable.UI.MenuList.js"></script>
    <script type="text/javascript" src="{$cfg.path}lib/mooeditable/Source/MooEditable/MooEditable.Extras.js"></script>
    <script type="text/javascript" src="{$cfg.path}lib/mooeditable/Source/MooEditable/MooEditable.Image.js"></script>
    <script type="text/javascript" src="{$cfg.path}lib/mooeditable/Source/MooEditable/MooEditable.Table.js"></script>

    <script type="text/javascript" src="{$cfg.path}krynJavascriptGlobalPath.js?noCache={$time}" ></script>
    <script type="text/javascript" src="{$cfg.path}admin/getPossibleLangs:1/?noCache={$time}" ></script>
    <script type="text/javascript" src="{$cfg.path}admin/getLanguage:{if $smarty.cookies.kryn_language}{$smarty.cookies.kryn_language}{else}{$adminLanguage}{/if}/js:1/" ></script>
    <script type="text/javascript" src="{$cfg.path}admin/getLanguagePluralForm:{if $smarty.cookies.kryn_language}{$smarty.cookies.kryn_language}{else}{$adminLanguage}{/if}/js:1/" ></script>

    <script type="text/javascript" src="{$cfg.path}lib/codemirror/lib/codemirror.js"></script>
    <script type="text/javascript" src="{$cfg.path}lib/codemirror/lib/util/loadmode.js"></script>


    <script type="text/javascript" src="{$path}media/admin/js/ka.ai.js?nc=1.0" ></script>
    <script type="text/javascript" src="{$path}media/admin/js/ka.js?nc=1.0" ></script>
    <script type="text/javascript" src="{$path}media/admin/js/ka.Button.js" ></script>
    <script type="text/javascript" src="{$path}media/admin/js/ka.Select.js" ></script>
    <script type="text/javascript" src="{$path}media/admin/js/ka.Checkbox.js" ></script>

    <link rel="stylesheet" type="text/css" href="{$cfg.path}lib/codemirror/lib/codemirror.css">


    <link rel="stylesheet" type="text/css" href="{$cfg.path}lib/mooeditable/Assets/MooEditable/MooEditable.css">
    <link rel="stylesheet" type="text/css" href="{$cfg.path}lib/mooeditable/Assets/MooEditable/MooEditable.Extras.css">
    <link rel="stylesheet" type="text/css" href="{$cfg.path}lib/mooeditable/Assets/MooEditable/MooEditable.SilkTheme.css">
    <link rel="stylesheet" type="text/css" href="{$cfg.path}lib/mooeditable/Assets/MooEditable/MooEditable.Image.css">
    <link rel="stylesheet" type="text/css" href="{$cfg.path}lib/mooeditable/Assets/MooEditable/MooEditable.Table.css">

    <link rel="stylesheet" type="text/css" href="{$cfg.templatepath}/admin/css/ka.ai.css" />

    <!--[if gte IE 9]>
    <style type="text/css">
        .gradient {
            filter: none;
        }
    </style>
    <![endif]-->

    <script type="text/javascript" >
        window._session = new Hash();
        window._session.user_rsn = {$user.rsn+0};
        window._session.username = '{$user.username}';
        window._session.sessionid = '{$client->token}';
        window._session.tokenid = '{$client->tokenid}';
        window._session.lang = '{if $smarty.cookies.kryn_language}{$smarty.cookies.kryn_language}{else}{$adminLanguage}{/if}';
        window._session.lastlogin = '{$user.lastlogin}';
        {if $noAdminAccess}
        window._session.noAdminAccess = true;
        {/if}

        CodeMirror.modeURL = "{$cfg.path}lib/codemirror/mode/%N/%N.js";
    </script>

    <link rel="stylesheet" type="text/css" href="{$cfg.templatepath}/admin/css/ka.login.css" />
    <link rel="stylesheet" type="text/css" href="{$cfg.templatepath}/admin/css/ka.Button.css" />
    <link rel="stylesheet" type="text/css" href="{$cfg.templatepath}/admin/css/ka.Select.css" />
    <link rel="stylesheet" type="text/css" href="{$cfg.templatepath}/admin/css/ka.Checkbox.css" />
    <link rel="SHORTCUT ICON" href="{$cfg.templatepath}/admin/images/favicon.ico" />

{$adminHeader}

</head>
<body>
<div class="border" style="display: none" id="border">
    <div class="header gradient" id="header">
        <div class="header-inner">
            <div class="headRight" id="iconbar">
                <a style="padding-right: 2px; width: 15px;" href="javascript: ka.openFrontend();" title="[[Frontend]]">
                    <img src="{$path}media/admin/images/icons/eye_bw.png" width="14" /></a>
                <a href="javascript:;" style="padding:0px 8px;" id="user-username"></a>
                <div class="ka-search">
                    <img class="ka-search-query-icon" src="{$path}media/admin/images/icon-search-loupe.png" />
                    <input type="text" class="text" id="ka-search-query" />
                </div>
                <div class="iconbar-item" style="width: 48px;">
                    <a href="javascript:;" onclick="ka.wm.open('admin/help');" class="ka-help-icon" title="[[Help]]"></a>
                    <a href="javascript:;" onclick="ka.clearCache();" id="ka-btn-clear-cache" class="ka-cache-icon" title="[[Clear cache]]"></a>
                    <a href="javascript:;" onclick="ka.openSearchContext();" id="ka-btn-create-search-index" class="ka-create-search-index-icon" title="[[Searchengine options]]"></a>
                </div>
                <div class="iconbar-item" id="serverTime"></div>
            </div>
            <div style="clear: both"></div>
            <div class="mainlinks" id="mainLinks"></div>
        </div>
    </div>
    <div class="windowList" id="windowList"></div>

    <div class="middle" id="middle">
        <div class="content ka-desktop" id="desktop"></div>
    </div>
</div>
</body>
</html>
