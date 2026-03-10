<?php 
if (!function_exists('count_surveyor')) {
    function count_surveyor()
    {
        $model = new \Survey\Models\SurveyModel();
		return $model->countAllResults();
    }
}
?>
