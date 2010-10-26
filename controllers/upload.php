<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Upload Controller
 *
 * @author	Bryce Johnston bryce@wingdspur.com
 *
 * Functions for file uploading with uploadify tasks moved into a controller
 * instead of using uploadify.php, its recommended you do some type of check
 * to prevent certain files, or prevent people from uploading whatever they want, this 
 * is just an example of using uploadify with CI and accepting a few other 
 * form inputs. If you are trying to restrict access based on session, note that
 * the uploadify.swf opens a separate session, you you would have to do further
 * modification for session based authentication checks by passing session id or
 * something along those lines.
 */

class Upload extends Controller 
{

    function __construct()
    {
        parent::__construct();
        //$this->load->model('file_model');
    }
	
	function index()
	{
		$this->load->view('upload_form');
	}

    function do_upload()
    {
        $upload_path = 'files/uploads/';  
        $group = $this->input->post('group');
		
        if(!empty($_FILES))
        {
			$name = $this->input->post('name', TRUE);
            $description = $this->input->post('description', TRUE);
            $path = FCPATH . $upload_path;
            $file_temp = $_FILES['Filedata']['tmp_name'];
            $file_name = $this->_prep_filename($_FILES['Filedata']['name']);
            $file_ext = $this->_get_extension($_FILES['Filedata']['name']);
            $real_name = $file_name;
            $newf_name = $this->_set_filename($path, $file_name, $file_ext);
            $file_size = round($_FILES['Filedata']['size']/1024, 2);
            $file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES['Filedata']['type']);
            $file_type = strtolower($file_type);
            $targetFile = str_replace('//', '/', $path) . $newf_name;
            move_uploaded_file($file_temp, $targetFile);
                
            if(empty($name)) { $name = newf_name; }       
            
			/***
			# You could easily insert the details in the db if you wanted
            $filearray = array(
				'title' => $name,
                'filename' => $newf_name,
                'filepath' => $upload_path . $newf_name,
                'description' => $description,
                'date' => date("Y-m-d H:i:s"),
                'status' => 1
            );
                    
            $this->file_model->add_upload($filearray);
			***/
                
            echo json_encode('<p class="success"><strong>Success!</strong> The file was uploaded.</p>');
		}
		else
		{
			echo json_encode('<p class="errors"><strong>Error:</strong> File was not uploaded!</p>');
		}
    }

    function uploadify()
    {
        $msg = $this->input->post('message');
        echo json_decode($msg);
    }

    function _set_filename($path, $filename, $file_ext, $encrypt_name = FALSE)
    {
        if($encrypt_name == TRUE)
        {
            mt_srand();
            $filename = md5(uniqid(mt_rand())).$file_ext;
        }
    
        if(!file_exists($path.$filename))
        {
            return $filename;
        }
    
        $filename = str_replace($file_ext, '', $filename);
    
        $new_filename = '';
        for ($i = 1; $i < 100; $i++)
        {
            if(!file_exists($path.$filename.$i.$file_ext))
            {
                $new_filename = $filename.$i.$file_ext;
                break;
            }
        }
    
        if($new_filename == '')
        {
            return FALSE;
        }
        else
        {
            return $new_filename;
        }
    }
      
    function _prep_filename($filename)
    {
       if (strpos($filename, '.') === FALSE)
       {
            return $filename;
       }
       $parts = explode('.', $filename);
       $ext = array_pop($parts);
       $filename    = array_shift($parts);
       foreach ($parts as $part)
       {
            $filename .= '.'.$part;
       }
       $filename .= '.'.$ext;
       return $filename;
    }
      
    function _get_extension($filename)
    {
       $x = explode('.', $filename);
       return '.'.end($x);
    } 

}

/* End of file upload.php */
/* Location: ./application/controllers/upload.php */