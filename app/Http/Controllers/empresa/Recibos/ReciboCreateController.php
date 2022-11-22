<?php

namespace App\Http\Controllers\empresa\Recibos;

use App\Http\Controllers\empresa\ReportShowController;
use App\Repositories\Empresa\ClienteRepository;
use App\Repositories\Empresa\FacturaRepository;
use App\Repositories\Empresa\FormaPagamentoRepository;
use App\Repositories\Empresa\ReciboRepository;
use App\Traits\Empresa\TraitEmpresaAutenticada;
use App\Traits\VerificaSeEmpresaTipoAdmin;
use App\Traits\VerificaSeUsuarioAlterouSenha;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use NumberFormatter;
use PhpParser\Node\Expr\Cast\Double;

class ReciboCreateController extends Component
{

    use VerificaSeEmpresaTipoAdmin;
    use VerificaSeUsuarioAlterouSenha;
    use TraitEmpresaAutenticada;
    use WithFileUploads;
    use LivewireAlert;

    public $recibo;
    public $clienteId;
    public $facturaId;
    public $facturas = [];

    private $reciboRepository;
    private $clienteRepository;
    private $formaPagamentoRepository;
    private $facturaRepository;

    public function boot(
        ReciboRepository $reciboRepository,
        ClienteRepository $clienteRepository,
        FacturaRepository $facturaRepository,
        FormaPagamentoRepository $formaPagamentoRepository
    ) {
        $this->reciboRepository = $reciboRepository;
        $this->clienteRepository = $clienteRepository;
        $this->formaPagamentoRepository = $formaPagamentoRepository;
        $this->facturaRepository = $facturaRepository;
    }
    public function mount()
    {
        $this->setarValorPadrao();
    }
    public function render()
    {
        $infoNotificacao = $this->alertarActivacaoLicenca();
        $data['alertaAtivacaoLicenca'] = $infoNotificacao;

        if ($infoNotificacao['diasRestantes'] === 0) {
            return redirect("empresa/home");
        }
        if ($this->isAdmin()) {
            return view('admin.dashboard');
        }

        $data['recibos'] = $this->reciboRepository->listarRecibos();
        $data['clientes'] = $this->clienteRepository->listarClienteComFacturasApagar();
        $data['formaPagamentos'] = $this->formaPagamentoRepository->listarFormaPagamentos();
        $data['facturas'] = $this->facturas;
        return view('empresa.recibos.create', $data);
    }
    public function updatedClienteId($cliente)
    {


        if ($cliente) {
            $cliente = json_decode($cliente);
            $this->facturas = $this->facturaRepository->listarFacturasParaEmitirReciboPeloIdCliente($cliente->id);
            $this->recibo['cliente_saldo'] = $this->clienteRepository->mostrarSaldoAtualDoCliente($cliente->id);
            $this->recibo['cliente_nome'] = $cliente->nome;
            $this->recibo['cliente_id'] = $cliente->id;
            $this->recibo['cliente_conta_corrente'] = $cliente->conta_corrente;
            $this->recibo['telefone_cliente'] = $cliente->telefone_cliente;
            $this->recibo['nif_cliente'] = $cliente->nif;
            $this->recibo['email_cliente'] = $cliente->email;
            $this->recibo['endereco'] = $cliente->endereco;
            $this->recibo['valor_a_pagar'] = NULL;
            $this->recibo['faltante'] = NULL;
            $this->recibo['total_debito'] = NULL;
            $this->recibo['factura_id'] = NULL;
        } else {
            $this->recibo['cliente_saldo'] = NULL;
            $this->recibo['cliente_nome'] = NULL;
            $this->recibo['cliente_id'] = NULL;
            $this->recibo['cliente_conta_corrente'] = NULL;
            $this->recibo['telefone_cliente'] = NULL;
            $this->recibo['nif_cliente'] = NULL;
            $this->recibo['email_cliente'] = NULL;
            $this->recibo['endereco'] = NULL;
        }
    }
    public function updatedFacturaId($factura)
    {
        if ($factura) {
            $factura = json_decode($factura);
            $this->recibo['valor_a_pagar'] = number_format($factura->valor_a_pagar, 2, ',', '.');
            $this->recibo['factura_id'] = $factura->id;
            $this->recibo['numeracaoFactura'] = $factura->numeracaoFactura;
            $this->recibo['total_debito'] = $this->clienteRepository->mostrarValorFaltanteApagarNaFaturaDoCliente($factura);


            $valorPagar = str_replace(".", "", $this->recibo['valor_a_pagar']);
            $totalDebito =  str_replace(".", "", $this->recibo['total_debito']);
            $valorPagar = str_replace(",", ".", $valorPagar);
            $totalDebito =  str_replace(",", ".", $totalDebito);


            // $totalDebito = str_replace(",00", "", $this->recibo['total_debito']);
            // $totalDebito = (float) str_replace(".", "", $totalDebito);
            // $valorPagar = (float) $factura->valor_a_pagar;
            $faltante = $valorPagar - $totalDebito;
            $this->recibo['faltante'] = number_format($faltante, 2, ',', '.');
        } else {
            $this->recibo['valor_a_pagar'] = NULL;
            $this->recibo['total_debito'] = NULL;
            $this->recibo['faltante'] = NULL;
            $this->recibo['factura_id'] = NULL;
        }
    }



