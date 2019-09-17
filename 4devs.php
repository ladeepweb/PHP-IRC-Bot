<?php

function GeraPessoa(){
    $headers = array( 
            "POST /ferramentas_online.php HTTP/1.0", 
            "Content-type: application/x-www-form-urlencoded; charset=UTF-8", 
            "Accept: application/json", 
            "Origin: https://www.4devs.com.br", 
        ); 
    $idade	= rand(20,50);
    $data	= 'acao=gerar_pessoa&cep_cidade=&cep_estado=&idade='.$idade.'&pontuacao=S';
    $curl	= curl_init();
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

?>
