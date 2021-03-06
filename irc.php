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

// TIME ZONE
date_default_timezone_set('America/Santiago');

// CONFIG PARAMETROS 
$server = 'irc.chknet.cc'; // irc chknet server
$port = 6667; // port irc.chknet.cc
$nickname = 'CheckNet'; //nickname of bot viadex24
$ident = 'CheckNet'; //indeitify bot 
$realname = 'Created By Norah_C_IV'; //profile of BOT
$channel = '#HISPANO'; // channel of chknet made with norah

// conexão com a rede
$socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
$error = socket_connect( $socket, $server, $port );


//TRATAMENTO DE ERRO P CONEXÃO MAL SUCEDIDA
if ( $socket === false ) {
    $errorCode = socket_last_error();
    $errorString = socket_strerror( $errorCode );
    die( "Error $errorCode: $errorString\n");
}

// ENVIANDO INFO DO REGISTRO 
socket_write( $socket, "NICK $nickname\r\n" );
socket_write( $socket, "USER $ident * 8 [$CPUdistro] :$realname\r\n" );

// Finalmente, Loop Até o Soquete Fecha

while ( is_resource( $socket ) ) {
    
    //buscar os dados do soquete.
    $data = trim( socket_read( $socket, 1024, PHP_NORMAL_READ ) );
    echo $data . "\n";

    // Dividindo os dados em pedaços
    $ex = explode(' ', $data);
    
    // Preenchendo o array evita feio indefinido
    $ex = array_pad( $ex, 10, '' );

    // Manipulador de ping
    // PING : irc.chknet.cc
    if ( $ex[0] === 'PING' ) {
      socket_write( $socket, 'PONG ' . $ex[1] . "\r\n" );
    }
     if($ex[0] == ":NickServ!services@services.chknet" && $ex[4] == "accepted") {
       socket_write( $socket, 'JOIN ' . $channel . "\r\n" );
       socket_write( $socket, "PRIVMSG NickServ :identify norah235144\n" );
       socket_write( $socket, "PART #BRAZIL\n");
       socket_write( $socket, "PART #UNIX\n");
       socket_write( $socket, "PART #CCPOWER\n");
       socket_write( $socket, "PART #alternative\n");
       socket_write( $socket, "PART #check\n");
       socket_write( $socket, "PART #cctools\n");
       socket_write( $socket, "PART #jcheck\n");
       socket_write( $socket, "JOIN #mexico");

     }

      if($ex[1] == "PRIVMSG" && $ex[0] == $nickname){
      $comando = str_replace(":!", "", preg_replace('/\s+/', '', $ex[3]));
      $nicktmp = explode('!', $ex[0]);
      $nickCMD = str_replace(":", "", $nicktmp[0]);
      $hosttmp = explode('@', $nicktmp[1]);
      $identdtmp = $hosttmp[0];
    }

     //   [0]                       [1]    [2]     [3]
     //  Nickname!ident@hostname PRIVMSG #USACC : !test
      if ( $ex[3] === ':!help' ) {
        $resposta = "07[ChkAYUDA] → 02[LINK]04 | [https://carding.network/] ";
        socket_write( $socket, 'PRIVMSG ' . $ex[2] . " :$resposta\r\n" );
     }

     if ( $ex[3] === ':!status' ) {
        $resposta = "07[ChkBOT] → 02[SERVICIOS] ←→02 [!GGBB]03 ONLINE 04|02 [!CHK] 03ONLINE 04|02 [!IP]03 ONLINE 04|02 [!BIN]03 ONLINE 04|02 [!CELL] 03ONLINE 04|02 [!PROXY]03 ONLINE 04|02 [!STATUS]03 ONLINE 04|02 [!RANDOM BR]03 ONLINE !07 [BETA] ";
        socket_write( $socket, 'PRIVMSG ' . $ex[2] . " :$resposta\r\n" );
     }

     if ( $ex[3] === ':!comandos' ) {
        $resposta = "07[ChkBOT] → 02[COMANDOS] [!GGBB] CHK GENERADO 04| [!CHK] CHK FULL 04| [!IP] IP LOCATOR 04| [!BIN] CHK BANCO DE INFORMACIÓN 04| [!CELL] ANALIZAR TELEFONO NUMERO 04| [!PROXY] SERVICIO DE PROXI 04| [!STATUS] ESTADO DOS SERVICIOS 04| [!RANDOM BR] GENERADOR DE DATOS  !07 [BETA]";
        socket_write( $socket, 'PRIVMSG ' . $ex[2] . " :$resposta\r\n" );
     }

     if ( $ex[3] === ':!version' ) {
        $resposta = "07[ChkBOT]  →02 [CheckNet BOT Version 4.0 [BETA] 27/09/2019] 04| 07#[$ex[2]] ";
        socket_write( $socket, 'PRIVMSG ' . $ex[2] . " :$resposta\r\n" );
      }

     if ( $ex[3] === ':!uptime' ) {
        $resposta = "07[ChkBOT]  →02TIEMPO DE EJECUCIÓN = 06 [$SYSuptime]";
        socket_write( $socket,'PRIVMSG' . $ex[2] . ":resposta\r\n");
     }

     if ( $ex[3] === ':!uname' ) {
        $resposta = "07[ChkBOT]  →02 BOT SHELL =  06 [$CPUdistro]";
        socket_write( $socket,'PRIVMSG' . $ex[2] . ":resposta\r\n");
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
    $resposta = "07[ChkBIN] → 2 [BIN] $checkBIN 04| [BANDERA] $bandeira 04| [TIPO] $tipo 04| [NIVEL] $nivel 04| [MONEDA] $moeda 04| [PAÍS] $pais 04| [BANCO] $bancoNOME / $bancoURL 04| [TELÉFONO] $bancoPHONE 04| 07[$ex[2]]";

    // ENVIANDO RESPOSTA AO IRC
    socket_write($socket,'PRIVMSG '.$ex[2]." :$resposta\r\n" );

      }

     if ( $ex[3] === ':!chk' ) {

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
    //SEPARANDO DADOS
    $ex = explode(' ',$output);

        $resposta = "07[ChkFULL] → $output ";

        socket_write( $socket, 'PRIVMSG ' . $ex[2] . " :$resposta\r\n" );
     }


     if ( $ex[3] === ':!gg' ) {
        $resposta = "07[ChkGG]  →02 [ESTE MANDO ESTÁ DESACTIVADO] 04| 07#[$ex[2]] ";
        socket_write( $socket, 'PRIVMSG ' . $ex[2] . " :$resposta\r\n" );
     }


    if ( $ex[3] === ':!ggbb' ) {
         // SEPARA SOMENTE OS 11 DIGITOS
    $gerada = $ex[4];
    // CURL
    $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "http://central.bronxservices.net/api/cartao/gerada/api.php?lista=$gerada");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    //SEPARANDO DADOS
   $ex = explode('💸',$output);
    
    // DEFININDO MENSAGEM DE RESPOSTA AO IRC
    $resposta = "07[ChkGGBB] → $output ";

    // ENVIANDO RESPOSTA AO IRC
    print_r('PRIVMSG ');
    
    socket_write( $socket, 'PRIVMSG ' . $ex[2] . " :$resposta\r\n" );
  }
     
     if ( $ex[3] === ':!ip' ) {
         // SEPARA SOMENTE OS 11 DIGITOS
    $iplist = $ex[4];
    $IPKEY = '7b6fab341bd4c7cd10c7e116c177c8c8fb246f77033f020b37d6b88467f14de1';
    // CURL
    $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "http://api.ipinfodb.com/v3/ip-city/?key=$IPKEY&ip=$iplist");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    //SEPARANDO DADOS
    $ex = explode(';', $output);
    
    // DEFININDO MENSAGEM DE RESPOSTA AO IRC
    $resposta = "07[ChkLOOKUP] → 02 $ex[2] » $ex[3] 04| [ESTADO-PROVINCIA] $ex[5] 04| [CIUDAD] $ex[6] 04| [PAIS] $ex[4] 04| [CEP] $ex[7] 04| [LONGITUD] $ex[8] 04| [LATITUD] $ex[9] 04| 07#HISPANO  ";

    // ENVIANDO RESPOSTA AO IRC
    print_r('PRIVMSG ');
    socket_write($socket,'PRIVMSG '.$ex[2]." :$resposta\r\n" );
     }

     if ( $ex[3] === ':!proxy' ) {
    // LINK PROXY
    $linkproxy = 'https://gimmeproxy.com/api/getProxy?coutry=BR&api_key=5a1a1257-cf8a-4975-b2fb-f01f13a3d023&protocol=SOCKS5';
    // CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$linkproxy");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);

    // DECODIFICANDO RESPOSTA EM JSON
    $jsonOUTPUT = json_decode($output, true);

    // DEFININDO VARIAVEL COM NOME AMIGAVEL
    $proxy = $jsonOUTPUT['ip'];
    $PortaProxy = $jsonOUTPUT['port'];
    $TipoProxy = $jsonOUTPUT['protocol'];
    $PaisProxy = $jsonOUTPUT['country'];
    $VelocidadeProxy = $jsonOUTPUT['speed'];

    // DEFININDO MENSAGEM DE RESPOSTA AO IRC
    $resposta = "07[ChkPROXY] → 02[DIRECCION] $proxy 04| [PUERTA] $PortaProxy 04| [TIPO] $TipoProxy 04| [UBICACIÓN] $PaisProxy 04| [VELOCIDAD]  $VelocidadeProxy 04|07 [$d[2]] ";

    // ENVIANDO RESPOSTA AO IRC
    socket_write( $socket,'PRIVMSG '.$ex[2]." :$resposta\r\n" );
    
      }

    if ( $ex[3] === ':!random br' ) {

     $dados = json_decode(GeraPessoa());

     $resposta = "07[ChkRANDOM] →02 [NOMBRE] $dados->nome  04| [CPF] $dados->cpf  04| [RG] $dados->rg 04| [NACIMIENTO] $dados->data_nasc 04| [CEP] $dados->cep 04| [RUA] $dados->endereco, $dados->numero 04| [BAIRRO] $dados->bairro 04| [CIDAD] $dados->cidade 04| [ESTADO] $dados->estado 04| [TELÉFONO] $dados->celular";

      // ENVIANDO RESPOSTA AO IRC

      socket_write( $socket,'PRIVMSG '.$ex[2]." :$resposta\r\n" );
    }



     if ( $ex[3] === ':!cell' ) {
    // SEPARA SOMENTE OS 11 DIGITOS
    $Number = substr($ex[4], 0, 16);
    $keyAPI = '5fa2c8a935ac364827f80d450b07d53d';
    // CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://apilayer.net/api/validate?access_key=$keyAPI&number=$Number&country_code=&format=1");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);

    // DECODIFICANDO RESPOSTA EM JSON
    $jsonOUTPUT = json_decode($output, true);

    // DEFININDO VARIAVEL COM NOME AMIGAVEL
    $numero = $jsonOUTPUT['international_format'];
    $codpais = $jsonOUTPUT['country_code'];
    $pais = $jsonOUTPUT['country_name'];
    $estado = $jsonOUTPUT['location'];
    $operadora = $jsonOUTPUT['carrier'];
    $linha = $jsonOUTPUT['line_type'];

    // DEFININDO MENSAGEM DE RESPOSTA AO IRC
    $resposta = "07[ChkCELL] → 02[NUMERO] $numero 04| [UBICACIÓN] $codpais 04| [PAIS] $pais 04| [ESTADO] $estado 04| [OPERADORA] 04| $operadora 04| [LINEA] $linha ";

    // ENVIANDO RESPOSTA AO IRC
    socket_write($socket,'PRIVMSG '.$ex[2]." :$resposta\r\n" );
    
      }
}
?>
