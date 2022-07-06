<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

	require '../common/database.php';
	
	if(isset($_POST['type']) && isset($_POST['category_id'])){
		//edit
		
		$sql = "update products_cats set title = '". $_POST['category_name'] ."' where id = " . $_POST['category_id'];
		mysql_query($sql);
		
		echo $_POST['category_name'];
		die;
	}
	else if(isset($_POST['category_id'])){
			//delete case
			if($_POST['isParent']){
				$sql = "delete from products_cats where parent_id = " . $_POST['category_id'];
				mysql_query($sql);
			}
			
			$sql ="delete from products_cats where id = " . $_POST['category_id'];
			mysql_query($sql);
			echo 1;
			die;
	}
	else if(isset($_POST['newCategory'])){
		$sql = "insert into products_cats (rdate, valid,title, parent_id) values ('". date("Y-m-d H-i-s") ."',1,'". $_POST['newCategory'] ."',0)";
		
		mysql_query($sql);
		echo mysql_insert_id();
		die;
	}
	else{
			$categories =$_POST['categories'];
			
			$categories = json_decode($categories);
			
			
			foreach($categories as $k=>$category){
				$category  = (array) $category;
				$sql ="update products_cats set parent_id = ". $category['parentId']." where id= ". $category['id'];
				mysql_query($sql);
				
			}
			echo 1;
			die;
	}

echo 0;
die;

?>