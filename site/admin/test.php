<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */
require_once('common/global.php');

if (!isAdmin()) _goto('/admin/');
switch ($act) {
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
				<li><a href="admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
			$set->content ="
			<link rel='stylesheet' href='../js/jquery-ui-1.12.1.custom-sortable/jquery-ui.css'/>
			<link rel='stylesheet' href='../js/jquery-ui-1.12.1.custom-sortable/jquery-ui.structure.min.css'/>
			<link rel='stylesheet' href='../js/jquery-ui-1.12.1.custom-sortable/jquery-ui.theme.min.css'/>
			<script src='../js/jquery-ui-1.12.1.custom-sortable/jquery-ui.min.js'></script>
			<script src='../js/jquery.mjs.nestedSortable.js'></script>
			<style>
			.sortable li div{
				width:300px;
				padding:10px;
				border:1px solid lightgrey;
				background-color:#eee;
				margin:5px;
				cursor:move;
			}
			div>a{
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
					$listCats .= "<li><div data-id=". $cat['id'] ." data-parentId=". $cat['parent_id'] .">". $cat['title'] ."<a href='javascript:void(0)'><img  src='../images/x.png'></a></div>";
					if(isset($cat['sub_categories'])):
						$sc = 0;
						foreach($cat['sub_categories'] as $sub_category):
						if($sc == 0)
						{
							$listCats .="<ol>";
						}
						$listCats .= "<li><div data-id=". $sub_category['id'] ." data-parentId=". $sub_category['parent_id'] .">". $sub_category['title'] ." <a href='javascript:void(0)'><img  src='../images/x.png'></a></div></li>";
						$sc++;
						endforeach;
						if($sc !=0){
							$listCats .="</ol>";
						}
					endif;
					$listCats .= "</li>";
			endforeach;
			
			
			
			$set->content .='<div>
			
			<table width="100%" border="0" cellpadding="0">
			<tr>
			<td width="50%" valign="top">
					<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Category').'</div>
					<table width="100%" border="0" cellpadding="0" bgcolor="#EFEFEF">
					<tr><td colspan="3" height="5"></td></tr>
					<tr><td align="left">'.lang('Category Name').':</td><td><input type="text" name="category"></td></tr>
					<tr><td></td><td align="left"><input type="button" value="'. lang('Add Category') .'" class="btnAdd"></td></tr>
					<tr><td colspan="3" height="5"></td></tr>
					</table>
			</td>
			<td width="50%" valign="top">
				<table width="100%" border="0" cellpadding="0">
				<tr>
				<td>
					<div class="normalTableTitle" style="cursor: pointer;">'.lang('Category List').'</div>
					<input type="button" value="'. lang('Save') .'" name="saveCategories" style="float:right;margin:5px 0;">
					<div style="overflow:scroll;height:56vh;clear:both;">							
						<form method="post">
							<div  class="menu">
								<ol class="sortable">
									'. $listCats .'
								</ol>
							</div>
							<input type="button" value="'. lang('Save') .'" name="saveCategories">
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
				$.post('ajax/ManageProductCategories.php',{newCategory :category},function(res){
						txt = '<li><div data-id=\''+ res +'\' class=\'new\' data-parentid=0>'+ category + ' <a href=\'javascript:void(0)\' onClick=\'deleteCategory(this)\'><img  src=\'../images/x.png\'></a></div></li>'
						$('.sortable').append(txt);
						$('[name=category]').val('');
				});
				
				
			});
			
			try{
			$('li>div').delegate('a','click',function(){
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
									
									$.post('ajax/ManageProductCategories.php',{category_id :category, isParent : isParent, parent_id:parent},function(res){
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
				
					$.post('ajax/ManageProductCategories.php',{categories :cats},function(res){
						window.location.href = window.location.href;
					});
					
				}
				
			});
		
			</script>
			<script type='text/javascript' src='../js/impromptu/dist/jquery-impromptu.min.js'></script>
			<link rel='stylesheet' href='../js/impromptu/dist/jquery-impromptu.min.css'/>
			";
			theme();
break;
}
?>