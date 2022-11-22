<?php

namespace App\Repositories\Empresa;

use App\Models\empresa\Cliente;
use App\Models\empresa\ExistenciaStock;
use App\Models\empresa\Produto;
use App\Repositories\Empresa\contracts\ClienteRepositoryInterface;
use App\Repositories\Empresa\contracts\ProdutoRepositoryInterface;
use App\Rules\Empresa\EmpresaUnique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;



class ProdutoRepository implements ProdutoRepositoryInterface
{

    protected $entity;

    public function __construct(Produto $produto)
    {
        $this->entity = $produto;
    }

    public function getProdutos()
    {
        $produtos = $this->entity::with(['tipoTaxa', 'statuGeral', 'unidade', 'empresa', 'unidadeMedida', 'existenciaEstock', 'marca', 'categoria', 'classe', 'fabricante', 'user', 'canal', 'status', 'motivoIsencao'])
            ->where('empresa_id', auth()->user()->empresa_id)->get();
        return $produtos;
    }
    public function listarProdutosPeloIdArmazem($armazemId)
    {

        $produtos = $this->entity::with(['tipoTaxa', 'statuGeral', 'unidade', 'empresa', 'unidadeMedida', 'existenciaEstock', 'marca', 'categoria', 'classe', 'fabricante', 'user', 'canal', 'status', 'motivoIsencao'])
            ->whereHas("existenciaEstock", function ($q) use ($armazemId) {
                $q->where('armazem_id', $armazemId);
            })
            ->where('empresa_id', auth()->user()->empresa_id)->get();
        return $produtos;
    }