    public function emitirRecibo()
    {

        $rules = [
            'recibo.factura_id' => 'required',
            'recibo.cliente_id' => 'required',
            'recibo.valor_total_entregue' => ["required", function ($attr, $valorEntregue, $fail) {
                $valorPagar = str_replace(".", "", $this->recibo['valor_a_pagar']);
                $totalDebito =  str_replace(".", "", $this->recibo['total_debito']);
                $valorPagar = str_replace(",", ".", $valorPagar);
                $totalDebito =  str_replace(",", ".", $totalDebito);

                $total = $valorPagar - $totalDebito;
                $total = round($total, 2);

                if ($valorEntregue > $total) {
                    $fail('Valor entregue maior que a debitar');
                } else if ($valorEntregue <= 0) {
                    $fail('Informe o valor entregue');
                }
            }],
            'recibo.forma_pagamento_id' => 'required',
        ];
        $messages = [
            'recibo.factura_id.required' => 'Informe a factura',
            'recibo.cliente_id.required' => 'Informe o cliente',
            'recibo.valor_total_entregue.required' => 'Informe o valor entregue',
            'recibo.forma_pagamento_id.required' => 'Informe a forma de pagamento',
        ];

        $this->validate($rules, $messages);

        $f = new NumberFormatter("pt", NumberFormatter::SPELLOUT);
        $this->recibo['valor_extenso'] = $f->format($this->recibo['valor_total_entregue']);

        //Faltando verificar se já foi emitido documento com datas anterior
        $recibo = $this->reciboRepository->salvarRecibo($this->recibo);
        $logotipo = public_path() . '/upload//' . auth()->user()->empresa->logotipo;

        $this->confirm('Operação Realizada com Sucesso', [
            'showConfirmButton' => 'OK',
            'showCancelButton' => false,
            'icon' => 'info',

        ]);
        $this->setarValorPadrao();

        $reportController = new ReportShowController();
        $report = $reportController->show(
            [
                'report_file' => 'recibos',
                'report_jrxml' => 'recibos.jrxml',
                'report_parameters' => [
                    'viaImpressao' => 1,
                    'empresa_id' => auth()->user()->empresa_id,
                    'recibo_id' => $recibo->id,
                    'factura_id' => $recibo->factura_id,
                    'logotipo' => $logotipo
                ]

            ]
        );
        $this->dispatchBrowserEvent('printPdf', ['data' => base64_encode($report['response']->getContent())]);
        unlink($report['filename']);
        flush();
    }
    public function setarValorPadrao()
    {
        $this->recibo['forma_pagamento_id'] = 1;
        $this->recibo['factura_id'] = NULL;
        $this->recibo['cliente_id'] = NULL;
        $this->clienteId = NULL;
        $this->facturaId = NULL;
        $this->recibo['observacao'] = NULL;
        $this->recibo['valor_a_pagar'] = NULL;
        $this->recibo['faltante'] = NULL;
        $this->recibo['total_debito'] = NULL;
        $this->recibo['valor_total_entregue'] = NULL;
        $this->recibo['cliente_saldo'] = 0;
        $this->recibo['cliente_conta_corrente'] = NULL;
        $this->recibo['cliente_nome'] = NULL;
    }
}
