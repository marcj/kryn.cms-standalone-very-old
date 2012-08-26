<?php /* Smarty version Smarty-3.0.8, created on 2012-08-26 23:35:49
         compiled from "./module/th_krynDemo/view/layout_default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10726503a96b5546287-22679926%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '59819705793b305cac58542453beec069e1a68ce' => 
    array (
      0 => './module/th_krynDemo/view/layout_default.tpl',
      1 => 1345990168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10726503a96b5546287-22679926',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_addCss')) include 'C:\Users\MArc\Documents\GitHub\Kryn.cms\lib\smarty\plugins\function.addCss.php';
if (!is_callable('smarty_function_navigation')) include 'C:\Users\MArc\Documents\GitHub\Kryn.cms\lib\smarty\plugins\function.navigation.php';
if (!is_callable('smarty_modifier_realUrl')) include 'C:\Users\MArc\Documents\GitHub\Kryn.cms\lib\smarty\plugins\modifier.realUrl.php';
if (!is_callable('smarty_function_slot')) include 'C:\Users\MArc\Documents\GitHub\Kryn.cms\lib\smarty\plugins\function.slot.php';
if (!is_callable('smarty_function_page')) include 'C:\Users\MArc\Documents\GitHub\Kryn.cms\lib\smarty\plugins\function.page.php';
?><?php echo smarty_function_addCss(array('file'=>"th_krynDemo/base.css"),$_smarty_tpl);?>


<div class="header">
    <div class="wrapper">
        <div class="header-top">
        <?php echo smarty_function_navigation(array('level'=>"1",'template'=>"th_krynDemo/navigation_top.tpl"),$_smarty_tpl);?>

        </div>
        
        <div class="header-logo">
            <a href="<?php echo $_smarty_tpl->getVariable('path')->value;?>
">
                <img src="" align="left" />
                <span class="header-logo-title"><?php echo $_smarty_tpl->getVariable('themeProperties')->value['title'];?>
</span><br />
                <span class="header-logo-slogan"><?php echo $_smarty_tpl->getVariable('themeProperties')->value['slogan'];?>
</span>
            </a>
        </div>
        
        <div class="header-subnavi">
            <?php echo smarty_function_navigation(array('level'=>"2",'template'=>"th_krynDemo/navigation_subnavi.tpl"),$_smarty_tpl);?>

        </div>
        
        <div class="header-search">
            <form action="<?php echo smarty_modifier_realUrl($_smarty_tpl->getVariable('themeProperties')->value['search_page']);?>
" method="get">
                <input type="text" name="q" value="[[Keyword ...]]" onfocus="if(this.value == '[[Keyword ...]]')this.value=''" onblur="if(this.value=='')this.value='[[Keyword ...]]'"/>
                <input type="submit" class="submit" value="<?php print tc("searchButton","Search"); ?>" />
                <input type="hidden" name="searchDo" value="1" />
            </form>
        </div>
    </div>
</div>

<div class="content">
    <div class="wrapper">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top">
                    <div class="content-main">
                        <div class="content-main-padding">
                            <?php echo smarty_function_navigation(array('id'=>"breadcrumb",'template'=>"th_krynDemo/navigation_breadcrumb.tpl"),$_smarty_tpl);?>


<pre>
</pre>

                            <?php echo smarty_function_slot(array('id'=>"1",'name'=>"[[Main content]]",'picturedimension'=>"640x1000"),$_smarty_tpl);?>

                        </div>
                    </div>
                </td>
                <td valign="top">
                    <?php if ($_smarty_tpl->getVariable('admin')->value){?>
                        <div class="content-sidebar">
                            <?php echo smarty_function_slot(array('id'=>"2",'name'=>"[[Sidebar]]",'assign'=>"sidebar"),$_smarty_tpl);?>

                        </div>
                    <?php }else{ ?>
                        <?php echo smarty_function_slot(array('id'=>"2",'name'=>"[[Sidebar]]",'assign'=>"sidebar"),$_smarty_tpl);?>

                        <?php if ($_smarty_tpl->getVariable('sidebar')->value!=''){?>
                            <div class="content-sidebar">
                                <?php echo $_smarty_tpl->getVariable('sidebar')->value;?>

                            </div>
                        <?php }?>
                    <?php }?>
                </td>
            </tr>
        </table>
    
    </div>
</div>


<div class="footer">
    <div class="wrapper">footer-box
        <div class="footer-box">
            <div class="footer-box-padding">
                <table width="100%">
                    <tr>
                        <td valign="top">
                            <?php if ($_smarty_tpl->getVariable('themeProperties')->value['footer_deposit']==''){?>
                                [[Please set "Footer deposit" under Domain » Theme » Kryn Demo]]
                            <?php }else{ ?>
                                <?php echo smarty_function_page(array('id'=>$_smarty_tpl->getVariable('themeProperties')->value['footer_deposit']),$_smarty_tpl);?>

                            <?php }?>
                        </td>
                        <td align="right" valign="top">
                            <?php echo smarty_function_navigation(array('id'=>$_smarty_tpl->getVariable('themeProperties')->value['footer_navi'],'template'=>"th_krynDemo/navigation_footer.tpl"),$_smarty_tpl);?>

                        </td>
                    </tr>
                </table>
                
            </div>
        </div>
    </div>
</div>