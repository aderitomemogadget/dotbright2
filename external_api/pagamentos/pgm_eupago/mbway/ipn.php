<?


	$json_post = json_encode($_POST);
	$json_get  = json_encode($_GET);

	file_put_contents('../../../../files/sibs_'.date('Y_m_d_h_i_s').'.txt', file_get_contents('php://input').' GET: '.$json_get.' POST: '.$json_post);


?>