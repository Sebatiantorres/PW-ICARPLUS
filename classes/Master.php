<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function save_service(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `service_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Service Name already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `service_list` set {$data} ";
		}else{
			$sql = "UPDATE `service_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$bid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Service successfully saved.";
			else
				$resp['msg'] = " Service successfully updated.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_service(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `service_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Service successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_mechanic(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `mechanic_list` where `firstname` = '{$firstname}' and `middlename` = '{$middlename}' and `lastname` = '{$lastname}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Mechanic already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `mechanic_list` set {$data} ";
		}else{
			$sql = "UPDATE `mechanic_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$bid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Mechanic successfully saved.";
			else
				$resp['msg'] = " Mechanic successfully updated.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_mechanic(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `mechanic_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Mechanic successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_product(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `product_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product Name already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `product_list` set {$data} ";
		}else{
			$sql = "UPDATE `product_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$pid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Product successfully saved.";
			else
				$resp['msg'] = " Product successfully updated.";
			if(!empty($_FILES['img']['tmp_name'])){
				$dir = 'uploads/products/';
				if(!is_dir(base_app.$dir))
				mkdir(base_app.$dir);
				$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
				$fname = $dir.$pid.".png";
				$accept = array('image/jpeg','image/png');
				if(!in_array($_FILES['img']['type'],$accept)){
					$resp['msg'] .= "Image file type is invalid";
				}
				if($_FILES['img']['type'] == 'image/jpeg')
					$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
				elseif($_FILES['img']['type'] == 'image/png')
					$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
				if(!$uploadfile){
					$resp['msg'] .= "Image is invalid";
				}
				list($width, $height) = getimagesize($_FILES['img']['tmp_name']);
				if($width > 640 || $height > 480){
					if($width > $height){
						$perc = ($width - 640) / $width;
						$width = 640;
						$height = $height - ($height * $perc);
					}else{
						$perc = ($height - 480) / $height;
						$height = 480;
						$width = $width - ($width * $perc);
					}
				}
				$temp = imagescale($uploadfile,$width,$height);
				if(is_file(base_app.$fname))
				unlink(base_app.$fname);
				$upload =imagepng($temp,base_app.$fname,6);
				if($upload){
					$this->conn->query("UPDATE `product_list` set image_path = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$pid}' ");
				}
				imagedestroy($temp);
			}
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function save_inventory(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `inventory_list` set {$data} ";
		}else{
			$sql = "UPDATE `inventory_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$bid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Stock has been saved successfully.";
			else
				$resp['msg'] = " Stock has been updated successfully.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_inventory(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `inventory_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Stock has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_product(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `product_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Product successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_transaction(){
		if(empty($_POST['id'])){
			$_POST['user_id'] = $this->settings->userdata('id');
			$prefix = date("Ymd");
			$code = sprintf("%'.04d", 1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `transaction_list` where code = '{$prefix}{$code}' ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.04d", abs($code) + 1);
				}else{
					$_POST['code'] = $prefix.$code;
					break;
				}
			}
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `transaction_list` set {$data} ";
		}else{
			$sql = "UPDATE `transaction_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$tid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['tid'] = $tid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Transaction successfully saved.";
			else
				$resp['msg'] = " Transaction successfully updated.";
			if(isset($service_id)){
				$data = "";
				foreach($service_id as $k =>$v){
					$sid = $v;
					$price = $this->conn->real_escape_string($service_price[$k]);
					if(!empty($data)) $data .= ", ";
					$data .= "('{$tid}', '{$sid}', '{$price}')";
				}
				if(!empty($data)){
					$this->conn->query("DELETE FROM `transaction_services` where transaction_id = '{$tid}'");
					$sql_service = "INSERT INTO `transaction_services` (`transaction_id`, `service_id`, `price`) VALUES {$data}";
					$save_services = $this->conn->query($sql_service);
					if(!$save_services){
						$resp['status'] = 'failed';
						$resp['sql'] = $sql_service;
						$resp['error'] = $this->conn->error;
						if(empty($id)){
							$resp['msg'] = "Transaction has failed save.";
							$this->conn->query("DELETE FROM `transaction_services` where transaction_id = '{$tid}'");
						}else{
							$resp['msg'] = "Transaction has failed update.";
						}
						return json_encode($resp);
					}
				}
			}
			if(isset($product_id)){
				$data = "";
				foreach($product_id as $k =>$v){
					$pid = $v;
					$price = $this->conn->real_escape_string($product_price[$k]);
					$qty = $this->conn->real_escape_string($product_qty[$k]);
					if(!empty($data)) $data .= ", ";
					$data .= "('{$tid}', '{$pid}', '{$qty}', '{$price}')";
				}
				if(!empty($data)){
					$this->conn->query("DELETE FROM `transaction_products` where transaction_id = '{$tid}'");
					$sql_product = "INSERT INTO `transaction_products` (`transaction_id`, `product_id`,`qty`, `price`) VALUES {$data}";
					$save_products = $this->conn->query($sql_product);
					if(!$save_products){
						$resp['status'] = 'failed';
						$resp['sql'] = $sql_product;
						$resp['error'] = $this->conn->error;
						if(empty($id)){
							$resp['msg'] = "Transaction has failed save.";
							$this->conn->query("DELETE FROM `transaction_products` where transaction_id = '{$tid}'");
						}else{
							$resp['msg'] = "Transaction has failed update.";
						}
						return json_encode($resp);
					}
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_transaction(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `transaction_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Transaction successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function update_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `transaction_list` set `status` = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Transaction's status has failed to update.";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', 'Transaction\'s Status has been updated successfully.');
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
	break;
	case 'save_service':
		echo $Master->save_service();
	break;
	case 'delete_service':
		echo $Master->delete_service();
	break;
	case 'save_mechanic':
		echo $Master->save_mechanic();
	break;
	case 'delete_mechanic':
		echo $Master->delete_mechanic();
	break;
	case 'save_product':
		echo $Master->save_product();
	break;
	case 'delete_product':
		echo $Master->delete_product();
	break;
	case 'save_inventory':
		echo $Master->save_inventory();
	break;
	case 'delete_inventory':
		echo $Master->delete_inventory();
	break;
	case 'save_transaction':
		echo $Master->save_transaction();
	break;
	case 'delete_transaction':
		echo $Master->delete_transaction();
	break;
	case 'update_status':
		echo $Master->update_status();
	break;
	default:
		// echo $sysset->index();
		break;
}