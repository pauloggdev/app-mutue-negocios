<?php

namespace App\Http\Controllers\admin\PedidosLicenca;

use App\Http\Controllers\admin\Traits\TraitEmpresa;
use App\Http\Controllers\admin\Traits\TraitPathRelatorio;
use App\Models\admin\Licenca;
use App\Repositories\Admin\contracts\PedidoLicencaRepositoryInterface;
use App\Repositories\Admin\PedidosLicencaRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Livewire\Component;


class PedidoLicencaAtivarIndexController extends Component
{

    use TraitEmpresa;
    use TraitPathRelatorio;

    public $pedidoId;


    private $pedidosLicencaRepository;

    public function mount($pedidoId)
    {
        $this->pedidoId = $pedidoId;
    }


    public function boot(PedidosLicencaRepository $pedidosLicencaRepository)
    {
        $this->pedidosLicencaRepository = $pedidosLicencaRepository;
    }

    public function render()
    {
        $pedido = $this->pedidosLicencaRepository->getPedidosLicenca($this->pedidoId);
        return view('admin.pedidosLicenca.ativar', compact('pedido'))->layout('layouts.appAdmin');
    }


    public function ativarPedidoLicenca($pedidoLicencaId)
    {


        $ativacaoLicenca = $this->pedidoLicencaRepository->getPedidosLicenca($pedidoLicencaId);
        $ultimaDataLicencaAtiva = $this->pedidoLicencaRepository->pegarUltimaDataLicencaDaEmpresa($ativacaoLicenca->empresa_id);

        if ($ativacaoLicenca->licenca_id == Licenca::MENSAL) {
            $data_inicio = $ultimaDataLicencaAtiva;
            $dataInicio = clone $data_inicio;
            $data_fim = $dataInicio->addDays(31);
            $observacao = "ativo a licença mensal no dia " . Carbon::now();
        } else if ($ativacaoLicenca->licenca_id == Licenca::ANUAL) {
            $data_inicio = $ultimaDataLicencaAtiva;
            $dataInicio = clone $data_inicio;
            $data_fim = $dataInicio->addDays(365);
            $observacao = "ativo a licença anual no dia " . Carbon::now();
        } else if ($ativacaoLicenca->licenca_id == Licenca::DEFINITIVO) {
            $data_inicio = $ultimaDataLicencaAtiva;
            $dataInicio = clone $data_inicio;
            $data_fim = NULL;
            $observacao = "ativo a licença definitiva no dia " . Carbon::now();
        }

        try {

            DB::beginTransaction();
            $ativacaoLicenca->status_licenca_id = StatuLicencas::ATIVO;
            $ativacaoLicenca->user_id = 1;
            $ativacaoLicenca->data_inicio = $data_inicio;
            $ativacaoLicenca->data_fim = $data_fim;
            $ativacaoLicenca->data_rejeicao = NULL;
            $ativacaoLicenca->data_activacao = Carbon::now();
            $ativacaoLicenca->observacao = $observacao;
            $ativacaoLicenca->save();

            $pagamento = $this->pagamentoRepository->getPagamento($ativacaoLicenca->pagamento_id, $ativacaoLicenca->empresa_id);
            $this->pagamentoRepository->alterarStatuPagamentoAtivo($ativacaoLicenca->pagamento_id, $ativacaoLicenca->empresa_id, $data_inicio);
            $this->facturaRepository->alterarStatuFacturaParaPago($pagamento->referenciaFactura, $ativacaoLicenca->empresa_id);

            //preparar aqui os dados para envio de email
            DB::commit();
            return response()->json($ativacaoLicenca, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
