<?php

namespace App\Http\Controllers\empresa\Fornecedores;

use App\Http\Requests\StoreUpdateClienteRequest;
use App\Http\Requests\StoreUpdateFornecedorRequest;
use App\Models\empresa\Pais;
use App\Models\empresa\TiposCliente;
use App\Repositories\Empresa\ClienteRepository;
use App\Repositories\Empresa\FornecedorRepository;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class FornecedorUpdateController extends Component
{
    use LivewireAlert;
    use StoreUpdateFornecedorRequest;

    public $fornecedor;
    private $fornecedorRepository;

    public function boot(FornecedorRepository $fornecedorRepository)
    {
        $this->fornecedorRepository = $fornecedorRepository;
        $this->setarValorPadrao();
    }

    public function mount($fornecedorId)
    {

        $this->fornecedor = $this->fornecedorRepository->getFornecedor($fornecedorId);

        $newDate = date("Y-m-d", strtotime($this->fornecedor['created_at']));
        $this->fornecedor['data_contrato'] = $newDate;
      
    }


    public function render()
    {

        $data['paises'] = Pais::all();

        return view('empresa.fornecedores.edit', $data);
    }
    public function Fornecedorupdate()
    {

        $this->validate($this->rules(), $this->messages());
        $this->fornecedorRepository->update($this->fornecedor);
        $this->confirm('Operação realizada com sucesso', [
            'showConfirmButton' => false,
            'showCancelButton' => false,
            'icon' => 'success'
        ]);
    }

    public function setarValorPadrao()
    {
        $this->fornecedor['nome'] = NULL;
        $this->fornecedor['email'] = NULL;
        $this->fornecedor['telefone_cliente'] = NULL;
        $this->fornecedor['telefone_cliente'] = NULL;
        $this->fornecedor['website'] = NULL;
        $this->fornecedor['endereco'] = NULL;
        $this->fornecedor['cidade'] = NULL;
        $this->fornecedor['pais_nacionalidade_id'] = 1;
        $this->fornecedor['nif'] = NULL;
        $this->fornecedor['pessoal_contacto'] = NULL;
        $this->fornecedor['status_id'] = 1;
        $this->fornecedor['tipo_user_id'] = 2;
        $this->fornecedor['canal_id'] = 2;
        $this->fornecedor['tipo_conta_corrente'] = 'Nacional';
        $this->fornecedor['data_contrato'] = Carbon::now()->format('Y-m-d');
    }
}
