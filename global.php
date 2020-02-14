<?php
/*
PHP IRC BOT MULTI-THREAD V1.1 BY &NORAH.C.IV
*/

/* INICIO Funções Extras */

function Uptime(){
    $str   = @file_get_contents('/proc/uptime');
    $num   = floatval($str);
    $secs  = $num % 60;      $num = intdiv($num, 60);
    $mins  = $num % 60;      $num = intdiv($num, 60);
    $hours = $num % 24;      $num = intdiv($num, 24);
    $days  = $num;

    return $days." DAYS ".$hours." HOURS ".$mins." MINUTES ".$secs." SECONDS";
}

function ConsultaBIN($binTEMP){
    
        // CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$binTEMP);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $outputbin = curl_exec($ch);
        curl_close($ch);
    
    // DECODIFICANDO RESPOSTA EM JSON
            $jsonOUTPUT = json_decode($outputbin, true);

            // DEFININDO VARIAVEL COM NOME AMIGAVEL
            $bandeira = $jsonOUTPUT['scheme'];
            $tipo = $jsonOUTPUT['type'];
            $nivel = $jsonOUTPUT['brand'];
            $pais = $jsonOUTPUT['country']['alpha2'];
            $moeda = $jsonOUTPUT['country']['currency'];
            $bancoNOME = $jsonOUTPUT['bank']['name'];
            $bancoURL = $jsonOUTPUT['bank']['url'];
            $bancoPHONE = $jsonOUTPUT['bank']['phone'];
    
    return $jsonOUTPUT;
}
        

