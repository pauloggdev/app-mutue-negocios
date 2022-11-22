<?php

namespace App\Http\Controllers\empresa\Usuarios;

use App\Repositories\Empresa\BancoRepository;
use App\Repositories\Empresa\RoleRepository;
use App\Repositories\Empresa\UserRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class UsuarioCreateController extends Component
{

    use LivewireAlert;
    use WithFileUploads;


    public $user;
    private $userRepository;
    private $roleRepository;
    


    public function __construct()
    {
        $this->setarValorPadrao();
    }

    public function boot(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function render()
    {

        $data['roles'] = $this->roleRepository->listarPerfis();
        return view('empresa.usuarios.create', $data);
    }
    public function salvarUtilizador()
    {


        $rules = [
            'user.name' => 'required',
            'user.email' => [
                "required", function ($attr, $value, $fail) {
                    $user =  DB::table('users_cliente')
                        ->where('empresa_id', auth()->user()->empresa_id)
                        ->where('email', $value)
                        ->first();

                    if ($user) {
                        $fail("E-mail já cadastrado");
                    }
                }
            ],
            'user.telefone' => "required",
            'user.status_id' => "required",
        ];
        $messages = [
            'user.name.required' => 'Informe o nome',
            'user.email.required' => 'Informe o email',
            'user.telefone.required' => 'Informe o número telefone',
            'user.status_id.required' => 'Informe o status',
        ];

        if(count($this->user['roles']) <= 0){
            $this->confirm('Informe pelo menos uma função', [
                'showConfirmButton' => false,
                'showCancelButton' => false,
                'icon' => 'warning'
            ]);
            return;
        }

        $this->validate($rules, $messages);
        $this->userRepository->createNewUser($this->user);
        // $this->setarValorPadrao();
        $this->confirm('Operação realizada com sucesso', [
            'showConfirmButton' => false,
            'showCancelButton' => false,
            'icon' => 'success'
        ]);
    }

    public function setarValorPadrao()
    {
        $this->user['name'] = NULL;
        $this->user['username'] = NULL;
        $this->user['email'] = NULL;
        $this->user['telefone'] = NULL;
        $this->user['status_id'] = 1;
        $this->user['role_id'] = 2;
        $this->user['roles'] = [];

    }
}
