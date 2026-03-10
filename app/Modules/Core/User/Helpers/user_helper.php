<?php
if (!function_exists('get__')) {
	function get__($id)
    {
		// $userModel = new \Auth\Models\UserModel();
		// $query = $userModel
		// 	->select('id, email, username, first_name, last_name, phone, avatar, cover, address, website, gender, birth_date')
		// 	->where('id', $id);

		// $data = $query->first();
		// if(!empty($data)){
		// 	$data->prefix_url = base_url('uploads/user/');
		// 	$data->bearer_token = "Bearer ".jwt_encode($data->id, $data->email);
		// } else {
		// 	$data = array();
		// }
		
		return true;
    }
}




