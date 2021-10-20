
<?php
require 'officetophp.php';
$target_dir = "./uploads/";
//$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$filename = $_FILES["file"]["name"];
$ext = pathinfo($filename, PATHINFO_EXTENSION);
$uploadfile=$target_dir."manifest.".$ext;

if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile)) {
    //echo "File is valid, and was successfully uploaded.\n";
} else {
   // echo "Possible file upload attack!\n";
}
$docObj = new DocxConversion($uploadfile);
$docText= $docObj->convertToText();
echo "finish";
file_put_contents('./uploads/out.txt', $docText);

?>
