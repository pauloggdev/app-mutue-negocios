<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\JobAtivacaoEmpresa;
use App\Jobs\JobCadastroEmpresaNotificacao;
use App\Mail\AtivacaoEmpresa;
use App\Mail\CadastroEmpresaNotificacao;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\admin\Empresa;
use App\Models\contsys\SubConta;
use App\Models\empresa\Empresa_Cliente;
use App\Models\empresa\Pais;
use App\Models\empresa\User as EmpresaUser;
use App\Rules\Empresa\EmpresaUnicaAdmin;
use Illuminate\Support\Facades\Mail;
use Keygen\Keygen;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/empresa/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('admin.empresas.create');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $mensagem = [
            'pessoal_Contacto.required' => 'É obrigatória o contacto',
            'pais_id.required' => 'É obrigatória selecionar o país',
            'tipo_cliente_id.required' => 'É obrigatória selecionar o tipo de empresa',
            'tipo_regime_id.required' => 'É obrigatória selecionar o tipo de regime',
            'file_alvara.mimes' => 'É obrigatória selecionar arquivo no formato pdf',
            'file_nif.mimes' => 'É obrigatória selecionar arquivo no formato pdf'
        ];

        return Validator::make($data, [
            'email' => ['required', 'email', 'max:145', new EmpresaUnicaAdmin('users_admin', 'mysql'), function ($attribute, $value, $fail) {
                $userCliente =  new EmpresaUnicaAdmin('users_cliente', 'mysql2');
                if (!$userCliente->passes($attribute, $value)) {
                    $fail('O ' . $attribute . ' já se encontra registrado');
                }
                $userCliente =  new EmpresaUnicaAdmin('empresas', 'mysql');
                if (!$userCliente->passes($attribute, $value)) {
                    $fail('O ' . $attribute . ' já se encontra registrado');
                }
            }],

            'nome' => ['required', 'string', 'min:3', 'max:255', new EmpresaUnicaAdmin('empresas', 'mysql')],
            'pessoal_Contacto' => ['required', 'string', 'max:9', new EmpresaUnicaAdmin('empresas', 'mysql'), function ($attribute, $value, $fail) {

                $contatoExiste = DB::connection('mysql2')->table('empresas')->where('pessoal_Contacto', $value)->first();
                if ($contatoExiste) {
                    $fail('O contato já cadastrado no sistema');
                }
            }],
            'endereco' => ['required', 'string'],
            'cidade' => ['required'],
            'file_alvara' => ['file', 'mimes:pdf'],
            'file_nif' => ['file', 'mimes:pdf'],
            'tipo_cliente_id' => ['required', 'numeric'],
            'tipo_regime_id' => ['required', 'numeric'],
            'pais_id' => ['required', 'numeric'],
            'nif' => ['required', 'string'],
            'logotipo' => ['file', 'mimes:jpeg,png,jpg', 'max:300'],
        ], $mensagem);
    }

    public function validarEmpresa(Request $request)
    {

        $this->validator($request->all())->validate();

        if (isset($request['logotipo']) && !empty($request['logotipo'])) {
            $photoName = $request['logotipo']->store('/utilizadores/cliente');
        } else {
            $photoName = 'utilizadores/cliente/avatarEmpresa.png';
        }

        if (isset($request['file_alvara']) && !empty($request['file_alvara'])) {
            $alvaraName = $request['file_alvara']->store('/documentos/empresa/documentos');
        } else {
            $alvaraName = NULL;
        }

        if (isset($request['file_nif']) && !empty($request['file_nif'])) {
            $nifName = $request['file_nif']->store('/documentos/empresa/documentos');
        } else {
            $nifName = NULL;
        }

        $token = md5(time() . rand(0, 99999) . rand(0, 99999));

        DB::beginTransaction();

        try {
            DB::connection('mysql')->table('validacao_empresa')->insertGetId([
                'nome' => $request->nome,
                'endereco' => $request->endereco,
                'pais_id' => $request->pais_id,
                'nif' => $request->nif,
                'tipo_cliente_id' => $request->tipo_cliente_id,
                'tipo_regime_id' => $request->tipo_regime_id,
                'logotipo' => $photoName,
                'website' => $request->website,
                'email' => $request->email,
                'cidade' => $request->cidade,
                'pessoal_Contacto' => $request->pessoal_Contacto,
                'file_alvara' => $alvaraName,
                'file_nif' => $nifName,
                'token' => $token,
                'expirado_em' => date('Y-m-d H:i', strtotime('+2 days')),
                'used' => 0,
                'remember_token' => $request->remember_token
            ]);

            $data['email'] = $request->email;
            $data['url'] = env('APP_URL') . 'register?token=' . $token;


            // JobAtivacaoEmpresa::dispatch($data)->delay(now()->addSecond('5'));


            //Mail::send(new AtivacaoEmpresa($data));

            $data['mensagem'] = 'Acessa o email ' . $request->email . ', clica no link para confirmar o cadastro da sua empresa';
            $data['tipoEmpresa'] = DB::table('tipos_clientes')->get();
            $data['tipoRegime'] = DB::table('tipos_regimes')->get();
            $data['paises'] = Pais::all();
            DB::commit();
            return redirect('/register?token=' . $token);
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
        // $data['tipoEmpresa'] = DB::table('tipos_clientes')->get();
        // $data['tipoRegime'] = DB::table('tipos_regimes')->get();
        // $data['paises'] = Pais::all();
        // return view('admin.empresas.create', $data);

    }

    public function removerTokenInvalidos()
    {
        DB::connection('mysql')->table('validacao_empresa')
            ->whereDate('expirado_em', '<=', date('Y-m-d H:i:s'))
            ->delete();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function register(Request $request)
    {



        $this->removerTokenInvalidos();


        $empresaInfo = DB::connection('mysql')->table('validacao_empresa')
            ->where('token', $request->token)->where('used', 0)
            ->whereDate('expirado_em', '>=', date('Y-m-d H:i:s'))
            ->first();

        if (!$empresaInfo) {
            return view('errors.error_tokenExpired');
        }


        $data = $empresaInfo;

        $CARTAO_CREDITO = 1;
        $STATUS_ATIVO = 1;
        $CANAL_PORTAL_CLIENTE = 2;
        $CANAL_PORTAL_ADMIN = 3;
        $TIPO_USER_EMPRESA = 2;
        $SENHA_VULNERAVEL = 1;

        DB::beginTransaction();

        try {



            // $userId = DB::connection('mysql')->table('users_admin')->insertGetId([
            //     'name' => $data['nome'],
            //     'username' => $data['nome'],
            //     'password' => Hash::make('mutue123'),
            //     'tipo_user_id' => $TIPO_USER_EMPRESA,
            //     'status_id' => $STATUS_ATIVO,
            //     'status_senha_id' => $SENHA_VULNERAVEL,
            //     'foto' => $photoName ? $photoName : "utilizadores/cliente/avatarEmpresa.png",
            //     'remember_token' => $data['remember_token'],
            //     'telefone' => $data['pessoal_Contacto'],
            //     'email' => $data['email'],
            //     'canal_id' => $CANAL_PORTAL_ADMIN
            // ]);


            $referencia = mb_strtoupper(Keygen::alphanum(7)->generate());
            //cadastrar empresa admin
            $empresaAdminId = DB::connection('mysql')->table('empresas')->insertGetId([
                'nome' => $data->nome,
                'pessoal_Contacto' => $data->pessoal_Contacto,
                'endereco' => $data->endereco,
                'pais_id' => $data->pais_id,
                'saldo' => 0.00,
                'canal_id' => $CANAL_PORTAL_ADMIN,
                'status_id' => $STATUS_ATIVO,
                'nif' => $data->nif,
                'gestor_cliente_id' => 1,
                'tipo_cliente_id' => $data->tipo_cliente_id,
                'tipo_regime_id' => $data->tipo_regime_id,
                'logotipo' => $data->logotipo,
                'website' => $data->website,
                'email' => $data->email,
                'file_alvara' => $data->file_alvara,
                'file_nif' => $data->file_nif,
                'cidade' => $data->cidade,
                'referencia' => $referencia
            ]);



            //fim cadastro empresa admin

            //cadastrar empresa cliente
            $empresaId = DB::connection('mysql2')->table('empresas')->insertGetId([
                'nome' => $data->nome,
                'pessoal_Contacto' => $data->pessoal_Contacto,
                'endereco' => $data->endereco,
                'pais_id' => $data->pais_id,
                'saldo' => 0.00,
                'referencia' => Empresa::latest()->first()->referencia,
                'canal_id' => $CANAL_PORTAL_ADMIN,
                'status_id' => $STATUS_ATIVO,
                'nif' => $data->nif,
                'gestor_cliente_id' => Empresa::latest()->first()->gestor_cliente_id,
                'tipo_cliente_id' => $data->tipo_cliente_id,
                'tipo_regime_id' => $data->tipo_regime_id,
                'logotipo' => $data->logotipo,
                'website' => $data->website,
                'email' => $data->email,
                'file_alvara' => $data->file_alvara,
                'file_nif' => $data->file_nif,
                'cidade' => $data->cidade,
                'referencia' => $referencia
            ]);



            //cadastrar centro custo
            $alvaraCentro = NULL;
            $nifCentro = NULL;
            $photoNameCentro = NULL;
            if ($data->logotipo) {
                $photoNameCentro = Str::after($data->logotipo, 'utilizadores/cliente/');
                $photoNameCentro = 'utilizadores/empresa/centroCustos/' . $photoNameCentro;
            }

            if ($data->file_alvara) {
                $alvaraCentro = Str::after($data->file_alvara, '/documentos/empresa/documentos/');
                $alvaraCentro = 'documentos/empresa/documentos/centroCustos/' . $alvaraCentro;
            }
            if ($data->file_nif) {
                $nifCentro = Str::after($data->file_nif, '/documentos/empresa/documentos/');
                $nifCentro = 'documentos/empresa/documentos/centroCustos/' . $nifCentro;
            }

            DB::table('centro_custos')->insertGetId([
                'uuid' => Str::uuid(),
                'nome' => $data->nome,
                'empresa_id' => $empresaId,
                'status_id' => 1, //ativo
                'endereco' => $data->endereco,
                'nif' => $data->nif,
                'cidade' => $data->cidade,
                'email' => $data->email,
                'website' => $data->website,
                'telefone' => $data->pessoal_Contacto,
                'file_alvara' => $alvaraCentro,
                'file_nif' => $nifCentro,
                'logotipo' => $photoNameCentro
            ]);


            DB::connection('mysql2')->table('modelo_documento_ativo')->insert([
                'modelo_id' => 2,
                'empresa_id' =>  $empresaId
            ]);


            //fim cadastro empresa cliente

            $userId = DB::connection('mysql2')->table('users_cliente')->insertGetId([
                'name' => $data->nome,
                'username' => $data->nome,
                'password' => Hash::make('mutue123'),
                'tipo_user_id' => $TIPO_USER_EMPRESA,
                'status_id' => $STATUS_ATIVO,
                'status_senha_id' => $SENHA_VULNERAVEL,
                'telefone' => $data->pessoal_Contacto,
                'email' => $data->email,
                'canal_id' => $CANAL_PORTAL_ADMIN,
                'empresa_id' => $empresaId,
                'foto' => $data->logotipo,
                'remember_token' => $data->remember_token,
            ]);

            //Dar função ao utilizador
            DB::connection('mysql2')->table('model_has_roles')->insert([
                'role_id' => 1, //Super admin
                'model_id' => $userId,
                'model_type' => 'App\Models\empresa\User',
            ]);
            // //Adicionar Todas permissões
            // $permissions = DB::connection('mysql2')->table('permissions')->get();
            // foreach ($permissions as $key => $permission) {
            //     DB::connection('mysql2')->table('model_has_permissions')->insert([
            //         'permission_id' => $permission->id,
            //         'model_id' => $userId,
            //         'model_type' => 'App\Models\empresa\User',
            //     ]);
            // }
            //activação de licença da empresa
            $PORTAL_CLIENTE = 2;
            $STATUS_ATIVO = 1;
            $LICENCA_GRATIS = 1;
            // $LICENCA_DEFINITIVA = 4;

            $paramentro = DB::connection('mysql')->table('parametros')->where('id', 1)->first();

            DB::connection('mysql')->table('activacao_licencas')->insertGetId([

                'licenca_id' => $LICENCA_GRATIS,
                'empresa_id' => $empresaAdminId,
                'data_inicio' => Carbon::createFromFormat('Y-m-d', date('Y-m-d')),
                'data_activacao' => Carbon::createFromFormat('Y-m-d', date('Y-m-d')),
                'data_fim' => Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDay(31),
                // 'user_id' =>  $userId,
                'canal_id' => $PORTAL_CLIENTE,
                'status_licenca_id' => $STATUS_ATIVO,
                'observacao' => 'Ativação da licença definitiva',
                'data_notificaticao' => Carbon::createFromFormat('Y-m-d', date('Y-m-d'))

            ]);
            //fim activação de licença da empresa

            //Cadastra armazem principal
            $ARMAZEM_PRINCIPAL = 1;
            DB::connection('mysql2')->table('armazens')->insertGetId([
                'codigo' => mb_strtoupper(Keygen::numeric(9)->generate()),
                'designacao' => "LOJA PRINCIPAL",
                'localizacao' => $data->endereco,
                'status_id' => $STATUS_ATIVO,
                'diversos' => $ARMAZEM_PRINCIPAL,
                'empresa_id' => Empresa_Cliente::latest()->first()->id,
            ]);

            // Cadastra unidade medida default
            $UN_DIVERSOS = 1;
            DB::connection('mysql2')->table('unidade_medidas')->insertGetId([
                'designacao' => "un",
                'empresa_id' => Empresa_Cliente::latest()->first()->id,
                'canal_id' => 2, //canal cliente
                'status_id' => $STATUS_ATIVO,
                'diversos' => $UN_DIVERSOS
            ]);

            $empresa = Empresa_Cliente::where('id', $empresaId)->first();

            //cadastrar empresa contsys
            $empresaContsysId = DB::connection('mysql3')->table('empresas')->insertGetId([
                'Nome' => $empresa->nome,
                'Endereco' => $empresa->endereco,
                'Movel' => $empresa->pessoal_Contacto,
                'website' => $data->website,
                'DataCadastro' => date("Y-m-d"),
                'NIF' => $empresa->nif,
                'referenciaEmpresa' => $empresa->referencia
            ]);
            //fim cadastro empresa contsys

            //cadastro utilizador contsys
            $STATUS_ATIVO = 1;
            $TIPO_USUARIO_ADMIN = 1;

            $utilizadorId = DB::connection('mysql3')->table('utilizadores')->insertGetId([
                'Nome' => $data->nome,
                'Username' => $data->nome,
                'email' => $data->email,
                'Password' => Hash::make('mutue123'),
                'CodStatus' => $STATUS_ATIVO,
                'CodTipoUser' => $TIPO_USUARIO_ADMIN,
                'empresa_id' => $empresaId,
                'UserId' => $userId
            ]);
            //fim cadastro utilizador contsys

            //buscar empresa contsys
            $CONTA_CLIENTE = 16;
            $empresaContsysId = $this->buscarEmpresaContsysId($empresa);
            $contaCorrente = $this->buscarContaCorrente($empresaContsysId, $CONTA_CLIENTE);

            // Cadastra cliente default
            $clienteId = DB::connection('mysql2')->table('clientes')->insertGetId([
                'nome' => "Consumidor final",
                'nif' => "999999999",
                'canal_id' => $CANAL_PORTAL_CLIENTE, //cliente
                'status_id' => $STATUS_ATIVO, //activo
                'diversos' => "Sim",
                'tipo_cliente_id' => $data->tipo_cliente_id,
                'conta_corrente' => $contaCorrente,
                'diversos' => "Sim",
                'endereco' => 'Av. Deolinda Rodrigues, 123',
                'empresa_id' => Empresa_Cliente::latest()->first()->id,
                'pais_id' => $data->pais_id,
                'cidade' => $data->cidade
            ]);

            //criar Subconta contsys
            $CONTA_ATIVO = 3;
            $CONTA_CLIENTE = 16;
            $CODIGO_ADMIN = 1;

            DB::connection('mysql3')->table('subcontas')->insert([
                'Numero' => $contaCorrente,
                'Descricao' => 'CLIENTE DIVERSOS',
                'CodConta' => $CONTA_CLIENTE,
                'CodUtilizador' => $utilizadorId,
                'DataCadastro' => Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')),
                'CodTipoConta' => $CONTA_ATIVO,
                'CodEmpresa' => $empresaContsysId,
                'Movimentar' => "SIM",
                'codigoCliente' => $clienteId
            ]);

            //cadastra fabricante default
            $TIPO_EMPRESA = 2;
            DB::connection('mysql2')->table('fabricantes')->insert([
                'designacao' => "DIVERSOS",
                'empresa_id' => Empresa_Cliente::latest()->first()->id,
                'canal_id' => $CANAL_PORTAL_CLIENTE, //cliente
                'user_id' => $userId, //user
                'status_id' => $STATUS_ATIVO, //activo
                'diversos' => "Sim",
                'tipo_user_id' =>  $TIPO_EMPRESA
            ]);

            $CONTA_FONECEDOR = 17;
            $FORNECEDOR_DIVERSOS = 1;
            $contaCorrenteFornecedor = $this->buscarContaCorrente($empresaContsysId, $CONTA_FONECEDOR);

            //cadastra fornecedor default
            $fornecedorId = DB::connection('mysql2')->table('fornecedores')->insertGetId([
                'nome' => "DIVERSOS",
                'empresa_id' => Empresa_Cliente::latest()->first()->id,
                'canal_id' => $CANAL_PORTAL_CLIENTE, //cliente
                'status_id' => $STATUS_ATIVO, //activo
                'user_id' => $userId, //user
                'diversos' => $FORNECEDOR_DIVERSOS,
                'conta_corrente' => $contaCorrenteFornecedor,
                'pais_nacionalidade_id' => 1, //Angola
                'tipo_user_id' =>  $TIPO_EMPRESA
            ]);
            //cadastra subconta fornecedor contsys
            DB::connection('mysql3')->table('subcontas')->insert([
                'Numero' => $contaCorrenteFornecedor,
                'Descricao' => 'FORNECEDOR DIVERSOS',
                'CodConta' => $CONTA_FONECEDOR,
                'CodUtilizador' => $utilizadorId,
                'DataCadastro' => Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')),
                'CodTipoConta' => $CONTA_ATIVO,
                'CodEmpresa' => $empresaContsysId,
                'Movimentar' => "SIM",
                'codigoFornecedor' => $fornecedorId
            ]);

            //INFO PARA ENVIO DE EMAIL
            $infoEmail['nome'] = $data->nome;
            $infoEmail['email'] = $data->email;
            $infoEmail['senha'] = 'mutue123';
            $infoEmail['telefone'] = $data->pessoal_Contacto;
            $infoEmail['linkLogin'] = getenv('APP_URL');
            $infoEmail['pessoal_Contacto'] = $data->pessoal_Contacto;
            //$infoEmail['empresa_id'] = $empresaId;
            //$infoEmail['data'] = $empresa;

            //FIM ENVIO EMAIL
            //  $empresaAdmin = Empresa::where('id', $empresaAdminId)->first();

            // $empresaAdmin->notify(new CadastroEmpresaNotificacao($infoEmail));

            JobCadastroEmpresaNotificacao::dispatch($infoEmail)->delay(now()->addSecond('5'));


            // Mail::send(new CadastroEmpresaNotificacao($infoEmail));

            $userCliente = EmpresaUser::find($userId);

            event(new Registered($userCliente));

            $this->guard('empresa')->login($userCliente);

            // $this->visualizarFichaCadastro($infoEmail);

            DB::commit();

            DB::connection('mysql')->table('validacao_empresa')
                ->where('token', $data->token)->update([
                    'used' => 1
                ]);

            return $this->registered($request, $userCliente)
                ?: redirect($this->redirectPath());
        } catch (\Exception $th) {
            DB::rollBack();
        }
    }
    protected function guard($guard)
    {
        return Auth::guard($guard);
    }
    public function visualizarFichaCadastro($data)
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml('
        <h4 style="text-align:center">FICHA DE CADASTRAMENTO</h4>
        <strong>Link de acesso: </strong> <a href="">' . env('APP_URL') . 'login</a><br>
        <strong>Email: </strong>' . $data['email'] . '<br>
        <strong>Telefone: </strong> ' . $data['telefone'] . '<br>
        <strong>Senha:</strong> mutue123<br><br>
        <span style="color:red;">OBS:</span><br>
        <span style="color:red;">Caso a palavra passe for diferente de <strong>mutue123</strong> é necessário que o utilizador acesse a tela de login para recuperar a sua senha</span>
        ');
        // (Optional) Setup the paper size and orientation
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('FICHA_CADASTRAMENTO');
    }

    public function cadastrarClienteDiversosContsys($data)
    {
        $subConta = new SubConta();
        $subConta->Numero = $data['Numero'];
        $subConta->Descricao = $data['Descricao'];
        $subConta->CodConta = $data['CodConta'];
        $subConta->CodUtilizador = $data['CodUtilizador'];
        $subConta->DataCadastro = $data['DataCadastro'];
        $subConta->CodTipoConta = $data['CodTipoConta'];
        $subConta->CodEmpresa = $data['CodEmpresa'];
        $subConta->Movimentar = $data['Movimentar'];
        $subConta->codigoCliente = $data['codigoCliente'];
        $subConta->save();

        return response()->json($subConta, 200);
    }

    public function activarLicencaGratis($empresaId, $userId)
    {
        $PORTAL_CLIENTE = 2;
        $STATUS_ATIVO = 1;
        $LICENCA_GRATIS = 1;

        $paramentro = DB::connection('mysql')->table('parametros')->where('id', 1)->first();

        $licencaId = DB::connection('mysql')->table('activacao_licencas')->insertGetId([
            'licenca_id' => $LICENCA_GRATIS,
            'empresa_id' => $empresaId,
            'data_inicio' => Carbon::createFromFormat('Y-m-d', date('Y-m-d')),
            'data_activacao' => Carbon::createFromFormat('Y-m-d', date('Y-m-d')),
            'data_fim' => date('Y-m-d', strtotime("+$paramentro->valor days")),
            'user_id' =>  $userId,
            'canal_id' => $PORTAL_CLIENTE,
            'status_licenca_id' => $STATUS_ATIVO,
            'observacao' => 'Ativação da licença grátis',
            'data_notificaticao' => Carbon::createFromFormat('Y-m-d', date('Y-m-d'))

        ]);
        //return response()->json($licencaId, 200);
    }
    public function cadastrarEmpresaContsys($empresa, $data)
    {
        DB::connection('mysql3')->table('empresas')->insertGetId([
            'Nome' => $empresa->nome,
            'Endereco' => $empresa->endereco,
            'Movel' => $empresa->pessoal_Contacto,
            'website' => $data['website'],
            'DataCadastro' => date("Y-m-d"),
            'NIF' => $empresa->nif,
            'referenciaEmpresa' => $empresa->referencia
        ]);
        //  return $empresaId;
    }

    public function cadastrarUtilizadorContsys($empresa, $data, $userId)
    {

        $STATUS_ATIVO = 1;
        $TIPO_USUARIO_ADMIN = 1;

        DB::connection('mysql3')->table('utilizadores')->insertGetId([
            'Nome' => $data['nome'],
            'Username' => $data['nome'],
            'email' => $data['email'],
            'Password' => Hash::make('mutue123'),
            'CodStatus' => $STATUS_ATIVO,
            'CodTipoUser' => $TIPO_USUARIO_ADMIN,
            'empresa_id' => $empresa->id,
            'UserId' => $userId
        ]);
        //return $utilizadorId;
    }
    public function buscarEmpresaContsysId($empresa)
    {
        return DB::connection('mysql3')->table('empresas')->where('referenciaEmpresa', $empresa->referencia)->first()->Codigo;
    }
    public function buscarContaCorrente($empresaId, $tipoConta)
    {
        $CONTA_CLIENTE = 16;
        $CONTA_FONECEDOR = 17;

        if ($tipoConta == $CONTA_CLIENTE) {
            $TIPO_CONTA = $CONTA_CLIENTE;
        } else if ($tipoConta == $CONTA_FONECEDOR) {
            $TIPO_CONTA = $CONTA_FONECEDOR;
        }

        $resultado =  DB::connection('mysql3')->table('subcontas')->where('CodConta', $TIPO_CONTA)
            ->orderBy('Codigo', 'DESC')->where('CodEmpresa', $empresaId)->limit(1)->first();

        if ($resultado) {
            $array = explode('.', $resultado->Numero);
            $ultimoElemento = (int) end($array);
            array_pop($array);
            $ultimoElemento++;
            array_push($array, (string)$ultimoElemento);
            $contaCorrente = implode(".", $array);
        } else {
            if ($tipoConta == $CONTA_CLIENTE) {
                $contaCorrente = "31.1.2.1.1";
            } else {
                $contaCorrente = "32.1.2.1.1";
            }
        }

        return $contaCorrente;
    }
}
