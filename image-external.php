<?php // ♣
	
	// 127.0.0.1/projetos/vanuatu/Vanuatu-Image-Buffer/ext/200x200/http://fc02.deviantart.net/fs71/f/2010/056/e/9/Japanese_Girl_by_Dreamsofnight.jpg
	// 127.0.0.1/vanuatu/Vanuatu-Image-Buffer/ext/200x200/http://127.0.0.1/x.png
	
	require_once 'config.php';
	
	$x = ( isset($_GET['x']   ) ) ? $_GET['x'] : $options['size']['width']  ;
	$y = ( isset($_GET['y']   ) ) ? $_GET['y'] : $options['size']['height'] ;
	$url = ( isset($_GET['url'] ) ) ? str_replace(":/", "://", $_GET['url']) : "" ;
	$bufferDir = "buffer-external";
	preg_match("/\/.*\.(.*)$/", $url, $ext);
	$ext = $ext[1];
	$md5 = md5( preg_replace("/http(.){0,1}:\/\//", "", $url) );
	$bufferFile = "{$md5}_-_{$x}_x_{$y}.{$ext}";
	
	// SE A IMAGEM JÁ ESTIVER NO BUFFER, APENAS 
	// REDIRECIONA PARA A IMAGEM CONTIDA NO BUFFER
	if( file_exists("{$bufferDir}/{$bufferFile}") ){
		$thisDir = basename( dirname($_SERVER['SCRIPT_FILENAME']) );
		$uri = $_SERVER['REQUEST_URI'];
		$goTo = preg_replace("/\/.*/", "", strtolower($_SERVER['SERVER_PROTOCOL'])) ."://". $_SERVER['HTTP_HOST'] . substr($uri, 0, strpos($uri, $thisDir)).$thisDir."/{$bufferDir}/{$bufferFile}";
		header("Location: $goTo");
		exit;
	}
	
	// IF DOWNLOAD-ORIGINAL-IMAGE
	if( $options['external']['download-original-image'] ){
		// SE A IMAGEM ORIGINAL AINDA NÃO EXISTIR NO BUFFER
		// SALVA A IMAGEM ORIGINAL
		if( !file_exists($original_file = "{$bufferDir}/{$md5}_-_original.{$ext}") ){
			require_once 'phpthumb/ThumbLib.inc.php';
			$thumb = PhpThumbFactory::create($url);
			$thumb->save($original_file);
		}
		
		// SE A IMAGEM ORIGINAL EXISTIR NO BUFFER
		// APENAS MONTA O OBJETO DESTA IMAGEM
		else{
			require_once 'phpthumb/ThumbLib.inc.php';
			$thumb = PhpThumbFactory::create($original_file);
		}
	}
		
	// SALAVA A IMAGEM NO TAMANHO DESEJADO NO BUFFER 
	// E RETORNA A IMAGEM DO TAMANHO DESEJADO
	$thumb->adaptiveResize($x, $y);
	$thumb->save("{$bufferDir}/{$bufferFile}");
	$thumb->show();