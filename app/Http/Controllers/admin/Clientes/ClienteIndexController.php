<?php

namespace App\Http\Controllers\admin\Clientes;

use App\Http\Controllers\admin\Traits\TraitEmpresa;
use App\Http\Controllers\admin\Traits\TraitPathRelatorio;
use App\Repositories\Admin\BancoRepository;
use App\Repositories\Admin\ClienteRepository;
use Livewire\Component;
use Livewire\WithPagination;

class ClienteIndexController extends Component
{

    use WithPagination;
    use TraitEmpresa;
    use TraitPathRelatorio;


    public $search = null;
    public $byStatus = null;
    public $perpage = 10;

    private $clienteRepository;

    public function boot(ClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    public function render()
    {
        $clientes = $this->clienteRepository->getClientes($this->byStatus, $this->search, $this->perpage);


        
        return view('admin.clientes.index', [
            'clientes' => $clientes
        ])->layout('layouts.appAdmin');
    }
    public function imprimirUtilizadores()
    {

        // return redirect()->route('admin.home');
        $pathLogoCompany = $this->getPathCompany();
        $pathRelatorio = $this->getPathRelatorio();


        $data =  $this->relatorioRepository->print('utilizadores', [
            'dirSubreportEmpresa' => $pathRelatorio,
            'diretorio' => $pathLogoCompany
        ]);
        return $data;
    }
}
