<?php

namespace App\Http\Controllers\admin\Users;

use App\Http\Controllers\admin\Traits\TraitEmpresa;
use App\Http\Controllers\admin\Traits\TraitPathRelatorio;
use App\Repositories\Admin\UserRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UserIndexController extends Component
{

    use WithPagination;
    use TraitEmpresa;
    use TraitPathRelatorio;


    public $search = null;
    public $byStatus = null;
    public $perpage = 15;

    private $userRepository;
    private $relatorioRepository;
    private $statuRepository;

    public function boot(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function render()
    {
        $users = $this->userRepository->getUsers($this->byStatus, $this->search, $this->perpage);
        return view('admin.usuarios.index', [
            'users' => $users
        ])->layout('layouts.appAdmin');
    }
    // public function imprimirUtilizadores()
    // {

    //     // return redirect()->route('admin.home');
    //     $pathLogoCompany = $this->getPathCompany();
    //     $pathRelatorio = $this->getPathRelatorio();

    //     $data =  $this->relatorioRepository->print('utilizadores', [
    //         'dirSubreportEmpresa' => $pathRelatorio,
    //         'diretorio' => $pathLogoCompany
    //     ]);
    //     return $data;
    // }
}
