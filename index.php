

<?php
// fazer o upload via ajax


error_reporting(E_ALL);
require_once("DropboxClient.php");
require_once("UtilDropbox.php");

$dropbox = new DropboxClient(array(
	'app_key' => "296849h0cxdre4u",
	'app_secret' => "l76cm1vpnaocjrh",
	'app_full_access' => false,
),'en');

$util = new UtilDropBox();


if($util->handle_dropbox_auth($dropbox)){
	$conexao = $util->handle_dropbox_auth($dropbox);
}
if($conexao == 'conectado'){
	$dadosUser = $dropbox->GetAccountInfo();
	$files = $dropbox->GetFiles("",false);
}
?>


<html>
<head>
	<title>Dropbox</title>
<head>
	<meta charset="utf-8">

	<title>Sistema Doacao</title><meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="css/jquery-ui.theme.css" media="screen" rel="stylesheet" type="text/css">
	<link href="css/jquery-ui.structure.css" media="screen" rel="stylesheet" type="text/css">
	<link href="css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css">
	<link href="css/bootstrap.css" media="screen" rel="stylesheet" type="text/css">
	<link href="css/bootstrap-theme.min.css" media="screen" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
</head>

<body>

<div class="container">

	<?php
	if($conexao != 'conectado') {
		echo '<div class="col-xs-12" style="margin-top: 40px">';
		echo "<h4 style='border-bottom: 1px solid #ccc'>Conectar ao DropBox</h4>";
		echo "<a class='btn btn-primary' href=$conexao>Validar</a>";
		echo "</div>";
	}
	else{

		echo '<div class="col-xs-12" style="border-bottom: 1px solid #ccc">';
			echo '<h4 style="border-bottom: 1px solid #ccc">Dados da Conta:</h4>';
			echo '<p class="text-info"><strong>Nome:</strong> '.$dadosUser->name_details->familiar_name .'</p>';
			echo '<p class="text-info"><strong>Email:</strong> '. $dadosUser->email .'</p>';
			echo '<p></pa><a href="https://www.dropbox.com/home">Acessar meu dropbox</a></p>';
		echo '</div>';

	?>

	<?php
		if(empty($_FILES['the_upload'])) {
			?>
			<form  class="form" id="form-upload" enctype="multipart/form-data" method="POST" action="">
				<p>
					<label for="file">Selecione um arquivo</label>
					<input class="file" type="file" name="the_upload" />
				</p>
				<p><input class="btn btn-success" id="btn-upload" type="submit" name="submit-btn" value="Upload"></p>
			</form>
		<?php } else {

			$upload_name = $_FILES["the_upload"]["name"];
			echo "<p><strong>Uploading $upload_name:</strong></p>";
			$meta = $dropbox->UploadFile($_FILES["the_upload"]["tmp_name"], $upload_name);
			echo "<p class='text-success'>Upload realizado com sucesso</p>";
		}
	?>


<?php
		if(!empty($files)) {
			echo "<table class='table'>";
			echo "<th>Download</th>";
			echo "<th>Tamanho</th>";
			echo "<th>Tipo</th>";

			foreach($files as $file){
				echo '<tr>';
				echo "<td><strong><a href='" . $dropbox->GetLink($file) . "'>$file->path</a></strong></td>";
				echo '<td>'.$file->size.'</td>';

				if(!$file->is_dir){
					echo '<td>'.$file->mime_type.'</td>';
				}else{
					echo '<td>Diretório</td>';
				}
				echo '</tr>';
			}
			echo '</table>';
		}

	}
?>
</div>

