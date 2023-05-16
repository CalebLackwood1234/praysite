<?php
if($this->cleanHeaders) {
	header_remove();
}
http_response_code($this->code);
header('Content-Type: application/json');
header('Status: ' . $this->code);
if($this->arr != NULL) {
	echo json_encode($this->arr, JSON_UNESCAPED_UNICODE);
}
?>