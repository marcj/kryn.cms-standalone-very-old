<?php /* Smarty version Smarty-3.0.8, created on 2012-08-26 23:35:49
         compiled from "./module/th_krynDemo/view/content_default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:32370503a96b577b953-48627665%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5a20de3a9bd680df6788ee6f99668403a313fedb' => 
    array (
      0 => './module/th_krynDemo/view/content_default.tpl',
      1 => 1345990168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32370503a96b577b953-48627665',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="contentElement <?php echo $_smarty_tpl->getVariable('content')->value['type'];?>
">
    <?php if ($_smarty_tpl->getVariable('content')->value['title']){?>
        <h2><?php echo $_smarty_tpl->getVariable('content')->value['title'];?>
</h2>
    <?php }?>
    <div class="contentElementContent">
        <?php echo $_smarty_tpl->getVariable('content')->value['content'];?>

    </div>
</div>