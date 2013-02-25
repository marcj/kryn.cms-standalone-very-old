<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>[[404 - Not found]]</title>
    <base href="{Kryn::getBaseUrl()}" />

    {if $backtrace}
        <script type="text/javascript" src="lib/codemirror/lib/codemirror.js"></script>
        <script type="text/javascript" src="lib/codemirror/lib/util/loadmode.js"></script>
        <script type="text/javascript" src="lib/codemirror/lib/util/runmode.js"></script>
        <link rel="stylesheet" type="text/css" href="lib/codemirror/lib/codemirror.css"  />
    {/if}

    <style type="text/css">
        body {
            color: white;
            line-height: 150%;
            font-size: 13px;
            margin: 0px;
            font-family: Verdana, Sans;
            background-color: #22628d;
        }

        #error {
            margin: 5px 50px;
            padding: 5px 45px;
            text-align: left;
        }

        #error > h2 {
            margin-top: 50px;
            color: red;
            text-shadow: 0 1px 0 black;
        }

        .msg {
            color: #444;
            overflow-x: auto;
            background-color: #f7f7f7;
            padding: 15px;
            border-top: 3px solid red;
            border-bottom: 3px solid red;
        }

        .CodeMirror-scroll {
          height: auto;
          overflow-y: hidden;
          overflow-x: auto;
        }

        .CodeMirror .highlightedLine {
            background-color: #ddd;
        }

    </style>
</head>
<body>
<div id="error">
    <img src="core/images/logo_white.png" />
    <h2>{$title}</h2>
    <div class="msg"><pre style="white-space: pre-wrap;">{$msg}</pre>

    {if $backtrace}
        Backtrace:<br/>

        {foreach from=$backtrace item=trace name=trace}
            <div style="border-bottom: 1px solid silver; padding: 5px; margin-bottom: 5px;">
               <div>#{$trace.id} <span style="color: gray;">{$trace.file} +{$trace.line}</span></div>
               {if $trace.function}<div style="color: gray;">(triggers {$trace.function}({$trace.args_string}))</div>{/if}
               <div relline="{$trace.relLine}" style="white-space: pre; border: 1px solid gray;" class="cm-s-default" id="codemirror_{$smarty.foreach.trace.index}">{$trace.code|escape:"html"}</div>
            </div>
        {/foreach}
    {/if}
    </div>
</div>
<script type="text/javascript" src="core/js/bgNoise.js"></script>
{if $backtrace}
{literal}
    <script type="text/javascript">
        var id = 0;
        CodeMirror.modeURL = "lib/codemirror/mode/%N/%N.js"
        CodeMirror.requireMode('php', function() {
            while(true){
                var el = document.getElementById('codemirror_'+id);
                if (!el) break;

                var value = el.innerHTML.replace(/&gt;/g, '>').replace(/&lt;/g, '<');
                el.innerHTML = '';
                var relLine = parseInt(el.getAttribute('relline'));
                var editor = CodeMirror(el, {
                    lineNumbers: true,
                    readOnly: true,
                    firstLineNumber: relLine,
                    onHighlightComplete: function(pEditor){
                        console.log(pEditor);
                        pEditor.setLineClass(5, '', 'highlightedLine');
                    }
                });
                editor.setOption('mode', 'text/x-php');
                editor.setValue(value);
                id++;
            }
        });
    </script>
{/literal}
{/if}
</body>
</html>