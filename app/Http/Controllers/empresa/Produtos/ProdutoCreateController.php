<?php

namespace App\Http\Controllers\empresa\Produtos;

use App\Models\empresa\TipoMotivoIva;
use App\Models\empresa\TipoTaxa;
use App\Traits\Empresa\TraitEmpresaAutenticada;
use App\Traits\VerificaSeEmpresaTipoAdmin;
use App\Traits\VerificaSeUsuarioAlterouSenha;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProdutoCreateController extends Component
{

    use VerificaSeEmpresaTipoAdmin;
    use VerificaSeUsuarioAlterouSenha;
    use TraitEmpresaAutenticada;
    use WithFileUploads;

    public $preco_venda;
    public $preco_compra;
    public $margemLucro;
    public $codigo_taxa = 1;
    public $motivo_isencao_id;
    public $stocavel = 'Sim';
    public $quantidade;
    public $imagem_produto;
    public $mensagem = "teste";


    public function render()
    {

        $infoNotificacao = $this->alertarActivacaoLicenca();
        $data['alertaAtivacaoLicenca'] = $infoNotificacao;

        $empresa = $this->pegarEmpresaAutenticadaGuardOperador();


        $data['guard'] = $empresa['guard'];

        if ($this->verificarSeAlterouSenha() || $infoNotificacao['diasRestantes'] === 0) {
            return redirect("empresa/home");
        }
        if ($this->isAdmin()) {
            return view('admin.dashboard');
        }

        $REGIME_SIMPLIFICADO = 2;
        $REGIME_EXCLUSAO = 3;
        $REGIME_GERAL = 1;


        if ($empresa['empresa']['tiporegime']['id'] ==  $REGIME_SIMPLIFICADO) {

            $data['taxas'] = TipoTaxa::where('empresa_id', null)
                ->where('codigo', 1)
                ->get();

            $data['motivos'] = TipoMotivoIva::where('empresa_id', null)
                ->where('codigo', 9)
                ->orwhere('codigo', 8)
                ->get();
        }

        if ($empresa['empresa']['tiporegime']['id'] == $REGIME_EXCLUSAO) {
            $data['taxas'] = TipoTaxa::where('empresa_id', null)
                ->where('codigo', 1)->get();

            $data['motivos'] = TipoMotivoIva::where('codigo', 7)
                ->where('empresa_id', null)
                ->orwhere('codigo', 8)
                ->get();
        }

        if ($empresa['empresa']['tiporegime']['id'] ==  $REGIME_GERAL) {
            $data['taxas'] = TipoTaxa::where('empresa_id', null)
                ->get();


            $data['motivos'] = TipoMotivoIva::where('empresa_id', null)
                ->where('codigo', '!=', 7)
                ->where('codigo', '!=', 9)
                ->get();
        }

        if ($this->codigo_taxa > 1) {
            $this->motivo_isencao_id = NULL;
            $data['motivos'] = [];
        }

        if ($this->stocavel == 'Não') {
            $this->quantidade = 0;
        }


        $data['categorias'] = DB::table('categorias')->where('empresa_id', auth()->user()->empresa_id)
            ->orwhere('empresa_id', NULL)->orderBy('id', 'asc')
            ->get();

        $data['armazens'] = DB::table('armazens')->where('empresa_id', auth()->user()->empresa_id)->orderBy('id', 'asc')
            ->get();
        $data['status'] = DB::table('status_gerais')->orderBy('id', 'asc')
            ->get();
        $data['unidades'] = DB::table('unidade_medidas')->where('empresa_id', auth()->user()->empresa_id)->orderBy('id', 'asc')
            ->get();
        $data['fabricantes'] = DB::table('fabricantes')->where('empresa_id', auth()->user()->empresa_id)->orderBy('id', 'asc')
            ->get();

        return view('empresa.produtos.create', $data);
    }

    public function calcularVenda()
    {
        if ($this->preco_venda) {
            $this->margemLucro =  0;
        }
    }
    public function calcularMargemLucro()
    {
        $preco_compra = (int) $this->preco_compra ?? 0;
        $margemLucro = (int) $this->margemLucro ?? 0;

        if ($preco_compra > 0 && $margemLucro > 0) {
            $this->preco_venda = $preco_compra + (($preco_compra * $margemLucro) / 100);
        }
    }
}
