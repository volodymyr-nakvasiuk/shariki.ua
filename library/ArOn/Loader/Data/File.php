<?php
class ArOn_Loader_Data_File {
	
	protected $file = null;
	protected $binary;
	protected $name;
	protected $size;
	protected $fullpath;
	protected $realpath;
	protected $realname;
	protected $debug;
	protected $action_before_reading = false;
	
	/**
	 * Constructor of class
	 * @param string $filename The name of the file
	 * @param boolean $binarty Optional. If file is a binary file then set TRUE, otherwise FALSE
	 * @desc Constructor of class
	 */
	public function __construct($filename, $binary = false, $read = false, $rewrite = false) {
		clearstatcache();
		
		$this->name = $filename;
		$this->binary = $binary;
		
		if($read === true){
			if(!file_exists($filename)){
				$this->file = false;			
				$pathArray = explode( PATH_SEPARATOR, get_include_path() );
				foreach ($pathArray as $path){
					if($path == "." || $path == "..")
						continue;
					$len = strlen($path);
					$tmp_name = ($path[$len-1] == '/' || $path[$len-1] == '\\') ? $path : $path . '/';
					$tmp_name .= $filename;
					if(file_exists($tmp_name) && is_file($tmp_name)){
						$filename = $tmp_name;
						$this->file = true;
						break;
					}
				}
				if($this->file === false)	
					return;
			}
		}
		if ($binary) {
			$this->file = @fopen ( $filename, "a+b" );
			if (! $this->file) {
				$this->file = @fopen ( $filename, "rb" );
			}
		}elseif($read === true){
			$this->file = @fopen ( $filename, "r" );
		}elseif($rewrite === true){
			$this->file = @fopen ( $filename, "w" );
		} else {	
			$this->file = @fopen ( $filename, "a+" );
			if (! $this->file) {
				$this->file = @fopen ( $filename, "r" );
			}
		}
		$this->fullpath = realpath($filename);		
		$this->realpath = dirname($this->fullpath);
		$this->realname = basename($this->fullpath);
	}
	
	public function reopen($mode = false){
		clearstatcache();
		if($mode == 'read')
		$this->file = @fopen ( $this->fullpath, "r" );
		elseif($mode == 'rewrite')
		$this->file = @fopen ( $this->fullpath, "w" );
		elseif($mode == 'write')
		$this->file = @fopen ( $this->fullpath, "a+" );
		elseif($mode == 'binary')
		$this->file = @fopen ( $this->fullpath, "a+b" );
		else{
		$this->file = @fopen ( $this->fullpath, "a+" );	
		}
	}
	
	public function close(){
		if(empty($this->file))
			return false;	
		fclose ($this->file);
		$this->file = null;
	}
	
	public function isFile(){
		if($this->file === false)
			return false;
		return true;
	}
	
	public function addData($data,$f = true) {
		if(!is_array($data))
			$data = array($data);
		foreach ($data as $line){
			if($f) 
				$this->write ( $line . "\n" );
			else
				$this->write ( $line );
		}
		return true;
	}
	
	/**
	 * Returns the filesize in bytes
	 * @return int $filesize The filesize in bytes
	 * @desc Returns the filesize in bytes
	 */
	public function get_size() {
		return filesize ( $this->fullpath );
	}
	
	/**
	 * Returns the timestamp of the last change
	 * @return timestamp $timestamp The time of the last change as timestamp
	 * @desc Returns the timestamp of the last change
	 */
	public function get_time() {
		return fileatime ( $this->fullpath );
	}
	
	/**
	 * Returns the filename
	 * @return string $filename The filename
	 * @desc Returns the filename
	 */
	public function get_name() {
		return $this->name;
	}
	
	public function get_file_name() {
		return $this->realname;
	}
	
	public function get_path() {
		return $this->realpath;
	}
	
	/**
	 * Returns user id of the file
	 * @return string $user_id The user id of the file
	 * @desc Returns user id of the file
	 */
	public function get_owner_id() {
		return fileowner ( $this->fullpath );
	}
	
	/**
	 * Returns group id of the file
	 * @return string $group_id The group id of the file
	 * @desc Returns group id of the file
	 */
	public function get_group_id() {
		return filegroup ( $this->fullpath );
	}
	
	/**
	 * Returns the suffix of the file
	 * @return string $suffix The suffix of the file. If no suffix exists FALSE will be returned
	 * @desc Returns the suffix of the file
	 */
	public function get_suffix() {
		$file_array = explode ( "\.", $this->fullpath ); // Splitting prefix and suffix of real filename
		$suffix = $file_array [count ( $file_array ) - 1]; // Returning file type
		if (strlen ( $suffix ) > 0) {
			return $suffix;
		} else {
			return false;
		}
	}
	
	/**
	 * Sets the actual pointer position
	 * @return int $offset Returns the actual pointer position
	 * @desc Returns the actual pointer position
	 */
	public function pointer_set_start($offset) {
		$this->action_before_reading = true;
		return fseek ( $this->file, $offset , SEEK_SET);
	}
	
	public function pointer_set_cur($offset) {
		$this->action_before_reading = true;
		return fseek ( $this->file, $offset , SEEK_CUR);
	}
	
	public function pointer_set_end($offset) {
		$this->action_before_reading = true;
		return fseek ( $this->file, $offset , SEEK_END);
	}
	
	/**
	 * Returns the actual pointer position
	 * @param int $offset Returns the actual pointer position
	 * @desc Returns the actual pointer position
	 */
	public function pointer_get() {
		return ftell ( $this->file );
	}
	
