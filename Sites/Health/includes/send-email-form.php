<?php 
// Pear library includes
// You should have the pear lib installed
include_once('Mail.php');
include_once('Mail/mime.php');

//Settings 
$max_allowed_file_size = 2048; // size in KB 
$allowed_extensions = array("pdf");
$upload_folder = 'uploads/'; //<-- this folder must be writeable by the script
$your_email = 'johncs2007@talktalk.net';//<<--  update this to your email address

$errors ='';

if(isset($_POST['submit']))
{
	//Get Form Data
	$name = $_POST['name'];
	$visitor_email = $_POST['email'];
	$user_message = $_POST['message'];
	$to = $your_email;
	$subject="New form submission";
	$from = $your_email;
	$text = "A user  $name has sent you this message:\n $user_message";
	$html = text2html($text);
		
	//Get the uploaded file information
	$name_of_uploaded_file =  basename($_FILES['uploaded_file']['name']);
	
	//get the file extension of the file
	$type_of_uploaded_file = substr($name_of_uploaded_file, 
							strrpos($name_of_uploaded_file, '.') + 1);
	
	$size_of_uploaded_file = $_FILES["uploaded_file"]["size"]/1024;
	
	///------------Do Validations-------------
	if(empty($_POST['name'])||empty($_POST['email']))
	{
		$errors .= "\n Name and Email are required fields. ";	
	}
	if(IsInjected($visitor_email))
	{
		$errors .= "\n Bad email value!";
	}
	
	if($size_of_uploaded_file > $max_allowed_file_size ) 
	{
		$errors .= "\n Size of file should be less than $max_allowed_file_size";
	}
	
	//------ Validate the file extension -----
	$allowed_ext = false;
	for($i=0; $i<sizeof($allowed_extensions); $i++) 
	{ 
		if(strcasecmp($allowed_extensions[$i],$type_of_uploaded_file) == 0)
		{
			$allowed_ext = true;		
		}
	}
	
	if(!$allowed_ext)
	{
		$errors .= "\n The uploaded file is not supported file type. ".
		" Only the following file types are supported: ".implode(',',$allowed_extensions);
	}
	
	//send the email 
	if(empty($errors))
	{
		//copy the temp. uploaded file to uploads folder
		$path_of_uploaded_file = $upload_folder . $name_of_uploaded_file;
		$tmp_path = $_FILES["uploaded_file"]["tmp_name"];
		
		if($is_uploaded = is_uploaded_file($tmp_path))
		{
		    if(!copy($tmp_path,$path_of_uploaded_file))
		    {
		    	$errors .= '\n error while copying the uploaded file';
		    }
		}
		
		//send the email
		$message = new Mail_mime();
		$message->setTXTBody($text);
		$message->setHTMLBody($html);
		$message->addAttachment($path_of_uploaded_file);
		$body = $message->get();
		$extraheaders = array("From"=>$from, "Subject"=>$subject,"Reply-To"=>$visitor_email, "Content-Type"=>'text/html; charset=UTF-8', "Content-Transfer-Encoding"=>'8bit');
		$headers = $message->headers($extraheaders);
		$imap_host = "imap.talktalk.net";
		$imap_port = 143;
		$smtp = @Mail::factory('smtp',array (
			  'host' => 'ssl://smtp.gmail.com',
			  'port' => '465',
			  'auth' => true,
			  'username' => 'johncs2008@gmail.com', //GMail user name
			  'password' => 'rdotbjlngiazfltz' // GMail password
		      ));
//		$smtp = @Mail::factory('smtp',array (
//			  'host' => 'ssl://smtp.talktalk.net',
//			  'port' => '587',
//			  'auth' => "PLAIN",
//			  'username' => 'johncs2007@talktalk.net', //TalkTalk user name
//			  'password' => 'hibs2007' // TalkTalk password
//		      ));

		$mail = @$smtp->send($to, $headers, $body);
		//redirect to 'thank-you page
		header('Location: thank-you.html');
	}
}
///////////////////////////Functions/////////////////
// Function to validate against any email injection attempts
function IsInjected($str)
{
  $injections = array('(\n+)',
              '(\r+)',
              '(\t+)',
              '(%0A+)',
              '(%0D+)',
              '(%08+)',
              '(%09+)'
              );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str))
    {
    return true;
  }
  else
    {
    return false;
  }
}

function text2html($plain) {
	$newhtml = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"><html>\n\t<body style=\"font-family:Arial,Helvetica,sans-serif; font-size:12pt;\">\n";
	$lines = explode("\n", $plain);
	foreach($lines as $line) {
		$ln = trim($line);
		if(!empty($ln)) {
			$words = explode(' ', $ln);
			foreach($words as $key => $word) {
				$wd = htmlspecialchars($word);
				$isurl = (
					substr($wd, 0, 7) == "http://" ||
					substr($wd, 0, 8) == "https://" ||
					substr($wd, 0, 6) == "ftp://" ||
					substr($wd, 0, 7) == "ftps://"
				);
				$hasdomain = (
					substr($wd, -4) == '.com' ||
					substr($wd, -6) == '.co.uk' ||
					substr($wd, -4) == '.org' ||
					substr($wd, -7) == '.org.uk' ||
					substr($wd, -4) == '.net'
				);
				$isemail = (substr_count($wd, '@') == 1);
				if($isurl) {
					$wd = '<a href="'.$wd.'">'.$wd.'</a>';
				} elseif($isemail) {
					$wd = '<a href="mailto:'.$wd.'">'.$wd.'</a>';
				} elseif($hasdomain) {
					$wd = '<a href="http://'.$wd.'">'.$wd.'</a>';
				} else {
					// Leave $wd unchanged
				}
				
				$words[$key] = $wd;
			}
			
			$newhtml .= "\t\t<p>".implode(' ', $words)."</p>\n";
		}
	}
	$newhtml .= "\t</body>\n</html>";
	return $newhtml;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
	<title>File upload form</title>
<!-- define some style elements-->
<style>
label,a, body 
{
	font-family : Arial, Helvetica, sans-serif;
	font-size : 12px; 
}

</style>	
<!-- a helper script for vaidating the form-->
<script language="JavaScript" src="scripts/gen_validatorv31.js" type="text/javascript"></script>	
</head>

<body>
<?php
if(!empty($errors))
{
	echo nl2br($errors);
}
?>
<form method="POST" name="email_form_with_php" 
action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data"> 
<p>
<label for='name'>Name: </label><br>
<input type="text" name="name" >
</p>
<p>
<label for='email'>Email: </label><br>
<input type="text" name="email" >
</p>
<p>
<label for='message'>Message:</label> <br>
<textarea name="message"></textarea>
</p>
<p>
<label for='uploaded_file'>Select A File To Upload:</label> <br>
<input type="file" name="uploaded_file">
</p>
<input type="submit" value="Submit" name='submit'>
</form>
<script language="JavaScript">
// Code for validating the form
// Visit http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
// for details
var frmvalidator  = new Validator("email_form_with_php");
frmvalidator.addValidation("name","req","Please provide your name"); 
frmvalidator.addValidation("email","req","Please provide your email"); 
frmvalidator.addValidation("email","email","Please enter a valid email address"); 
</script>
<noscript>
<small><a href='http://www.html-form-guide.com/email-form/php-email-form-attachment.html'
>How to attach file to email in PHP</a> article page.</small>
</noscript>

</body>
</html>
