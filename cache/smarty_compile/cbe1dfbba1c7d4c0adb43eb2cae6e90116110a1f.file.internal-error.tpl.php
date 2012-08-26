<?php /* Smarty version Smarty-3.0.8, created on 2012-08-26 12:59:47
         compiled from "./core/view/internal-error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5843503a1dc3cfaa75-71629967%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cbe1dfbba1c7d4c0adb43eb2cae6e90116110a1f' => 
    array (
      0 => './core/view/internal-error.tpl',
      1 => 1345911220,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5843503a1dc3cfaa75-71629967',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include 'C:\Users\MArc\Documents\GitHub\Kryn.cms\lib\smarty\plugins\modifier.escape.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>[[404 - Not found]]</title>
    <base href="<?php echo Core\Kryn::getBaseUrl();?>
" />

    <?php if ($_smarty_tpl->getVariable('backtrace')->value){?>
        <script type="text/javascript" src="lib/codemirror/lib/codemirror.js"></script>
        <script type="text/javascript" src="lib/codemirror/lib/util/loadmode.js"></script>
        <script type="text/javascript" src="lib/codemirror/lib/util/runmode.js"></script>
        <link rel="stylesheet" type="text/css" href="lib/codemirror/lib/codemirror.css"  />
    <?php }?>

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
    <img src="media/core/images/logo_white.png" />
    <h2><?php echo $_smarty_tpl->getVariable('title')->value;?>
</h2>
    <div class="msg"><pre><?php echo $_smarty_tpl->getVariable('msg')->value;?>
</pre>

    <?php if ($_smarty_tpl->getVariable('backtrace')->value){?>
        Backtrace:<br/>

        <?php  $_smarty_tpl->tpl_vars['trace'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('backtrace')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['trace']['index']=-1;
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['trace']->key => $_smarty_tpl->tpl_vars['trace']->value){
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['trace']['index']++;
?>
            <div style="border-bottom: 1px solid silver; padding: 5px; margin-bottom: 5px;">
               <div>#<?php echo $_smarty_tpl->tpl_vars['trace']->value['id'];?>
 <span style="color: gray;"><?php echo $_smarty_tpl->tpl_vars['trace']->value['file'];?>
+<?php echo $_smarty_tpl->tpl_vars['trace']->value['line'];?>
</span></div>
               <?php if ($_smarty_tpl->tpl_vars['trace']->value['function']){?><div style="color: gray;">(triggers <?php echo $_smarty_tpl->tpl_vars['trace']->value['function'];?>
(<?php echo $_smarty_tpl->tpl_vars['trace']->value['args_string'];?>
))</div><?php }?>
               <div relline="<?php echo $_smarty_tpl->tpl_vars['trace']->value['relLine'];?>
" style="white-space: pre; border: 1px solid gray;" class="cm-s-default" id="codemirror_<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['trace']['index'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trace']->value['code'],"html");?>
</div>
            </div>
        <?php }} ?>
    <?php }?>
    </div>
</div>
<script type="text/javascript" src="media/core/js/bgNoise.js"></script>
<?php if ($_smarty_tpl->getVariable('backtrace')->value){?>

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

<?php }?>
</body>
</html>