	/**
	 * Reads a line from the file
	 * @return string $line A line from the file. If is EOF, false will be returned
	 * @desc Reads a line from the file
	 */
	public function read_line() {
		if(feof($this->file)){
			return false;
		}
		if ($this->action_before_reading) {
			if (rewind ( $this->file )) {
				$this->action_before_reading = false;
				if (($str = str_replace ( array ("\n", "\r" ), "", fgets ( $this->file ) )) > 0) {
				}
				return $str;
			} else {
				$this->halt ( "Pointer couldn't be reset" );
				return false;
			}
		} else {			
			if (($str = str_replace ( array ("\n", "\r" ), "", fgets ( $this->file ) )) > 0) {
			}
			return $str;			
		}
	}
	
	/**
	 * Reads data from a binary file
	 * @return string $line Data from a binary file
	 * @desc Reads data from a binary file
	 */
	public function read_bytes($bytes, $start_byte = 0) {
		if (is_int ( $start_byte )) {
			if (rewind ( $this->file )) {
				if ($start_byte > 0) {
					$this->pointer_set ( $start_byte );
					return fread ( $this->file, $bytes );
				} else {
					return fread ( $this->file, $bytes );
				}
			} else {
				$this->halt ( "Pointer couldn't be reset" );
				return false;
			}
		} else {
			$this->halt ( "Start byte have to be an integer" );
			return false;
		}
	}
	
	/**
	 * Writes data to the file
	 * @param string $data The data which have to be written
	 * @return boolean $written Returns TRUE if data could be written, FALSE if not
	 * @desc Writes data to the file
	 */
	public function write($data) {
		$this->action_before_reading = true;
		if (strlen ( $data ) > 0) {
			if ($this->binary) {
				$bytes = fwrite ( $this->file, $data );
				if (is_int ( $bytes )) {
					return $bytes;
				} else {
					$this->halt ( "Couldn't write data to file, please check permissions" );
					return false;
				}
			} else {
				$bytes = fputs ( $this->file, $data );
				if (is_int ( $bytes )) {
					return $bytes;
				} else {
					$this->halt ( "Couldn't write data to file, please check permissions" );
					return false;
				}
			}
		} else {
			$this->halt ( "Data must have at least one byte" );
		}
	}
	
	/**
	 * Copies a file to the given destination
	 * @param string $destination The new file destination
	 * @return boolean $copied Returns TRUE if file could bie copied, FALSE if not
	 * @desc Copies a file to the given destination
	 */
	public function copy($destination) {
		if (strlen ( $destination ) > 0) {
			if (copy ( $this->fullpath, $destination )) {
				return true;
			} else {
				$this->halt ( "Couldn't copy file to destination, please check permissions" );
				return false;
			}
		} else {
			$this->halt ( "Destination must have at least one char" );
		}
	}
	
	/**
	 * Searches a string in file
	 * @param string $string The string which have to be searched
	 * @return array $found_bytes Pointer offsets where string have been found. On no match, function returns false
	 * @desc Searches a string in file
	 */
	public function search($string) {
		if (strlen ( $string ) != 0) {
			
			$offsets = array ();
			
			$offset = $this->pointer_get ();
			rewind ( $this->file );
			
			// Getting all data from file
			$data = fread ( $this->file, $this->get_size () );
			
			// Replacing \r in windows new lines
			$data = preg_replace ( "[\r]", "", $data );
			
			$found = false;
			$k = 0;
			
			for($i = 0; $i < strlen ( $data ); $i ++) {
				
				$char = $data [$i];
				$search_char = $string [0];
				
				// If first char of string have been found and first char havn't been found
				if ($char == $search_char && $found == false) {
					$j = 0;
					$found = true;
					$found_now = true;
				}
				
				// If beginning of the string have been found and next char have been set
				if ($found == true && $found_now == false) {
					$j ++;
					// If next char have been found
					if ($data [$i] == $string [$j]) {
						// If complete string have been matched
						if (($j + 1) == strlen ( $string )) {
							$found_offset = $i - strlen ( $string ) + 2;
							$offsets [$k ++] = $found_offset;
						}
					} else {
						$found = false;
					}
				
				}
				
				$found_now = false;
			}
			
			$this->pointer_set ( $offset );
			
			return $offsets;
		} else {
			$this->halt ( "Search String have to be at least 1 chars" );
		}
	}
	
	/**
	 * Prints out a error message
	 * @param string $message all occurred errors as array
	 * @desc Returns all occurred errors
	 */
	public function halt($message) {
		if ($this->debug) {
			printf ( "File error: %s\n", $message );
			if ($this->error_nr != "" && $this->error != "") {
				printf ( "MySQL Error: %s (%s)\n", $this->error_nr, $this->error );
			}
			die ( "Session halted." );
		}
	}
	
	/**
	 * Switches to debug mode
	 * @param boolean $switch
	 * @desc Switches to debug mode
	 */
	public function debug_mode($debug = true) {
		$this->debug = $debug;
		if (! $this->file) {
			$this->halt ( "File couln't be opened, please check permissions" );
		}
	}
	
	static function saveToFile($file, $data) {
		
		if (file_exists ( $file )) {
			unlink ( $file );
		}
		
		if (! $handle = fopen ( $file, 'a' )) {
			return "Cannot open file ($file)";
		}
		
		// Write $somecontent to our opened file.
		if (fwrite ( $handle, $data ) === FALSE) {
			return "Cannot write to file ($file)";
		}
		
		fclose ( $handle );
		return true;
	}
	
/*	public function __destruct(){
		$this->close();
	}*/
}