function randomBR(){
    $headers = array( 
            "POST /ferramentas_online.php HTTP/1.0", 
            "Content-type: application/x-www-form-urlencoded; charset=UTF-8", 
            "Accept: application/json", 
            "Origin: https://www.4devs.com.br", 
        ); 
    $idade  = rand(20,50);
    $data   = 'acao=gerar_pessoa&cep_cidade=&cep_estado=&idade='.$idade.'&pontuacao=S';
    $curl   = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://www.4devs.com.br/ferramentas_online.php");
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/* FIM Funções Extras */
 
/* VARIAVEIS PARA DETERMINAÇÃO DE SERVIDOR NICK etc.. */
$CONFIG = array();
$CONFIG['server'] = 'irc.chknet.cc'; // SERVIDOR PUBLICO DA CHKNET
$CONFIG['nick'] = 'CheckNet'; // BOT NICKNAME
$CONFIG['port'] = 6667; // PORTA DO SERVIDOR [6667] - [6697]
$CONFIG['channel'] = '#global'; // CANAL A SE CONECTAR
$CONFIG['name'] = 'CheckNet Service'; // NOME REAL DO BOT
$CONFIG['admin_pass'] = 'standby'; // SENHA ADMIN DO BOT
 
/* IMPEDINDO QUE O SCRIPT PARE APOS 30 SEGUNDOS */
set_time_limit(0);
 
/* CONEXÃO */
$con = array();
 
/* INICIANDO O BOT */
init();
 
function init()
{
        global $con, $CONFIG;
       
        // CRIANDO A VARIAVEL DE BUFFER
        $old_buffer = "";
 
        /* NECESSARIO PARA VER SE PRECISAMOS ENTRAR NO CANAL DURANTE A PRIMEIRA ITERAÇÃO DO LOOP PRINCIPAL */
        $firstTime = true;
       
        /* CONEXÃO COM O SERVIDOR DA CHKNET */
        $con['socket'] = fsockopen($CONFIG['server'], $CONFIG['port']);
       
        /* CHECANDO SE A CONEXÃO FOI EFETUADA */
        if (!$con['socket']) {
                print ("Could not connect to: ". $CONFIG['server'] ." on port ". $CONFIG['port']);
        } else {
                /* ENVIANDO DADOS AOS SERVICOS DO SERVIDOR */
                cmd_send("USER ". $CONFIG['nick'] ." carding.network carding.network :". $CONFIG['name']);
                cmd_send("NICK ". $CONFIG['nick'] ." carding.network");
               
                /* AQUI ESTA O LOOP. LE OS DADOS RECEBIDOS (da conexão do soquete) */
                while (!feof($con['socket']))
                {
                        /* PENSE EM $con['buffer']['all'] COMO UMA LINHA DE MENSAGEM DE BATE PAPO.
                        ESTAMOS RECEBENDO UMA 'LINHA' E SE LIVRANDO DO ESPAÇO EM BRANCO AO REDOR DELA. */
                        $con['buffer']['all'] = trim(fgets($con['socket'], 4096));
                       
                        /* PRING A line/buffer PARA O CONSOLE
                        UTILIZADO <- PARA IDENTIFICAR DADOS RECEBIDOS, -> PARA SAIDA. ISTO PARA QUE POSSAMOS IDENTIFICAR MENSAGENS QUE APARECEM NO CONSOLE */
                        print date("[d/m @ H:i]")." <- ".$con['buffer']['all'] ."\n";
                       
                        /* JOGANDO PING-PONG COM O SERVIDOR PARA DIZER AO SERVIDOR QUE AINDA ESTAMOS CONECTADOS */
                        if(substr($con['buffer']['all'], 0, 6) == 'PING :') {
                                /* PONG : É SEGUIDO PELA LINHA QUE O SERVIDOR NOS ENVIOU DURANTE O PING */
                                cmd_send('PONG :'.substr($con['buffer']['all'], 6));
                                /* CASO A PRIMEIRA CONEXÃO & PING SEJA BEM SUCEDIDA - ABRIMOS A CONEXÃO COM OS CANAIS DA REDE */
                                if ($firstTime == true){
                                        cmd_send("JOIN ". $CONFIG['channel']);
                                        cmd_send("PRIVMSG NickServ :identify norah235144\n");
                                        cmd_send("PART #BRAZIL\n");
                                        cmd_send("PART #CCPOWER\n");
                                        cmd_send("PART #UNIX\n");
                                        cmd_send("PART #PAYMENT\n");
                                        cmd_send("PART #ALTERNATIVE\n");
                                        cmd_send("PART #CHECK\n");
                                        /* A PROXIMA VEZ QUE O BOT CHEGAR ATE AQUI, NÃO SERÁ A PRIMEIRA VEZ */
                                        $firstTime = false;
                                }
                                /* VERIFICA SE TEMOS UMA NOVA LINHA DE BATE-PAPOS PARA ANALISAR. SE NÃO O FIZER, NÃO HÁ NECESSIDADE DE ANALIZAR OS DADOS NOVAMENTE. */
                        } elseif ($old_buffer != $con['buffer']['all']) {
                                /* A PARTIR DAQUI O BOT IRÁ LER TODOS OS TEXTOS ENVIADOS NO CANAL E SE FAMILIARIZAR ENTRE ELES PARA PODER EFETUAR RESPOSTAS OU COMANDOS DE CONSULTAS PEDIDAS A ELE. */
                                // log DO BUFFER REGISTRADO EM "log.txt" ( ARQUIVO
                                // CRIADO AUTOMATICAMENTE).
                                // log_to_file($con['buffer']['all']);
                               
                                // FAZ SENTIDO DO BUFFER
                                parse_buffer();
                               
                                // AGORA O PROCESSO DOS COMANDOS EMITIDOS PARA O BOT
                                process_commands();
                               
                        }
                        // SETA BUFFER SE HOUVER (EVITANDO O FLOOD)
                        if(isset($con['buffer']['all'])) { $old_buffer = $con['buffer']['all']; }
                }
        }
}
 
/* ACEITA O COMANDO COMO ARGUMENTO , ENVIA O COMANDO PARAO SERVIDOR E, EM SEGUIDA EXIBE O COMANDO NO CONSOLE PARA DEPURAÇÃO. */
function cmd_send($command)
{
        global $con, $time, $CONFIG;
        /* ENVIANDO O COMANDO - LENDO E ESCREVENDO */
        fputs($con['socket'], $command."\n\r");
        /* PARA EXIBIT O COMANDO LOCALMENTE, COM O UNICO OBJETIVO DE VERIFICAR A SAIDA. ( A LINHA NÃO É REALMENTE NECESSÁRIA) */
        print (date("[d/m @ H:i]") ."-> ". $command. "\n\r");
       
}
 
function log_to_file ($data)
{
        $filename = "log.txt";
        $data .= "\n";
        // ABRIR O ARQUIVO DE LOG
        if ($fp = fopen($filename, "ab"))
    {
        // AGORA REGISTRANDO TUDO NO ARQUIVO.TXT
        if ((fwrite($fp, $data) === FALSE))
        {
            echo "Could not write to file.<br />";
        }
    }
    else
    {
        echo "File could not be opened.<br />";
    }
}
 
function process_commands()
{
        global $con, $CONFIG;
       
        /* TIME */
        if(strtoupper($con['buffer']['text']) == '!TIME') {
                cmd_send(prep_text("07TIME" , date(" 06F j, Y, g:i a", time())));
        }

        /* HELP */
        if(strtoupper($con['buffer']['text']) == '!HELP') {

      cmd_send(prep_text("07BOT HELP", " 02ACCESS → [05404 - PAGE NOT FOUND02]06 TO HELP YOU / OR JOIN07 #HELP04 |07 #GLOBAL"));

        }

        /* STATUS */
        if(strtoupper($con['buffer']['text']) == '!STATUS') {

            $GERADAS = false;
            $CHKFULL = false;
            $GGELO = false;
            $CADSUS = true;
            $CHKBIN = false;
            $LOOKUP = false;
            $RANDOMBR = true;
            $RANDOMUS = false;
            $PROXY = true;
            

            if($GERADAS) { $GERADAS = "03✔"; } else { $GERADAS = "04✘"; }
            if($CHKFUll) { $CHKFULL = "03✔"; } else { $CHKFULL = "04✘"; }
            if($GGELO) { $GGELO = "03✔"; } else { $GGELO = "04✘"; }
            if($CADSUS) { $CADSUS = "03✔"; } else { $CADSUS = "04✘"; }
            if($CHKBIN) { $CHKBIN = "03✔"; } else { $CHKBIN = "04✘"; }
            if($LOOKUP) { $LOOKUP = "03✔"; } else { $LOOKUP = "04✘"; }
            if($RANDOMBR) { $RANDOMBR = "03✔"; } else { $RANDOMBR = "04✘"; }
            if($RANDOMUS) { $RANDOMUS = "03✔"; } else { $RANDOMUS = "04✘"; }
            if($PROXY) { $PROXY = "03✔"; } else { $PROXY = "04✘"; }
            
      cmd_send(prep_text("07SERVICES", "02 → [07CHK-FULL$CHKFULL] - [07GG$GERADAS] - [07GG-ELO$GGELO] - [07BIN$CHKBIN] - [07CADSUS$CADSUS] - [07LOOKUP$LOOKUP] - [07PROXY$PROXY] - [07RND BR$RANDOMBR] - [07RND US$RANDOMUS]"));

        }


        /* UPTIME */
        if(strtoupper($con['buffer']['text']) == '!UPTIME') {
            $SYSuptime = Uptime();
            cmd_send(prep_text("07BOT UPTIME", "06 $SYSuptime"));
        }

         /* BOTSYS */
        if(strtoupper($con['buffer']['text']) == '!BOTSYS') {

                $version = "1.9";
                $release = "15/10/2019";
                $NAMESERVER = php_uname('n');
                $CPUdistro = php_uname('a');

                $args = explode(" ", $con['buffer']['text']);
               
            cmd_send(prep_text("07BOT SYSTEM", "06 $CPUdistro04 |07 PHP BOT VERSION »06 $version 04|07 UPDATED »06 $release 04|07 CPU NAME » 06$NAMESERVER04 |07 #GLOBAL"));
        }
       
        /* NICK */
        if (substr(strtoupper($con['buffer']['text']), 0, 5) == ".NICK"){
                $args = explode(" ", $con['buffer']['text']);
               
                if (count($args) < 3) {
                        cmd_send(prep_text("07NICK", "02SYNTAX »06 .NICK [07ADMIN] 10NEW_NICK"));
                } else {
                        if ($args[1] == $CONFIG['admin_pass'])
                        cmd_send("NICK ". $args[2]);
                        else
                        cmd_send(prep_text("07NICK", "05INVALID PASSWORD !"));
                }
        }
 
        /* PROXY */
        if(strtoupper($con['buffer']['text']) == '!PROXY'){
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
               
                // ENVIA RESPOSTA AO CANAL
                cmd_send(prep_text("07PROXY"," 07IP ->06 $proxy 04|07 PORT »06 $PortaProxy04|07 TYPE » 06$TipoProxy 04| 07COUNTRY » 06$PaisProxy04 | 07VELOCITY » 06$VelocidadeProxy 04|07 #GLOBAL"));
        }
    
        /* CHK */
        if(strtoupper($con['buffer']['text']) == '!CHK'){
            
            // ENVIA RESPOSTA AO CANAL
                cmd_send(prep_text("07CHKFULL"," 05 CHECKER FULL IS 06[DISABLED]10 BY BOT ADMINISTRATOR"));
        }

        /* randomBR */
        if(strtoupper($con['buffer']['text']) == '!RANDOMBR'){
                
                $dados = json_decode(randomBR());
               
                // ENVIA RESPOSTA AO CANAL
                cmd_send(prep_text("07RAMDOM BR"," 07NAME »02 $dados->nome 04|07 CPF » 02$dados->cpf 04|07 RG » 02$dados->rg 04| 07BIRTH » 02$dados->data_nasc 04|07 ZIP » 02$dados->cep 04| 07ADDRESS » 02$dados->endereco - 10$dados->numero 04|07 DISTRICT » 02$dados->bairro 04|07 CITY » 02$dados->cidade 04| 07STATE » 02$dados->estado 04|07 PHONE » 02$dados->celular "));
        }
    
         /* RANDOMUS */
        if(strtoupper($con['buffer']['text']) == '!RANDOMUS'){
            
            // ENVIA RESPOSTA AO CANAL
                cmd_send(prep_text("07RANDOM US"," 05 RANDOM US GENERATOR IS 06[DISABLED]10 BY BOT ADMINISTRATOR"));
        }
    

        /* BIN */
       if(strtoupper(substr($con['buffer']['text'], 0, 6)) == '!BIN'){

        $chkBIN = explode(" ", $con['buffer']['text']);

         if (count($chkBIN) < 6) {

        cmd_send(prep_text("07BIN", " 04404 ERROR »06 !BIN 02[ENTER A VALID BIN NUMBER TO CONSULT]10 EX -> 1 2 3 4 5 6 DIGITS "));

    } else {

        $CheckBIN = explode(" ", $con['buffer']['text']);

                // CURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$CheckBIN[0]);
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
               
                // ENVIA RESPOSTA AO CANAL
                cmd_send(prep_text("07BIN", " 07CheckBIN -> 10$CheckBIN[0] 04|07 FLAG » 02$bandeira04 |07 TYPE » 02$tipo 04|07 COUNTRY » 02$pais 04|07 LEVEL » 02$nivel 04| 07CURRENCY » 02$moeda 04|07 BANK INFO » 02$bancoNOME - 10$bancoPHONE 06[$bancoURL]04 |07 #GLOBAL"));
        
            }
        }
       
    
        /* BIN beta */
       if(strtoupper(substr($con['buffer']['text'])) == '!betabin'){
           
           $gatilho = explode(' ', $con['buffer']['text']);
           $gatilho[0] = '!betabin';
           $gatilho[1] = $binTEMP, 0, 6();

                // ENVIA RESPOSTA AO CANAL
                cmd_send(prep_text("07BIN", " 07CheckBIN -> 10$CheckBIN[0] 04|07 FLAG » 02$bandeira04 |07 TYPE » 02$tipo 04|07 COUNTRY » 02$pais 04|07 LEVEL » 02$nivel 04| 07CURRENCY » 02$moeda 04|07 BANK INFO » 02$bancoNOME - 10$bancoPHONE 06[$bancoURL]04 |07 #GLOBAL"));
        }

        /* Noob */
        if(strtoupper(substr($con['buffer']['text'], 0, 5)) == '!NOOB') {
                $args = explode(" ", $con['buffer']['text'], 2);
                $name = (!empty($args[1]))?$args[1]:"beginner";
               cmd_send(prep_text("07BEGINNER HELP","10 WELCOME,06 ".$name."02, TO 07#GLOBAL 06CARDING CHANNEL 02ACCESS TO → [10HTTP://CARDING.NETWORK]06 FOR MORE INFORMATION"));
        }
       
        /* No PMs */
        if(strtoupper(substr($con['buffer']['text'], 0, 5)) == '!PM') {
                cmd_send(prep_text("07PLEASE","05 DO NOT SEND PRIVATE MESSAGES TO CHANNEL SERVICES !"));
        }
 
}
 
function parse_buffer()
{
       
        /*
        :username!~identd@hostname JOIN :#CHKNET
        :username!~identd@hostname PRIVMSG #CHKNET :action text
        :username!~identd@hostname command channel :text
        */
       
        global $con, $CONFIG;
               
        $buffer = $con['buffer']['all'];
        $buffer = explode(" ", $buffer, 4);
       
        /* PARA OBTER NOME DE USUARIO */
        $buffer['username'] = substr($buffer[0], 1, strpos($buffer['0'], "!")-1);
       
        /* PARA OBTER O IDENTD */
        $posExcl = strpos($buffer[0], "!");
        $posAt = strpos($buffer[0], "@");
        $buffer['identd'] = substr($buffer[0], $posExcl+1, $posAt-$posExcl-1);
        $buffer['hostname'] = substr($buffer[0], strpos($buffer[0], "@")+1);
       
        /* O USUARIO E O HOST TODO O SHABANG */
        $buffer['user_host'] = substr($buffer[0],1);
       
        /* RESOLVER O COMANDO QUE O USUÁRIO ESTÁ ENVIANDO DE O TEXTO "GERAL" ENVIADO AO CANAL QUE A CONEXÃO FOI EFETUADA
 
        TAMBEM EFETUA FORMATAÇÃO DO #buffer['text'] PARA QUE ELE POSSA SER BEM REGISTRADO.
        */
        switch (strtoupper($buffer[1]))
        {
                case "JOIN":
                        $buffer['text'] = "*JOINS: ". $buffer['username']." ( ".$buffer['user_host']." )";
                        $buffer['command'] = "JOIN";
                        $buffer['channel'] = $CONFIG['channel'];
                        break;
                case "QUIT":
                        $buffer['text'] = "*QUITS: ". $buffer['username']." ( ".$buffer['user_host']." )";
                        $buffer['command'] = "QUIT";
                        $buffer['channel'] = $CONFIG['channel'];
                        break;
                case "NOTICE":
                        $buffer['text'] = "*NOTICE: ". $buffer['username'];
                        $buffer['command'] = "NOTICE";
                        $buffer['channel'] = substr($buffer[2], 1);
                        break;
                case "PART":
                        $buffer['text'] = "*PARTS: ". $buffer['username']." ( ".$buffer['user_host']." )";
                        $buffer['command'] = "PART";
                        $buffer['channel'] = $CONFIG['channel'];
                        break;
                case "MODE":
                        $buffer['text'] = $buffer['username']." sets mode: ".$buffer[3];
                        $buffer['command'] = "MODE";
                        $buffer['channel'] = $buffer[2];
                break;
                case "NICK":
                        $buffer['text'] = "*NICK: ".$buffer['username']." => ".substr($buffer[2], 1)." ( ".$buffer['user_host']." )";
                        $buffer['command'] = "NICK";
                        $buffer['channel'] = $CONFIG['channel'];
                break;
               
                default:
                        // PROVAVELMENTE SEJA PRIVMSG
                        $buffer['command'] = $buffer[1];
                        $buffer['channel'] = $buffer[2];
                        $buffer['text'] = substr($buffer[3], 1);      
                break;
        }
        $con['buffer'] = $buffer;
}
 
function prep_text($type, $message)
{
        global $con;
        return ('PRIVMSG '. $con['buffer']['channel'] .' :['.$type.']'.$message);
}
 
?>
