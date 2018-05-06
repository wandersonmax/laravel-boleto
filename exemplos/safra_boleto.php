<?php
require 'autoload.php';
$beneficiario = new \Eduardokum\LaravelBoleto\Pessoa(
    [
        'nome' => 'ACME',
        'endereco' => 'Rua um, 123',
        'cep' => '99999-999',
        'uf' => 'UF',
        'cidade' => 'CIDADE',
        'documento' => '99.999.999/9999-99',
    ]
);

$pagador = new \Eduardokum\LaravelBoleto\Pessoa(
    [
        'nome' => 'Cliente',
        'endereco' => 'Rua um, 123',
        'bairro' => 'Bairro',
        'cep' => '99999-999',
        'uf' => 'UF',
        'cidade' => 'CIDADE',
        'documento' => '999.999.999-99',
    ]
);

$boleto = new Eduardokum\LaravelBoleto\Boleto\Banco\Safra(
    [
        'logo' => realpath(__DIR__ . '/../logos/') . DIRECTORY_SEPARATOR . '422.png',
        'dataVencimento' => Carbon\Carbon::create(2017, 2, 8, 0, 0, 0, 'America/Toronto'),
        'valor' => 629.98,
        'multa' => false,
        'juros' => false,
        'numero' =>  12345678,
        'numeroDocumento' => 001,
        'pagador' => $pagador,
        'beneficiario' => $beneficiario,
        'carteira' => '2',
        'agencia' => '99999',
        'conta' => '009999999',
        'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
        'instrucoes' =>  ['instrucao 1', 'instrucao 2', 'instrucao 3'],
        'aceite' => 'N',
        'especieDoc' => 'DM',
    ]
);

// If you want to show a print window after rendering pass true on the first argument.
echo $boleto->renderHTML();
//
//$pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();
//$pdf->addBoleto($boleto);
//$pdf->gerarBoleto($pdf::OUTPUT_SAVE, __DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'safra.pdf');
