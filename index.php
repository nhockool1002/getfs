<?php 
function get_csrf($page) {
	preg_match('#csrf-token" content="(.*?)">#', $page, $data);
	$csrf = $data[1];
	return $csrf;
}

function get_id($link) {
	preg_match('/https\:\/\/www\.fshare\.vn\/file\/(.[a-zA-Z0-9]+)/', $link, $data);
	$id = $data[1];
	return $id;
}

function login_fs($csrf) {
	$username = 'kangcourse@gmail.com';
	$password = '0909274128';
	$curl = new cURL();
	$login_url = "https://www.fshare.vn/site/login";
	$data = "_csrf-app=" . urlencode($csrf) . "&LoginForm%5Bemail%5D=" . urlencode($username) . "LoginForm%5Bpassword%5D=" . urlencode($password) . "LoginForm%5BrememberMe%5D=0";
	$curl->post($login_url, $data);
}

function get_link($csrf, $link) {
	$curl = new cURL();
	$id = get_id($link);
	$data = "_csrf-app=" . urlencode($csrf) . "&linkcode=" . $id . "&withFcode5=0&fcode5=";
	$getdata_url = 'https://www.fshare.vn/download/get';
	$getLink = $curl->post($getdata_url, $data);
	if(strpos($getLink, '{"url"')) {
		$link = substr($getLink, strpos($getLink, '{'));
		$link = json_decode($link, true);
		$link = $link['url'];
		return $link;
	} else {
		echo "Không get được link";
		return false;
	}
}

function create_short($link) {
	$url = file_get_contents('http://tinyurl.com/api-create.php?url='.$link);
	return $url;
}


if (isset($_GET['link'])) {
	$link = $_GET['link'];
	require_once 'curl.php';

	preg_match('/https\:\/\/www\.fshare\.vn\/file\/[a-zA-Z0-9]+/', $link, $data);
	unset($link);
	$link = $data[0];
	$curl = new cURL();
	$page = $curl->get($link);
	$csrf = get_csrf($page);
	login_fs($csrf);
	$download = get_link($csrf, $link);
	$final = create_short($download);
	echo "<a href='" . $final . "' target='_blank'>" . $final ."</a>";
} else echo "Không có đường dẫn !";
?>