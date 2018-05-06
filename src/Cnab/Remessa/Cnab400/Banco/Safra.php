<?php

namespace Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco;

use DeepCopyTest\B;
use Eduardokum\LaravelBoleto\CalculoDV;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\AbstractRemessa;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Remessa as RemessaContract;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Util;

class Safra extends AbstractRemessa implements RemessaContract
{
    const ESPECIE_DUPLICATA = '01';
    const ESPECIE_NOTA_PROMISSORIA = '02';
    const ESPECIE_NOTA_SEGURO = '03';
    const ESPECIE_RECIBO = '05';
    const ESPECIE_DUPLICATA_SERVICOS = '09';

    const OCORRENCIA_REMESSA = '01';
    const OCORRENCIA_PEDIDO_BAIXA = '02';
    const OCORRENCIA_CONCESSAO_ABATIMENTO = '04';
    const OCORRENCIA_CANC_ABATIMENTO_CONCEDIDO = '05';
    const OCORRENCIA_ALT_VENCIMENTO = '06';
    const OCORRENCIA_ALT_CONTROLE_PARTICIPANTE = '07';
    const OCORRENCIA_ALT_SEU_NUMERO = '08';
    const OCORRENCIA_PEDIDO_PROTESTO = '09';
    const OCORRENCIA_SUSTAR_PROTESTO_BAIXAR_TITULO = '18';
    const OCORRENCIA_SUSTAR_PROTESTO_MANTER_TITULO = '19';
    const OCORRENCIA_TRANS_CESSAO_CREDITO_ID10 = '22';
    const OCORRENCIA_TRANS_CARTEIRAS = '23';
    const OCORRENCIA_DEVOLUCAO_TRANS_CARTEIRAS = '24';
    const OCORRENCIA_ALT_OUTROS_DADOS = '31';
    const OCORRENCIA_DESAGENDAMENTO_DEBITO_AUT = '35';
    const OCORRENCIA_ACERTO_RATEIO_CREDITO = '68';
    const OCORRENCIA_CANC_RATEIO_CREDITO = '69';


