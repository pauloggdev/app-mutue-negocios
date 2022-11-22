<?php

namespace App\Http\Controllers\empresa\Categorias;

use App\Http\Requests\StoreUpdateCategoriaRequest;
use App\Http\Requests\StoreUpdateClienteRequest;
use App\Http\Requests\StoreUpdateMarcaRequest;
use App\Models\empresa\Pais;
use App\Models\empresa\TiposCliente;
use App\Repositories\Empresa\CategoriaRepository;
use App\Repositories\Empresa\FornecedorRepository;
use App\Repositories\Empresa\MarcaRepository;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CategoriaUpdateController extends Component
{
    use LivewireAlert;
    use StoreUpdateCategoriaRequest;
    public $categoria;
    public $search = null;

    private $categoriaRepository;


    public function boot(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
        
    }

    public function mount($categoriaId)
    {    
       
        $this->categoria = $this->categoriaRepository->getCategoria($categoriaId);
        $this->setarValorPadrao();
    }


    public function render()
    {

        return view('empresa.categorias.edit');
    }
    public function CategoriaUpdate()
    {

        $this->validate($this->rules(), $this->messages());
        $this->categoriaRepository->update($this->categoria);
        $this->alert('success', 'Operação realizada com sucesso');
    }

    public function setarValorPadrao()
    {
        // $this->categoria['designacao'] = NULL;
        // $this->categoria['status_id'] = 1;
         $this->categoria['tipo_user_id'] = 2;
         $this->categoria['canal_id'] = 2;
    }
}
