<?php 

/**
 * 
 */
class Filemanipulator {

	// get from http://php.net

	private $upload_dir = '';
	private $mimetypes_allowed = array();
	private $fuploaded = array();
	private $max_file_size = 10;

	public function set_upload_folder(string $filedir): void {

		$this->upload_dir = $filedir;
	}

	public function set_dir_name(string $dir): void {

		$this->pdir = $dir;
	}

	private $pdir; 

	public function create_dir(): void {

		if (!is_dir($this->pdir)) {

			mkdir($this->pdir, 0755,true);
			chmod($this->pdir, 0755);
		}
		unset($this->pdir);
	}

	public function set_allowed_mimetypes(array $mtypes= array()): void {

		$this->mimetype_list($mtypes);  
	}

	private function get_filesize(int $filesize): string {

		if(is_numeric($filesize)) {

	    	$decr = 1024; 
	    	$step = 0;
	    	$prefix = array('Byte','KB','MB','GB','TB','PB');

		    while(($filesize / $decr) > 0.9)
		    {
		        $filesize = $filesize / $decr;
		        $step++;
		    } 
	    	return round($filesize,2).' '.$prefix[$step];
	    } else {
	    	return 'NaN';
	    }
	}

	protected function upload_file(): void {

		$myFile = $_FILES[$fileName];
		$folder = $this->upload_dir;

		// Проверка папки, если не существует создаем! Проверяем файл на безопасность

    	$myFile['name'] = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile['name']);

    	try {

			if (count($this->mimetypes_allowed) < 1) 
			{
				throw new RuntimeException('Critical, no allowed mimetypes!');
			}
		    // Undefined | Multiple Files | $_FILES Corruption Attack
		    // If this request falls under any of them, treat it invalid.
		    if (!isset($myFile['error']) || is_array($myFile['error'])) 
		    {
		        throw new RuntimeException('Invalid parameters');
		    } 

		    // Check $_FILES['upfile']['error'] value.
		    switch ($myFile['error']) 
		    {
		        case UPLOAD_ERR_OK:
		            break;
		        case UPLOAD_ERR_NO_FILE:
		            throw new RuntimeException('No file sent');
		        case UPLOAD_ERR_INI_SIZE:
		        case UPLOAD_ERR_FORM_SIZE:
		            throw new RuntimeException('Exceeded filesize limit');
		        default:
		            throw new RuntimeException('Unknown errors');
		    }

		    // You should also check filesize here. 
		    if ($myFile['size'] > $this->max_file_size) 
		    {
		        throw new RuntimeException('Exceeded filesize limit');
		    }

		    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
		    // Check MIME Type by yourself.
		    $finfo = new finfo(FILEINFO_MIME_TYPE);
		    $mimetype =  $finfo->file($myFile['tmp_name']);
		    $ext = array_search($mimetype, $this->mimetypes_allowed, true);

		    if ($ext === false) {
		        throw new RuntimeException('Invalid file format');
		    }

		    $i = 0;
		    $parts = pathinfo($myFile['name']);

		    while (file_exists($folder . $myFile['name'])) 
		    {
		    	$i++;
		    	$myFile['name'] = $parts["filename"] . "-" . $i . "." . $parts["extension"];
		    }
		    // You should name it uniquely.
		    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
		    // On this example, obtain safe unique name from its binary data.
		    if (!move_uploaded_file($myFile['tmp_name'], $this->working_folder . $myFile['name'])) 
		    {
		        throw new RuntimeException('Failed to move uploaded file');
		    } 
		    else 
		    {
		    	chmod($folder . $myFile['name'], 0644);
		    }
		    echo 'File is uploaded successfully';
		} catch (RuntimeException $e) {
		    echo $e->getMessage();
		}
		$this->fuploaded['file_name'] = $myFile['name'];
		$this->fuploaded['file_size'] = $this->display_filesize($myFile['size']);
		$this->fuploaded['file_type'] = $mimetype;
	}

	private function get_mimetype(): string {

		$ext = strtolower(array_pop(explode('.',$filename)));

        if (array_key_exists($ext, $mime_types)) 
        {
            return $mime_types[$ext];
        } 
        elseif (function_exists('finfo_open')) 
        {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            $r = $mimetype;
        } else {
            $r = 'application/octet-stream';
        }
        
        return $r;
	}

	private function mimetype_list(array $allowedmim = array()): void {

		$r_mime = array();

		$mime_types = array(
			// txt 
            'txt' 	=> 'text/plain',
            'htm' 	=> 'text/html',
            'html' 	=> 'text/html',
            'php' 	=> 'text/html',
            'css' 	=> 'text/css',
            'js' 	=> 'application/javascript',
            'json' 	=> 'application/json',
            'xml' 	=> 'application/xml',
            'swf' 	=> 'application/x-shockwave-flash',
            'flv' 	=> 'video/x-flv',
            // images
            'png' 	=> 'image/png',
            'jpe' 	=> 'image/jpeg',
            'jpeg' 	=> 'image/jpeg',
            'jpg' 	=> 'image/jpeg',
            'gif' 	=> 'image/gif',
            'bmp' 	=> 'image/bmp',
            'ico' 	=> 'image/vnd.microsoft.icon',
            'tiff' 	=> 'image/tiff',
            'tif' 	=> 'image/tiff',
            'svg' 	=> 'image/svg+xml',
            'svgz' 	=> 'image/svg+xml',
            // archives
            'zip' 	=> 'application/zip',
            'rar' 	=> 'application/x-rar-compressed',
            'exe' 	=> 'application/x-msdownload',
            'msi' 	=> 'application/x-msdownload',
            'cab'	=> 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' 	=> 'audio/mpeg',
            'qt' 	=> 'video/quicktime',
            'mov' 	=> 'video/quicktime',
            // adobe
            'pdf' 	=> 'application/pdf',
            'psd' 	=> 'image/vnd.adobe.photoshop',
            'ai' 	=> 'application/postscript',
            'eps' 	=> 'application/postscript',
            'ps' 	=> 'application/postscript',
            // ms office
            'doc' 	=> 'application/msword',
            'rtf' 	=> 'application/rtf',
            'xls' 	=> 'application/vnd.ms-excel',
            'ppt' 	=> 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

		// filter all unnessary mimetypes, keep that allowed 
		foreach ($allowedmim as $kma => $vma) {
			foreach ($mime_types as $kmt => $vmt) {
				if ($vma == $kmt) {
					$r_mime[$kmt] = $vmt;
				}
			}
		}
        $this->mimetypes_allowed = $r_mime;
	}

	private function list_folder(bool $recursion=false): array  {

		$dir = $work_dir;
		$result = array(); 
	   	$cdir = scandir($dir); 

	   	foreach ($cdir as $key => $value) {
	   		
			if (!in_array($value,array(".",".."))) {

				if (is_dir($dir . DIRECTORY_SEPARATOR . $value) && $recursive) {

					$r = $this->list_folder($dir . DIRECTORY_SEPARATOR . $value, $recursive);
					$result[$value] = $r;
				} else 
				    $result[] = $value; 
			}
	   } 
	   return $result; 
	}
}