    const INSTRUCAO_SEM = '00';
    const INSTRUCAO_PROTESTAR_FAMILIAR_XX = '05';
    const INSTRUCAO_PROTESTAR_XX = '06';
    const INSTRUCAO_NAO_COBRAR_JUROS = '08';
    const INSTRUCAO_NAO_RECEBER_APOS_VENC = '09';
    const INSTRUCAO_MULTA_10_APOS_VENC_4 = '10';
    const INSTRUCAO_NAO_RECEBER_APOS_VENC_8 = '11';
    const INSTRUCAO_COBRAR_ENCAR_APOS_5 = '12';
    const INSTRUCAO_COBRAR_ENCAR_APOS_10 = '13';
    const INSTRUCAO_COBRAR_ENCAR_APOS_15 = '14';
    const INSTRUCAO_CENCEDER_DESC_APOS_VENC = '15';
    const INSTRUCAO_DEVOLVER_XX = '18';

    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->addCampoObrigatorio('idremessa');
    }


    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SAFRA;

    /**
     * Define as carteiras disponíveis para cada banco
     *
     * @var array
     */

    protected $carteiras = ['1', '2'];

    /**
     * Caracter de fim de linha
     *
     * @var string
     */
    protected $fimLinha = "\r\n";

    /**
     * Caracter de fim de arquivo
     *
     * @var null
     */
    protected $fimArquivo = "\r\n";

    /**
     * Retorna o codigo do cliente.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCodigoCliente()
    {
        if (empty($this->codigoCliente)) {
            $this->codigoCliente =
                Util::formatCnab('9', $this->getAgencia(), 5) .
                Util::formatCnab('9', $this->getConta(), 9);
        }

        return $this->codigoCliente;
    }

    /**
     * Seta o codigo do cliente.
     * @param $codigoCliente
     * @return $this
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function header()
    {
        $this->iniciaHeader();

        $this->add(1, 1, '0');
        $this->add(2, 2, '1');
        $this->add(3, 9, 'REMESSA');
        $this->add(10, 11, '01');
        $this->add(12, 19, Util::formatCnab('X', 'COBRANCA', 8));
        $this->add(20, 26, '');
        $this->add(27, 40, Util::formatCnab('9', $this->getCodigoCliente(), 14));
        $this->add(41, 46, '');
        $this->add(47, 76, Util::formatCnab('X', $this->getBeneficiario()->getNome(), 30));
        $this->add(77, 79, $this->getCodigoBanco());
        $this->add(80, 90, Util::formatCnab('X', 'BANCO SAFRA', 11));
        $this->add(91, 94, '');
        $this->add(95, 100, $this->getDataRemessa('dmy'));
        $this->add(101, 391, '');
        $this->add(392, 394, Util::formatCnab('9', $this->getIdremessa(), 3));
        $this->add(395, 400, Util::formatCnab('9', 1, 6));

        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return $this
     * @throws \Exception
     */
    public function addBoleto(BoletoContract $boleto)
    {
        $this->boletos[] = $boleto;
        $this->iniciaDetalhe();

        $this->add(1, 1, '1');
        $this->add(2, 3, '02');
        $this->add(4, 17, Util::onlyNumbers($boleto->getBeneficiario()->getDocumento()));
        $this->add(18, 31, Util::formatCnab('9', $this->getCodigoCliente(), 14));
        $this->add(32, 37, '');
        $this->add(38, 62, '');
        $this->add(63, 71, Util::formatCnab('9', $boleto->getNossoNumero(), 9));
        $this->add(72, 101, '');
        $this->add(102, 102, '0');
        $this->add(103, 104, '00');
        $this->add(105, 105, '');
        $this->add(106, 107, self::INSTRUCAO_SEM);
        $this->add(108, 108, $this->getCarteira());
        $this->add(109, 110, self::OCORRENCIA_REMESSA);
        $this->add(111, 120, Util::formatCnab('X', $boleto->getNossoNumero(), 10));
        $this->add(121, 126, $boleto->getDataVencimento()->format('dmy'));
        $this->add(127, 139, Util::formatCnab('9', $boleto->getValor(), 11, 2));
        $this->add(140, 142, Util::formatCnab('9', \Eduardokum\LaravelBoleto\Boleto\Banco\Safra::COD_BANCO_SAFRA, 3));
        $this->add(143, 147, Util::formatCnab('9', $this->getAgencia(), 5));
        $this->add(148, 149, self::ESPECIE_DUPLICATA);
        $this->add(150, 150, 'N');
        $this->add(151, 156, $boleto->getDataDocumento()->format('dmy'));
        $this->add(157, 158, self::INSTRUCAO_SEM);
        $this->add(159, 160, self::INSTRUCAO_SEM);
        $this->add(161, 173, '');
        $this->add(174, 179, '');
        $this->add(180, 192, '');
        $this->add(193, 205, '');
        $this->add(206, 218, '');
        $this->add(219, 220, '01');
        $this->add(221, 234, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getDocumento()), 14));
        $this->add(235, 274, Util::formatCnab('X', $boleto->getPagador()->getNome(), 40));
        $this->add(275, 314, Util::formatCnab('X', $boleto->getPagador()->getEndereco(), 40));
        $this->add(315, 324, Util::formatCnab('X', $boleto->getPagador()->getBairro(), 10));
        $this->add(325, 326, '');
        $this->add(327, 334, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getCep()), 8));
        $this->add(335, 349, Util::formatCnab('X', $boleto->getPagador()->getCidade(), 15));
        $this->add(350, 351, Util::formatCnab('X', $boleto->getPagador()->getUf(), 2));
        $this->add(352, 381, '');
        $this->add(382, 388, '');
        $this->add(389, 391, '');
        $this->add(392, 394, Util::formatCnab('9', $this->getIdremessa(), 3));
        $this->add(395, 400, Util::formatCnab('9', $this->iRegistros + 1, 6));

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function trailer()
    {
        $this->iniciaTrailer();

        $this->add(1, 1, '9');
        $this->add(2, 368, '');
        $this->add(369, 376, '');
        $this->add(377, 391, '');
        $this->add(392, 394, Util::formatCnab('9', $this->getIdremessa(), 3));
        $this->add(395, 400, Util::formatCnab('9', $this->getCount(), 6));

        return $this;
    }
}
