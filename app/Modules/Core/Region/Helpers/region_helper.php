<?php 
if (!function_exists('get_region')) {
	function get_region($sub_district_id)
	{
		$codes = explode('.', $sub_district_id);
		$province_id = $codes[0];
		$city_id = $province_id.'.'.$codes[1];
		$district_id = $city_id.'.'.$codes[2];

		$regionModel = new \Region\Models\RegionModel();
		$province = $regionModel->where('code',$province_id)->first();
		$city = $regionModel->where('code',$city_id)->first();
		$district = $regionModel->where('code',$district_id)->first();
		$sub_district = $regionModel->where('code',$sub_district_id)->first();

		$data = array(
			'province' => $province->name,
			'province_id' => $province_id,
			'city' => $city->name,
			'city_id' => $city_id,
			'district' => $district->name,
			'district_id' => $district_id,
			'sub_district' => $sub_district->name,
			'sub_district_id' => $sub_district_id,
		);

		return (object) $data;
	}
}
?>