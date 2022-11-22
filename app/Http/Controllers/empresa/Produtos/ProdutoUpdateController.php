<?php

namespace App\Http\Controllers\empresa\Produtos;

use App\Http\Controllers\Controller;
use App\Models\empresa\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProdutoUpdateController extends Controller
{



    public function update(Request $request, $uuid)
    {
        $message = [
            'designacao.required' => 'É obrigatório o nome',
            'categoria_id.required' => 'É obrigatório a categoria',
            'fabricante_id.required' => 'É obrigatório o fabricante',
            'preco_venda.required' => 'É obrigatório o preço de venda',
            'status_id.required' => 'É obrigatório o status',

        ];
        $this->validate($request, [
            'designacao' => ['required'],
            'categoria_id' => ['required'],
            'preco_venda' => ['required', 'min:0'],
            'status_id' => ['required'],
            'fabricante_id' => ['required'],
            'imagem_produto' => 'image|max:1024'
        ], $message);



        $produto = Produto::where('uuid', $uuid)
            ->where('empresa_id', auth()->user()->empresa_id)->first();

        if (!$produto) {
            return redirect()->back();
        }

        $imagem = NULL;
        if ($request->hasFile('imagem_produto') && $request->imagem_produto->isValid()) {
            if (Storage::exists($produto->imagem_produto)) {
                Storage::delete($produto->imagem_produto);
            }
            $imagem = $request->imagem_produto->store("/produtos");
        }

        try {

            DB::beginTransaction();

            DB::table('produtos')->where('uuid', $uuid)->update([
                'designacao' => $request->designacao,
                'preco_venda' => $request->preco_venda,
                'preco_compra' => $request->preco_compra,
                'categoria_id' => $request->categoria_id,
                'unidade_medida_id' => $request->unidade_medida_id,
                'fabricante_id' => $request->fabricante_id,
                'status_id' => $request->status_id,
                'codigo_taxa' => $request->codigo_taxa,
                'motivo_isencao_id' => $request->codigo_taxa <= 0 ? 8 : $request->motivo_isencao_id, //Transmissão de bens e serviço não sujeita
                'quantidade_minima' => $request->quantidade_minima ?? 0,
                'quantidade_critica' => $request->quantidade_critica ?? 0,
                'imagem_produto' => $imagem ? $imagem : $request->imagem_produto,
                'stocavel' => $request->stocavel
            ]);

            if ($request->stocavel == 'Não') {
                DB::table('existencias_stocks')->where('produto_id', $produto->id)->update([
                    'quantidade' =>  0
                ]);
            }
            DB::commit();
            return redirect()->route('produto.edit', $uuid)->withSuccess('Operação efectuada com Sucesso!');
        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->route('produto.create', $uuid)->withErrors('Erro ao cadastrar centro de custo!');
        }
    }
}
