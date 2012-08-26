<?php /* Smarty version Smarty-3.0.8, created on 2012-08-26 23:35:49
         compiled from "./module/th_krynDemo/view/navigation_breadcrumb.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12157503a96b5742566-12779968%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3622283ba95df96c4e78e0cb5853bda66b23f1fa' => 
    array (
      0 => './module/th_krynDemo/view/navigation_breadcrumb.tpl',
      1 => 1345990168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12157503a96b5742566-12779968',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<a href="<?php echo Core\Kryn::$domain->getUrl();?>
"><?php echo Core\Kryn::$domain->getDomain();?>
</a>

<?php  $_smarty_tpl->tpl_vars['crumb'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('breadcrumbs')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['crumb']->key => $_smarty_tpl->tpl_vars['crumb']->value){
?>
    » <a href="<?php echo $_smarty_tpl->getVariable('crumb')->value->getFullUrl();?>
"><?php echo $_smarty_tpl->getVariable('crumb')->value->getTitle();?>
</a>
<?php }} ?>
