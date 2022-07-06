<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */

switch ($act) {
	case "valid":
		$db=dbGet($id,'products_cats');
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit('products_cats',"valid='".$valid."'","id='".$db['id']."'");
		echo '<a title="'. lang('Active/Inactive Category') .'" class="activeCategory" onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		
	default:
			
		$pageTitle = 'Manage Product Categories';
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.$userLevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
			$set->content ="
			<link rel='stylesheet' href='".$set->SSLprefix."js/jquery-ui-1.12.1.custom-sortable/jquery-ui.css'/>
			<link rel='stylesheet' href='".$set->SSLprefix."js/jquery-ui-1.12.1.custom-sortable/jquery-ui.structure.min.css'/>
			<link rel='stylesheet' href='".$set->SSLprefix."js/jquery-ui-1.12.1.custom-sortable/jquery-ui.theme.min.css'/>
			<script src='".$set->SSLprefix."js/jquery-ui-1.12.1.custom-sortable/jquery-ui.min.js'></script>
			<script src='".$set->SSLprefix."js/jquery.mjs.nestedSortable.js'></script>
			<style>
			.sortable li div{
				width:300px;
				padding:10px;
				border:1px solid lightgrey;
				background-color:#eee;
				margin:5px;
				cursor:move;
			}
			div>a, div>span>a{
				position: relative;
				bottom: 4px;
				width: 22px;		
				float:right;
				cursor:pointer;
			}
		
			</style>
			";
			
			$result = getProductCategoriesList();
			
			
			foreach ($result as $cat):
					$listCats .= "<li><div data-id=". $cat['id'] ." data-parentId=". $cat['parent_id'] ." class='cat'>". $cat['title'] ." <a style='display:none;' class='editCategory' href='". $set->SSLprefix.$userLevel.'/product_categories.php?id='. $cat['id'] ."' title='". lang('Edit Category') ."'><img  src='".$set->SSLprefix."images/edit.png' style='width:16px'></a><a style='display:none;' class='deleteCategory' href='javascript:void(0)' title='". lang('Delete Category') ."'><img  src='".$set->SSLprefix."images/x2.png' style='width:16px'></a>";
					$listCats .= '<span id="lng_'.$cat['id'].'" class="activeCategory"><a title="'. lang('Active/Inactive Category') .'" class="activeCategory"  style="display:none" onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$cat['id'].'\',\'lng_'.$cat['id'].'\');" style="cursor: pointer;">'.xvPic($cat['valid']).'</a></span></div>';
					if(isset($cat['sub_categories'])):
						$sc = 0;
						foreach($cat['sub_categories'] as $sub_category):
						if($sc == 0)
						{
							$listCats .="<ol>";
						}
						$listCats .= "<li><div data-id=". $sub_category['id'] ." data-parentId=". $sub_category['parent_id'] ." class='cat'>". $sub_category['title'] ." <a style='display:none;' class='editCategory' href='". $set->SSLprefix .$userLevel.'/product_categories.php?id='. $sub_category['id'] ."' title='". lang('Edit Category') ."'><img  src='".$set->SSLprefix."images/edit.png' style='width:16px'></a> <a style='display:none;' class='deleteCategory' href='javascript:void(0)' title='". lang('Delete Category') ."'><img  src='".$set->SSLprefix."images/x2.png' style='width:16px'></a>";
						$listCats .= '<span id="lng_'.$sub_category['id'].'" class="activeCategory"  style="display:none;float:right;cursor:pointer;"><a title="'. lang('Active/Inactive Category') .'" class="activeCategory"  style="display:none" onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$sub_category['id'].'\',\'lng_'.$sub_category['id'].'\');" style="cursor: pointer;">'.xvPic($sub_category['valid']).'</a></span></div></li>';
						$sc++;
						endforeach;
						if($sc !=0){
							$listCats .="</ol>";
						}
					endif;
					$listCats .= "</li>";
			endforeach;
			
			if(isset($id)){
				$db = mysql_fetch_assoc(function_mysql_query("select * from products_cats where id=" . $id , __FILE__,__FUNCTION__));
			}
			
			
			$set->content .='<div>
			
			<table width="100%" border="0" cellpadding="0">
			'.(isset($id)?'
			<tr>
			<td>
				<input type="button" value="'. lang('Add New Category') .'" class="btnEdit" onclick="window.location = \''. $set->SSLprefix. $userLevel.'/product_categories.php' .'\'">
			</td>
			</tr>
			<tr>
			<td height="10px"></td>
			</tr>':'').'
			<tr>
			<td width="35%" valign="top">
					<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.(isset($id)?lang('Edit Category'):lang('Add New Category')).'</div>
					<table width="100%" border="0" cellpadding="0" bgcolor="#EFEFEF">
					<tr><td colspan="3" height="5"></td></tr>
					<tr><td align="left">'.lang('Category Name').':</td><td><input type="text" name="category" value="'. $db['title'] .'"></td></tr>
					<tr><td></td><td align="left">
					<input type ="hidden" name="id" value="'. (isset($id)?$id:'') .'">
					'.(isset($id)?'<input type="button" value="'. lang('Edit Category') .'" class="btnEdit">':'<input type="button" value="'. lang('Add Category') .'" class="btnAdd">').'</td></tr>
					<tr><td colspan="3" height="5"></td></tr>
					</table>
			</td>
			<td width="65%" valign="top">
				<table width="100%" border="0" cellpadding="0">
				<tr>
				<td>
					<div class="normalTableTitle" style="cursor: pointer;">'.lang('Category List').'</div>
					<!--input type="button" value="'. lang('Save') .'" name="saveCategories" style="float:right;margin:5px 0;"-->
					<div style="overflow:scroll;height:56vh;clear:both;">							
						<form method="post">
							<div  class="menu">
								<ol class="sortable">
									'. $listCats .'
								</ol>
							</div>
							
							<input type="button"  value="'. lang('Save') .'" name="saveCategories">
						</form>
						</div>
				</td>
				</tr>
				</table>
			</td>
			</tr>
			</table>
			';
			
			$set->content .="
			<script type='text/javascript'>
			$('.btnAdd').on('click',function(){
				category = $('[name=category]').val();
				$.post('".$set->SSLprefix."ajax/ManageProductCategories.php',{newCategory :category},function(res){
						/* txt = '<li><div data-id=\''+ res +'\' class=\'new\' data-parentid=0 onmouseover=\'showLinks(this)\' onmouseout=\'hideLinks(this)\'>'+ category + '  <a style=\'display:none;\' class=\'editCategory\' href=\'". $set->SSLprefix.$userLevel."/product_categories.php?id='+ res +'\'><img  src=\'".$set->SSLprefix."images/edit.png\' style=\'width:16px\'></a> <a class=\'deleteCategory\' href=\'javascript:void(0)\' onClick=\'deleteCategory(this)\' style=\'display:none;\'><img  src=\'".$set->SSLprefix."images/x.png\'></a>';
						txt +='</div></li>'
						$('.sortable').append(txt);
						$('[name=category]').val(''); */
						window.location.href = window.location.href;
				});
			});
			
			$('.btnEdit').on('click',function(){
				category = $('[name=category]').val();
				categoryId = $('[name=id]').val();
				$.post('ajax/ManageProductCategories.php',{type:'edit', category_name:category,category_id:categoryId},function(res){
						window.location.href= '". $set->SSLprefix.$userLevel."/product_categories.php';
				});
				
				
			});
			
			
			$('li').delegate('div','mouseover',function(){
				$(this).find('.deleteCategory').show();
				$(this).find('.editCategory').show();
				$(this).find('.activeCategory').show();
			}).delegate('div','mouseleave',function(){
				$(this).find('.deleteCategory').hide();
				$(this).find('.editCategory').hide();
				$(this).find('.activeCategory').hide();
			});
			
			function showLinks(e){
				$(e).find('a').show();
			}
			function hideLinks(e){
				$(e).find('a').hide();
			}
			
			try{
			$('li>div').delegate('a.deleteCategory','click',function(){
				deleteCategory($(this));
			});
			
			}
			catch(e){
			console.log(e);
			}
			function deleteCategory(e){
				category = $(e).parent('div').data('id');
				parent = $(e).parent('div').data('parentid');
				isParent = false;
				msg = '". lang('Are you sure you want to delete this subcategory?') ."';
				if(parent == 0){
					isParent = true;
							msg = '". lang('All the subcategories under this category will be deleted automatically if you delete this category. <br/> Are you sure you want to proceed?') ."';
				}
				
					$.prompt(msg, {
							top:200,
							title: '".lang('Delete Category')."',
							buttons: { 'Yes': true, 'Cancel': false },
							submit: function(e,v,m,f){
								if(v){
									
									$.post('".$set->SSLprefix."ajax/ManageProductCategories.php',{category_id :category, isParent : isParent, parent_id:parent},function(res){
										window.location.href = window.location.href;
									});
									
								}
								else{
									//
								}
							}
					});
			}
			$(document).ready(function(){
				$('.sortable').nestedSortable({
					handle: 'div',
					items: 'li',
					toleranceElement: '> div',
					maxLevels: 2,
				
					update:function(a,b){
						parent_id = b.item.parent().parent('li').find('div').data('id');
						if(parent_id != 'undefined' && parent_id>0)
						b.item.find('div').attr('data-parentid',parent_id);
						else
						b.item.find('div').attr('data-parentid',0);
						b.item.find('div').addClass('changed');
					}
				});
			});
			
			$('input[name=\'saveCategories\']').on('click',function(){
				var cats= {};
				new_categories = $('li div');
				
				new_categories.each(function(k,v){
					cats[k] = {};
					
			
					parent_id = $(this).data('parentid');
					cat_id = $(this).data('id');
					cats[k]['category_name'] = $(this).text();
					cats[k]['parentId'] =parent_id;
					if(typeof cat_id != 'undefined')
					cats[k]['id'] =cat_id;
					
				});
				
				
				if(cats!= ''){
					cats = JSON.stringify(cats);
					//ajax to insert data in product categories table
				
					$.post('".$set->SSLprefix."ajax/ManageProductCategories.php',{categories :cats},function(res){
						window.location.href = window.location.href;
					});
					
				}
				
			});
		
			</script>
			<script type='text/javascript' src='".$set->SSLprefix."js/impromptu/dist/jquery-impromptu.min.js'></script>
			<link rel='stylesheet' href='".$set->SSLprefix."js/impromptu/dist/jquery-impromptu.min.css'/>
			";
			theme();
break;
}
?>