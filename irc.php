<?php
// SCRIPTS
require('4devs.php');

// VARIAVEIS
$version = "1.0.0";
$release = "20/09/2019";
$NAMESERVER = php_uname('n');
$CPUdistro = php_uname('a');

// PHP VARS
ini_set('default_charset','UTF-8');
//
// FUNÇÕES DO BOT CheckNet
//
function Uptime(){
    $str   = @file_get_contents('/proc/uptime');
    $num   = floatval($str);
    $secs  = $num % 60;      $num = intdiv($num, 60);
    $mins  = $num % 60;      $num = intdiv($num, 60);
    $hours = $num % 24;      $num = intdiv($num, 24);
    $days  = $num;

    return $days." DIAS ".$hours." HORAS ".$mins." MINUTOS ".$secs." SEGUNDOS";
}

// VARIAVEIS DO SERVIDOR 
$nickname = 'CheckNet';
$channel = '#hispano';
$server = 'irc.chknet.cc';
$port = 6667;
$ident = 'ChkBOT';
$realname = '[".$NAMESERVER."] NORAH_C_IV SERVICES';

// MASTER'S
$master = 'NORAH_C_IV';

// CONEXÃO COM O SERVIDOR 
$socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
$error = socket_connect( $socket, $server, $port );

// TRATAMENTO DE ERRO
if ( $socket === false ){
	$errorCode = socket_last_error();
	$errorString = socket_strerror( $errorCode );
	die ( "Error $errorCode: $errorString\n");
}

// ENVIANDO INFO DE REGISTRO DO BOT
socket_write( $socket, "NICK $nickname\r\n" );
socket_write( $socket, "USER $ident * 8 :$realname\r\n" );

// FIM DE LOOP & FECHAMENTO DO SOCKET
while ( is_resource( $socket ) ){
	//SEPARANDO DADOS DO SOCKET
	$data = trim( socket_read ( $socket, 1024, PHP_NORMAL_READ ) );
	echo $data . "\n";
    // SEPARANDO O SOCKET EM PARTES
	$ex = explode(' ', $data);

	// FECHANDO TRATAMENTO DE ERRO (PADDING THE ARRAY AVOIDS)
	$ex = array_pad( $ex, 10, '' );
}

// MANIPULADOR DE PING DO BOT
// PING TO : irc.chknet.cc 
if ( $ex[0] === 'PING' ){
	socket_write( $socket, 'PONG '. $ex[1] . "\r\n" );
}
if ( $ex[1] === '376' || $ex[1] === '422' ){
	socket_write( $socket, "JOIN #HISPANO\r\n");
	socket_write( $socket, "JOIN #MEXICO\r\n");
	socket_write( $socket, "PART #BRAZIL\r\n");
	socket_write( $socket, "PART #UNIX\r\n");
	socket_write( $socket, "PART #CCPOWER\r\n");
	socket_write( $socket, "PART #ALTERNATIVE\r\n");
	socket_write( $socket, "PART #HELP\r\n");
	socket_write( $socket, "PRIVMSG NickServ :identify norah235144\n");
}
// SEPARAÇÃO DO SOCKET EM PARTES 
//             [0]             [1]     [2]       [3]
// :Nickname!ident@hostname  PRIVMSG #CHANNEL :!comando

// COMANDOS DO BOT PARA USUARIOS

  if ( $ex[3] === ':!ajuda' ) {
        $resposta = "07[ChkAYUDA] → 02[LINK]04 | [https://paste24.com/ChkNet/ajuda] ";
        socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );
}

  if ( $ex[3] === ':!comandos' ) {
        $resposta = "07[ChkBOT] → 02[COMANDOS] [!GGBB] CHK GENERADO 04| [!CHK] CHK FULL 04| [!IP] IP LOCATOR 04| [!BIN] CHK BANCO DE INFORMACIÓN 04| [!CELL] ANALIZAR TELEFONO NUMERO 04| [!PROXY] SERVICIO DE PROXI 04| [!STATUS] ESTADO DOS SERVICIOS 04| [!RANDOM BR] GENERADOR DE DATOS  !07 [BETA]";
         socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );
}

  if ( $ex[3] === ':!status' ) {
        $resposta = "07[ChkBOT] → 02[SERVICIOS] ←→02 [!GGBB]03 ONLINE 04|02 [!CHK] 03ONLINE 04|02 [!IP]03 ONLINE 04|02 [!BIN]03 ONLINE 04|02 [!CELL] 03ONLINE 04|02 [!PROXY]03 ONLINE 04|02 [!STATUS]03 ONLINE ! 04|02 [!RANDOM BR]03 ONLINE !07 [BETA]";
        socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );
 }

   if ( $ex[3] === ':!uptime' ) {
  
    $SYSuptime = Uptime();

        $resposta = "07[ChkBOT] → 02[BOT-UPTIME] =>6 $SYSuptime  ";
        socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );
 }

    if ( $ex[3] === ':!random br' ) {

     $dados = json_decode(GeraPessoa());

     $resposta = "07[ChkRANDOM] →02 [NOMBRE] $dados->nome  04| [CPF] $dados->cpf  04| [RG] $dados->rg 04| [NACIMIENTO] $dados->data_nasc 04| [CEP] $dados->cep 04| [RUA] $dados->endereco, $dados->numero 04| [BAIRRO] $dados->bairro 04| [CIDAD] $dados->cidade 04| [ESTADO] $dados->estado 04| [TELÉFONO] $dados->celular\n";

          socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );
  }

  if ( $ex[3] === ':!bin' ) {
    // SEPARA SOMENTE OS 6 PRIMEIROS DIGITOS
    $checkBIN = substr($ex[4], 0, 6);
    // CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$checkBIN);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);

    // DECODIFICANDO RESPOSTA EM JSON
    $jsonOUTPUT = json_decode($output, true);

    // DEFININDO VARIAVEL COM NOME AMIGAVEL
    $bandeira = $jsonOUTPUT['scheme'];
    $tipo = $jsonOUTPUT['type'];
    $nivel = $jsonOUTPUT['brand'];
    $pais = $jsonOUTPUT['country']['alpha2'];
    $moeda = $jsonOUTPUT['country']['currency'];
    $bancoNOME = $jsonOUTPUT['bank']['name'];
    $bancoURL = $jsonOUTPUT['bank']['url'];
    $bancoPHONE = $jsonOUTPUT['bank']['phone'];

    // DEFININDO MENSAGEM DE RESPOSTA AO IRC
    $resposta = "07[ChkBIN] → 2 [BIN] $checkBIN 04| [BANDERA] $bandeira 04| [TIPO] $tipo 04| [NIVEL] $nivel 04| [MONEDA] $moeda 04| [PAÍS] $pais 04| [BANCO] $bancoNOME / $bancoURL 04| [TELÉFONO] $bancoPHONE 04| 07#HISPANO ";

    // ENVIANDO RESPOSTA AO IRC
    socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );

 } 

 if ( $ex[3] === ':!chk' ) {
         // SEPARA SOMENTE OS 16 DIGITOS
    $infocc = $ex[4];
    // CURL
    $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "http://central.bronxservices.net/api/cartao/full/api.php?lista=$infocc");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);

    // DEFININDO MENSAGEM DE RESPOSTA AO IRC
    $resposta = "07[ChkFULL] → $output ";

    // ENVIANDO RESPOSTA AO IRC
    socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );
  }

if ( $ex[3] === ':!test' ) {

	$resposta = "[ex[0]] => isto é um teste !";

	 // ENVIANDO RESPOSTA AO IRC
    socket_write( $socket,"PRIVMSG #HISPANO :$resposta\r\n" );
}

?>
