<?php
	class Logger {
		
		private
			$file,
			$timestamp;
			
		public function __construct($filename) {			
			$this->file = $filename;			
		}
		
		public function setTimestamp( $format) {
			$this->timestamp = "<strong>".date($format)." &raquo; </strong>";
		}
		
		public function putLog($insert) {
			/*if (isset($this->timestamp)) {
				file_put_contents($this->file, $this->timestamp.$insert."<br>", FILE_APPEND);
			} else {
				trigger_error("Timestamp not set", E_USER_ERROR);
			}*/
			file_put_contents($this->file, $insert, FILE_APPEND);
		}
		
		public function getLog() {
			if (isset($this->file)) {
				$content = @file_get_contents($this->file);
				return $content;
			}
		}
		
		public function clear() {
			@unlink($this->file);
		}
		
	}
?>