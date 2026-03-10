<?php 
if (!function_exists('count_indicator')) {
    function count_indicator()
    {
        $model = new \Reference\Models\ReferenceModel();
		return $model->where('menu_id', 93)->countAllResults();
    }
}

if (!function_exists('count_qna')) {
    function count_qna()
    {
        $model = new \Qna\Models\QnaModel();
		return $model->countAllResults();
    }
}
?>
