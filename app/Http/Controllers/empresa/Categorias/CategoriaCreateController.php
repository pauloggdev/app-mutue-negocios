<?php

namespace App\Http\Controllers\empresa\Categorias;

use App\Models\empresa\Pais;
use App\Models\empresa\TiposCliente;
use App\Repositories\Empresa\CategoriaRepository;
use App\Repositories\Empresa\FornecedorRepository;
use App\Repositories\Empresa\MarcaRepository;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CategoriaCreateController extends Component
{
    use LivewireAlert;
    public $categoria;
    private $categoriaRepository;

    public function __construct()
    {
        $this->setarValorPadrao();
    }

    public function boot(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }
    
    public function render()
    {   
          
        //  $data['categorias']= $this->categoriaRepository->getCategoria();
        
        return view('empresa.categorias.create');
       
    } 
    
    public function salvarCategoria()
    {
  
        $rules = [
          
            'categoria.designacao' => 'required',
            
        ];
        $messages = [
            'categoria.designacao.required' => 'Informe o nome da marca',
           
        ];

        $this->validate($rules, $messages);
        $this->categoriaRepository->store($this->categoria);
        $this->confirm('Operação realizada com sucesso', [
            'showConfirmButton' => false,
            'showCancelButton' => false,
            'icon' => 'success'
        ]);
        $this->setarValorPadrao();
    }

    public function setarValorPadrao()
    {
        $this->categoria['designacao'] = NULL;
        $this->categoria['status_id'] = 1;
        $this->categoria['user_id'] = 2;
        $this->categoria['canal_id'] = 2;
      
        
    }



    
}