    public function getProduto(int $id)
    {
        $produto = $this->entity::where('empresa_id', auth()->user()->empresa_id)
            ->where('id', $id)
            ->first();
        return $produto;
    }
    public function store(Request $request)
    {

        dd($request->all());
        $isApi = $request->segment(1) === 'api' ? true : false;
        $message = [
            'nif.required' => 'É obrigatório o nif',
            'status_id.required' => 'É obrigatório o nif',
            'nif.unique' => 'nif já cadastrado',
            'nome.required' => 'É obrigatório a indicação de um valor para o campo designação',
            'telefone_cliente.required' => 'É obrigatório a indicação de um valor para o campo telefone',
            'cidade.required' => 'É obrigatório a indicação de um valor para o campo cidade',
            'endereco.required' => 'É obrigatório a indicação de um valor para o campo endereço',
            'pais_id.required' => 'É obrigatório a indicação de um valor para o campo nacionalidade',
            'tipo_cliente_id.required' => 'É obrigatório a indicação de um valor para o campo tipo cliente'
        ];

        $validator = Validator::make($request->all(), [
            'nome' => ['required'],
            'telefone_cliente' => ['required'],
            'nif' => [
                'required', function ($attribute, $nif, $fail) {
                    $cliente = DB::connection('mysql2')->table('clientes')
                        ->where('empresa_id', auth()->user()->empresa_id)
                        ->where('nif', $nif)->where('nif', '!=', '999999999')->first();
                    if ($cliente)
                        $fail('O ' . $attribute . ' já cadastrado');
                }
            ],
            'cidade' => ['required'],
            'endereco' => ['required'],
            'pais_id' => ['required'],
            'tipo_cliente_id' => ['required']

        ], $message);

        if ($validator->fails()) {
            if ($isApi) {
                return response()->json($validator->errors()->messages(), 400);
            }
            return redirect()->back()->withErrors($validator)
                ->withInput();
        }

        $clienteId = DB::connection('mysql2')->table('clientes')
            ->insertGetId([

                'nome' => $request->nome,
                'uuid' =>(string) Str::uuid(),
                'pessoa_contacto' => $request->pessoa_contacto,
                'email' => $request->email,
                'website' => $request->website,
                'numero_contrato' => $request->numero_contrato,
                'data_contrato' => $request->data_contrato ?? NULL,
                'telefone_cliente' => $request->telefone_cliente,
                'taxa_de_desconto' => $request->taxa_de_desconto,
                'limite_de_credito' => $request->limite_de_credito,
                'endereco' => $request->endereco,
                'gestor_id' => $request->gestor_id ?? NULL,
                'canal_id' => $request->canal_id ? $request->canal_id : 2,
                'status_id' => $request->status_id,
                'nif' => $request->nif ? $request->nif : "999999999",
                'operador' => auth()->user()->name,
                'tipo_cliente_id' => $request->tipo_cliente_id,
                'user_id' => auth()->user()->id,
                'empresa_id' => auth()->user()->empresa_id,
                'pais_id' => $request->pais_id,
                'cidade' => $request->cidade,
                'tipo_conta_corrente' => $request->tipo_conta_corrente,
                'conta_corrente' => $this->contaCorrente()
            ]);
        if ($isApi) {
            return response()->json($clienteId, 200);
        } else {
            Session::flash('message', 'Operação realizada com sucesso!');
            Session::flash('alert-class', 'alert-success');

            return redirect()->back();
        }
    }
    private function contaCorrente()
    {
        $resultado =  DB::connection('mysql2')->table('clientes')->orderBy('id', 'DESC')
            ->where('empresa_id', auth()->user()->empresa_id)->limit(1)->first();

        if ($resultado) {
            $array = explode('.', $resultado->conta_corrente);
            $ultimoElemento = (int) end($array);
            array_pop($array);
            $ultimoElemento++;
            array_push($array, (string) $ultimoElemento);
            $contaCorrente = implode(".", $array);
        } else {
            $contaCorrente = "31.1.2.1.1";
        }

        return $contaCorrente;
    }
    public function update(Request $request, int $clienteId)
    {

        $isApi = $request->segment(1) === 'api' ? true : false;
        $message = [
            'nif.required' => 'É obrigatório o nif',
            'status_id.required' => 'É obrigatório o nif',
            'nif.unique' => 'nif já cadastrado',
            'nome.required' => 'É obrigatório a indicação de um valor para o campo designação',
            'telefone_cliente.required' => 'É obrigatório a indicação de um valor para o campo telefone',
            'cidade.required' => 'É obrigatório a indicação de um valor para o campo cidade',
            'endereco.required' => 'É obrigatório a indicação de um valor para o campo endereço',
            'pais_id.required' => 'É obrigatório a indicação de um valor para o campo nacionalidade',
            'tipo_cliente_id.required' => 'É obrigatório a indicação de um valor para o campo tipo cliente'
        ];

        $validator = Validator::make($request->all(), [
            'nome' => ['required'],
            'telefone_cliente' => ['required'],
            'nif' => [
                'required', function ($attribute, $nif, $fail) use ($clienteId) {

                    $cliente = DB::connection('mysql2')->table('clientes')
                        ->where('empresa_id', auth()->user()->empresa_id)
                        ->where('id', '!=', $clienteId)
                        ->where('nif', $nif)->where('nif', '!=', '999999999')->first();

                    if ($cliente)
                        $fail('O ' . $attribute . ' já cadastrado');
                }
            ],
            'cidade' => ['required'],
            'endereco' => ['required'],
            'pais_id' => ['required'],
            'tipo_cliente_id' => ['required']

        ], $message);

        if ($validator->fails()) {
            if ($isApi) {
                return response()->json($validator->errors()->messages(), 400);
            }
            return redirect()->back()->withErrors($validator)
                ->withInput();
        }

        $cliente = Cliente::where('empresa_id', auth()->user()->empresa_id)->find($clienteId);

        $cliente->nome = $request->nome;
        $cliente->pessoa_contacto = $request->pessoa_contacto;
        $cliente->email = $request->email;
        $cliente->website = $request->website;
        $cliente->numero_contrato = $request->numero_contrato;
        $cliente->data_contrato = $request->data_contrato ?? NULL;
        $cliente->telefone_cliente = $request->telefone_cliente;
        $cliente->taxa_de_desconto = $request->taxa_de_desconto;
        $cliente->limite_de_credito = $request->limite_de_credito;
        $cliente->endereco = $request->endereco;
        $cliente->gestor_id = $request->gestor_id ?? NULL;
        $cliente->canal_id = $request->canal_id ? $request->canal_id : 2;
        $cliente->status_id = $request->status_id;
        $cliente->nif = $request->nif ? $request->nif : "999999999";
        $cliente->operador = auth()->user()->name;
        $cliente->tipo_cliente_id = $request->tipo_cliente_id;
        $cliente->user_id = auth()->user()->id;
        $cliente->empresa_id = auth()->user()->empresa_id;
        $cliente->pais_id = $request->pais_id;
        $cliente->cidade = $request->cidade;
        $cliente->tipo_conta_corrente = $request->tipo_conta_corrente;
        $cliente->save();

        if ($isApi) {
            return response()->json($cliente, 200);
        } else {
            Session::flash('message', 'Operação realizada com sucesso!');
            Session::flash('alert-class', 'alert-success');

            return redirect()->back();
        }
    }

    public  function listarProdutoComQuantidadeCritica()
    {
        $produtos = ExistenciaStock::with(['produtos'])
            ->whereHas('produtos', function ($q) {
                $q->where('produtos.quantidade_critica', '!=', 0)
                    ->where('produtos.empresa_id', auth()->user()->empresa_id)
                    ->where('produtos.quantidade_critica', '>=', 'existencias_stocks.quantidade');
            })

            ->get();
        return $produtos;
    }
}
