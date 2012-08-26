<?php /* Smarty version Smarty-3.0.8, created on 2012-08-26 23:35:49
         compiled from "./module/th_krynDemo/view/navigation_subnavi.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8660503a96b56f2650-77839280%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '68ba9d23391995bddea29afba658bc3706bc8bf6' => 
    array (
      0 => './module/th_krynDemo/view/navigation_subnavi.tpl',
      1 => 1345990168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8660503a96b56f2650-77839280',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php  $_smarty_tpl->tpl_vars['children'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('navigation')->value->getLinks(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['children']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['children']->iteration=0;
if ($_smarty_tpl->tpl_vars['children']->total > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['children']->key => $_smarty_tpl->tpl_vars['children']->value){
 $_smarty_tpl->tpl_vars['children']->iteration++;
 $_smarty_tpl->tpl_vars['children']->last = $_smarty_tpl->tpl_vars['children']->iteration === $_smarty_tpl->tpl_vars['children']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["subnavi"]['last'] = $_smarty_tpl->tpl_vars['children']->last;
?>
<a class="<?php if ($_smarty_tpl->getVariable('children')->value->isActive()){?> active<?php }?>" href="<?php echo $_smarty_tpl->getVariable('children')->value->getFullUrl();?>
"><?php echo $_smarty_tpl->getVariable('children')->value->getTitle();?>
</a>
    <?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['subnavi']['last']){?>|<?php }?>
<?php }} ?>