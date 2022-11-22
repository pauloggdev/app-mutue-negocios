<?php

namespace App\Http\Controllers\empresa\Produtos;

use App\Models\empresa\Produto;
use App\Models\empresa\TipoMotivoIva;
use App\Models\empresa\TipoTaxa;
use App\Traits\Empresa\TraitEmpresaAutenticada;
use App\Traits\VerificaSeEmpresaTipoAdmin;
use App\Traits\VerificaSeUsuarioAlterouSenha;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProdutoShowController extends Component
{

    use VerificaSeEmpresaTipoAdmin;
    use VerificaSeUsuarioAlterouSenha;
    use TraitEmpresaAutenticada;
    use WithFileUploads;

    public $uuid;


    public function mount($uuid)
    {
        $this->uuid = $uuid;
    }



    public function render()
    {
        $produto = Produto::where('uuid', $this->uuid)
            ->where('empresa_id', auth()->user()->empresa_id)->first();
        if (!$produto) {
            return redirect()->back();
        }

        // dd($produto);

        $infoNotificacao = $this->alertarActivacaoLicenca();
        $data['alertaAtivacaoLicenca'] = $infoNotificacao;

        $empresa = $this->pegarEmpresaAutenticadaGuardOperador();


        $data['guard'] = $empresa['guard'];

        if ( $infoNotificacao['diasRestantes'] === 0) {
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

        $data['produto'] = $produto;
        $data['categorias'] = DB::table('categorias')->where('empresa_id', auth()->user()->empresa_id)
            ->orwhere('empresa_id', NULL)
            ->get();

        $data['armazens'] = DB::table('armazens')->where('empresa_id', auth()->user()->empresa_id)->get();
        $data['status'] = DB::table('status_gerais')->get();
        $data['unidades'] = DB::table('unidade_medidas')->where('empresa_id', auth()->user()->empresa_id)->get();
        $data['fabricantes'] = DB::table('fabricantes')->where('empresa_id', auth()->user()->empresa_id)->get();

        return view('empresa.produtos.edit', $data);
    }
   
}
