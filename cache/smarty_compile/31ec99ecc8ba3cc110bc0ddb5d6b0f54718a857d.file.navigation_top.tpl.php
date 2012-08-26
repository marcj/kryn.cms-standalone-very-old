<?php /* Smarty version Smarty-3.0.8, created on 2012-08-26 23:35:49
         compiled from "./module/th_krynDemo/view/navigation_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6951503a96b5696ba6-81830097%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '31ec99ecc8ba3cc110bc0ddb5d6b0f54718a857d' => 
    array (
      0 => './module/th_krynDemo/view/navigation_top.tpl',
      1 => 1345990168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6951503a96b5696ba6-81830097',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

<?php  $_smarty_tpl->tpl_vars['children'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('navigation')->value->getLinks(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['children']->key => $_smarty_tpl->tpl_vars['children']->value){
?>
   <a class="<?php if ($_smarty_tpl->getVariable('children')->value->isActive()){?> active<?php }?>" href="<?php echo $_smarty_tpl->getVariable('children')->value->getFullUrl();?>
"><?php echo $_smarty_tpl->getVariable('children')->value->getTitle();?>
</a>
<?php }} ?>
