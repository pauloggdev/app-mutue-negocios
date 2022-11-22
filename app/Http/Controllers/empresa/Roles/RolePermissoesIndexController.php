<?php

namespace App\Http\Controllers\empresa\Roles;

use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\Empresa\PermissaoRepository;
use App\Repositories\Empresa\RoleRepository;
use App\Traits\Empresa\TraitEmpresaAutenticada;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class RolePermissoesIndexController extends Component
{

    use TraitEmpresaAutenticada;
    use LivewireAlert;


    public $search;
    public $role;
    public $roleId;
    private $roleRepository;
    private $permissaoRepository;
    public $selectedPermissoes = [];



    public function mount($uuid)
    {
        $perfil = $this->roleRepository->getPerfil($uuid);
        if (!$perfil) {
            return redirect()->route('roles.index');
        }
        $this->role = $perfil;
    }

    public function boot(RoleRepository $roleRepository, PermissaoRepository $permissaoRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissaoRepository = $permissaoRepository;
    }

    public function render()
    {
        $infoNotificacao = $this->alertarActivacaoLicenca();
        $data['alertaAtivacaoLicenca'] = $infoNotificacao;

        $data['permissoes'] = $this->permissaoRepository->getPermissoes($this->search);
        $this->selectedPermissoes = [];
        $rolePermissoes = $this->permissaoRepository->getPermissoesPorRole($this->role->id);
        foreach ($rolePermissoes as $perfil) {
            array_push($this->selectedPermissoes, $perfil['permission_id']);
        }


        if ($infoNotificacao['diasRestantes'] === 0) {
            return redirect("empresa/home");
        }
        return view('empresa.roles.permissoes', $data);
    }
    public function checkPermissaoPorRole($permissaoId)
    {
        $this->permissaoRepository->checkPermissaoPorRole($permissaoId, $this->role->id);
        $this->confirm('Operação realizada com sucesso', [
            'showConfirmButton' => false,
            'showCancelButton' => false,
            'icon' => 'success',
        ]);
    }
    // public function atualizarPerfil()
    // {
    //     $this->validate($this->rules(), $this->messages());
    //     $this->roleRepository->update($this->role);

    //     $this->confirm('Operação Realizada com Sucesso', [
    //         'showConfirmButton' => false,
    //         'showCancelButton' => false,
    //         'icon' => 'success',
    //     ]);
    // }
}
