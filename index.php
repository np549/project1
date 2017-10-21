<?php
//turn on debugging messages 	no changes required this is for debugging
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//instantiate the program object

//Class to load classes it finds the file when the progrm starts to fail for calling a missing class
class Manage {
    public static function autoload($class) {
        //you can put any file name or directory here
       // include $class . '.php';
    }
}

spl_autoload_register(array('Manage', 'autoload'));

//instantiate the program object   no changes required--to instantiate main

$obj = new main();


class main {

    public function __construct()
    {
        //set default page request when no parameters are in URL
         $pageRequest = 'Uploadform';
        //check if there are parameters
        if(isset($_REQUEST['page'])) {
            //load the type of page the request wants into page request
            $pageRequest = $_REQUEST['page'];
        }
        //instantiate the class that is being requested
         $page = new $pageRequest;

if(isset($_REQUEST['filename'])) {
					  $htmlTable = new HtmlTable;
					  $htmlTable->show_html_table();die;
				 }
				
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
           echo $page->get();
        } else {
            $post_response=$page->post();
			if($post_response=="true")
			{
				echo  "Successfully Uploaded CSV File"; // show success msg.
			}
			else
			{
				echo "Please Select CSV File";
			}
        }

    }

}

abstract class page {
    protected $html;
    public function __construct()
    {
        $this->html .= '<html>';
		$this->html .= '<style>table td,th{border: 1px solid black;}</style>';
        $this->html .= '<body>';
    }
    public function __destruct()
    {
        $this->html .= '</body></html>';
        stringFunctions::printThis($this->html); 
    }

}


class Uploadform extends page
{
	public function get()
			{
				$form = '<form action="index.php" method="post"
			    enctype="multipart/form-data">';
				$form .= '<input type="file" name="fileToUpload" id="fileToUpload">';
				$form .= '<input type="submit" value="Upload" name="submit">';
				$form .= '</form> ';
                $this->html .= $form;
			}

			public function post() 
			{
				$get_file_name=pathinfo($_FILES['fileToUpload']['name']); // get all  info about upload file with extension like csv,xml etc.
			  $extension=trim($get_file_name['extension']);// remove space if attached with csv extension like // csv , xml 
			if($extension=='CSV' || $extension=='csv') // check condition for file is csv or not
	         {   
				 $get_file_name=pathinfo($_FILES['fileToUpload']['name']); // get all  info about upload file with extension like csv,xml etc.
			  $extension=trim($get_file_name['extension']);// remove space if attached with csv extension like // csv , xml 
			  $filename=trim($get_file_name['filename']);// remove space if attached with csv file's name
			  $upload_directory_path_org="upload/";  // path for the csv folder where user want to save uploaded file
			 $upload_directory_path_trimed=trim($upload_directory_path_org,"/");// remove "/" if already in given path 
			$upload_directory_path=	$upload_directory_path_trimed."/";   //  set "/" after path 	
			$combined_filename=trim($filename."_".time());// attach file name with unique id
			$unique_filename=$combined_filename.".".$extension;// attach file name with unique id
			if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$upload_directory_path.$unique_filename))
			{
				    chmod($upload_directory_path, 0777); // fix permission of given directory 
					
			}
			$current_url=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			header("Location:http://".$current_url."?page=HtmlTable&filename=".$unique_filename);
			 }
				else{
					 return "false";
				}
			
			}
}

class stringFunctions {
      static public function printThis($string)
	  {
          print ($string);
         
      }
					}
					
					
class HtmlTable extends page 
{
	function show_html_table()
	{
	$unique_filename=$_REQUEST['filename'];
	$upload_directory_path_org='upload/';
	$files = fopen($upload_directory_path_org.$unique_filename,"r"); // open the uploaded directory
			$this->html="<table>";
			$count_array=0;
			while (!feof($files) ) // loop running till file's end
						{
							$line_of_text = fgetcsv($files, 99999999); // get file content with max size
							$item_fetch = trim($line_of_text[0]);// get first row's content 
							if($item_fetch!="")
							{
											if($count_array==0)
											{
												$item_fetch_header_only =$line_of_text;// get header info 
												$this->html.="<thead>
																				<tr>";
												foreach($item_fetch_header_only as  $item_fetch_header_only_get)
												{													
														$this->html.="<th>".$item_fetch_header_only_get."</th>";
												}
												$this->html.="</tr>
																			</thead><tbody>";
											}
											else
											{
													$item_fetch_body =$line_of_text;
													$this->html.="<tr>";
												foreach($item_fetch_body as  $item_fetch_body_get)
												{
														$this->html.="<td>".$item_fetch_body_get."</td>";
												}
												$this->html.="</tr>";
											}								
							}
							$count_array++;
						}
			return $this->html.=" </tbody></table>";
}
}
?>
