
<?php
 //unlink("img/product/prod8.webp");
$target_dir = "./img/product/";
if (isset($_POST["action"])){
    if ($_POST["action"]=='delete'){
        unlink($_POST["path"]."/".$_POST["name"]);
        exit();
    }
}
$filename = $_FILES["file"]["name"];
$ext = pathinfo($filename, PATHINFO_EXTENSION);
$uploadfile=$target_dir."manifest.".$ext;
$target_dir=$_POST["path"] ;
$uploadfile=$target_dir."/".$_POST["name"].".".$ext ;

if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile)) {
    echo $_POST["name"].".".$ext;
} else {
    echo "#";
}


?>
