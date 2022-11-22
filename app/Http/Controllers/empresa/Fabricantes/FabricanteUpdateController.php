<?php

namespace App\Http\Controllers\empresa\Fabricantes;

use App\Http\Requests\UpdateFabricanteRequest;
use App\Repositories\Empresa\FabricanteRepository;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class FabricanteUpdateController extends Component
{

    use LivewireAlert;
    use UpdateFabricanteRequest;

    public $fabricante;
    private $fabricanteRepository;

    public function boot(FabricanteRepository $fabricanteRepository)
    {
        $this->fabricanteRepository = $fabricanteRepository;
    }

    public function mount($fabricanteId)
    {
        $this->fabricante = $this->fabricanteRepository->getFabricante($fabricanteId);
        if (!$this->fabricante) return redirect()->back();
    }

    public function render()
    {
        return view('empresa.fabricantes.edit');
    }
    public function atualizarFabricante()
    {
        $this->validate($this->rules(), $this->messages());
        $this->fabricanteRepository->update($this->fabricante);
        $this->confirm('Operação realizada com sucesso', [
            'showConfirmButton' => false,
            'showCancelButton' => false,
            'icon' => 'success'
        ]);
    }
}
