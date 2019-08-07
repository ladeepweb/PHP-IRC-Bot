<?php

// TIME ZONE
date_default_timezone_set('America/Santiago');

// CONFIG PARAMETROS 
$server = 'irc.chknet.cc'; // irc chknet server
$port = 6667; // port irc.chknet.cc
$nickname = 'CheckNet'; //nickname of bot viadex24
$ident = 'CheckNet'; //indeitify bot 
$realname = 'Created By Norah_C_IV'; //profile of BOT
$channel = '#CARDER'; // channel of chknet made with norah

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
socket_write( $socket, "USER $ident * 8 :$realname\r\n" );

// Finalmente, Loop Até o Soquete Fecha

while ( is_resource( $socket ) ) {
    
    //buscar os dados do soquete.
    $data = trim( socket_read( $socket, 1024, PHP_NORMAL_READ ) );
    echo $data . "\n";

    // Dividindo os dados em pedaços
    $d = explode(' ', $data);

    
    // Preenchendo o array evita feio indefinido
    $d = array_pad( $d, 10, '' );

    // SEPARAÇÃO DE NICK - IDENT - HOSTNAME [CheckNet~!CheckNet@CardingBotNetwork]
 if($d[1] == "PRIVMSG" && $d[2] == $nickname){
			$comando = str_replace(":!", "", preg_replace('/\s+/', '', $d[3]));
			$nicktmp = explode('!', $d[0]);
			$nickCMD = str_replace(":", "", $nicktmp[0]);
			$hosttmp = explode('@', $nicktmp[1]);
			$identdtmp = $hosttmp[0];
 }

    // Manipulador de ping
    // PING : irc.chknet.cc
    if ( $d[0] === 'PING' ) {
      socket_write( $socket, 'PONG ' . $d[1] . "\r\n" );
    }
     if ( $d[1] === '376' || $d[1] === '422' ) {
       socket_write( $socket, 'JOIN ' . $channel . "\r\n" );
       socket_write( $socket, "PRIVMSG NickServ :identify norah235144\n" );
       socket_write( $socket, "PART #BRAZIL\n");
       socket_write( $socket, "PART #UNIX\n");
       socket_write( $socket, "PART #CCPOWER\n");

     }

     //   [0]                       [1]    [2]     [3]
     //  Nickname!ident@hostname PRIVMSG #USACC : !test
      if ( $d[3] === ':!help' ) {
        $resposta = "06[_$nickCMD_] COMANDOS DISPONIVEIS → 07 [ !GGBB - !CHK - !CELL - !IP - !BIN - !TEMPMAIL - !RANDOMBR - !SRO - !CPF - !CNS - !NOME - !SSN ]03 EM BREVE TODOS EM FUNCIONAMENTO ! 13{BETA} ";
        socket_write( $socket, 'PRIVMSG ' . $d[2] . " :$resposta\r\n" );
     }

     if ( $d[3] === ':!status' ) {
        $resposta = "CHANNEL 07[#USACC] → 10CHK 04[OFF] 10BIN 03[ON] 10IP 03[ON] 10CELL 03[ON] 10GG 04[OFF] 05CANAL EM DESENVOLVIMENTO ! ";
        socket_write( $socket, 'PRIVMSG ' . $d[2] . " :$resposta\r\n" );
     }

      if ( $d[3] === ':!bin' ) {
    // SEPARA SOMENTE OS 6 PRIMEIROS DIGITOS
    $checkBIN = substr($d[4], 0, 6);
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
    $resposta = "06[_$nickCMD_] →  02BIN: 05$checkBIN 02BANDEIRA: 05$bandeira 02TIPO: 05$tipo 02NIVEL: 05$nivel 02MOEDA: 05$moeda 02PAÍS: 05$pais 02BANCO: 05$bancoNOME - $bancoURL 02TELEFONE: 05$bancoPHONE";

    // ENVIANDO RESPOSTA AO IRC
    socket_write($socket,'PRIVMSG '.$d[2]." :$resposta\r\n" );

      }

     if ( $d[3] === ':!chk' ) {
        $resposta = " →06[_$nickCMD_] 0Este Comando Esta 07[ DESATIVADO ]02 TENTE NOVAMENTE MAIS TARDE ! ";
        socket_write( $socket, 'PRIVMSG ' . $d[2] . " :$resposta\r\n" );
     }


     if ( $d[3] === ':!gg' ) {
        $resposta = " → 06[_$nickCMD_] 0Este Comando Esta 07[ DESATIVADO ]02 TENTE NOVAMENTE MAIS TARDE ! ";
        socket_write( $socket, 'PRIVMSG ' . $d[2] . " :$resposta\r\n" );
     }
     
     if ( $d[3] === ':!ip' ) {
         // SEPARA SOMENTE OS 11 DIGITOS
    $iplist = $d[4];
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
    $resposta = "10[_CHECK-BOT-IP_] → 06[_$nickCMD_] 05$ex[2] $ex[3] 02ESTADO-PROVINCIA: 05$ex[5] 02CIDADE: 05$ex[6] 02PAIS: 05$ex[4] 02CEP: 05$ex[7] 02LONGITUDE: 05$ex[8] 02LATITUDE: 05$ex[9] ";

    // ENVIANDO RESPOSTA AO IRC
    print_r('PRIVMSG ');
    socket_write($socket,'PRIVMSG '.$d[2]." :$resposta\r\n" );
     }

     if ( $d[3] === ':!cell' ) {
    // SEPARA SOMENTE OS 11 DIGITOS
    $Number = substr($d[4], 0, 16);
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
    $resposta = "06[_$nickCMD_] → 02NUMERO: 01$numero 02LOCAL: 03$codpais 02PAIS: 03$pais 02ESTADO: 03$estado 02OPERADORA: 03$operadora 02LINHA: 03$linha";

    // ENVIANDO RESPOSTA AO IRC
    socket_write($socket,'PRIVMSG '.$d[2]." :$resposta\r\n" );
    
      }
}
?>
