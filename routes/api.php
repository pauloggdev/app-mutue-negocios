<?php


use App\Http\Controllers\Api\ArmazenController;
use App\Http\Controllers\Api\Armazens\ArmazemCreateController;
use App\Http\Controllers\Api\Armazens\ArmazemIndexController;
use App\Http\Controllers\Api\Armazens\ArmazemShowController;
use App\Http\Controllers\Api\Armazens\ArmazemUpdateController;
use App\Http\Controllers\Api\Auth\EmpresaAuthController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\FabricanteController;
use App\Http\Controllers\Api\FacturaController;
use App\Http\Controllers\Api\FechoCaixaController;
use App\Http\Controllers\Api\FornecedorController;
use App\Http\Controllers\Api\MotivoIvaController;
use App\Http\Controllers\Api\PaisController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\RelatorioVendasController;
use App\Http\Controllers\Api\StatuGeralController;
use App\Http\Controllers\Api\TaxaIvaController;
use App\Http\Controllers\empresa\ClienteController as EmpresaClienteController;
use App\Http\Controllers\empresa\LicencaController as EmpresaLicencaController;
use App\Http\Controllers\RegimeController;
use App\Http\Controllers\TipoEmpresaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 *ROTAS EMPRESA
 */

Route::post('/empresa/usuario/login', [EmpresaAuthController::class, 'auth']);
Route::post('/admin/usuario/login', [AdminAuthController::class, 'auth']);
Route::get('/listarRegimeEmpresa', [RegimeController::class, 'index']);
Route::get('/listarTipoEmpresa', [TipoEmpresaController::class, 'index']);
Route::get('/listarPaises', [PaisController::class, 'index']);
Route::get('/listarStatusGeral', [StatuGeralController::class, 'index']);
Route::get('empresa/listarTipoClientes', [EmpresaClienteController::class, 'listarTipoClienteApi']);






//empresa
Route::middleware(['auth:sanctum'])->prefix('empresa')->group(function () {

    Route::get('/listarPaises', [PaisController::class, 'index']);
    Route::get('usuario/me', [EmpresaAuthController::class, 'me']);
    Route::post('usuario/logout', [EmpresaAuthController::class, 'logout']);
    Route::get('planos-assinaturas', [EmpresaLicencaController::class, 'index']);
    Route::get('facturas', [FacturaController::class, 'listarFacturas']);
    Route::get('facturas/imprimirFactura/{id}/{tipoFolha}', [FacturaController::class, 'imprimirFactura']);

    //ARMAZENS
    Route::get('armazens', [ArmazemIndexController::class, 'listarArmazens']);
    Route::post('armazens', [ArmazemCreateController::class, 'store']);
    Route::get('armazem/{id}', [ArmazemShowController::class, 'listarArmazem']);
    Route::put('armazem/{id}', [ArmazemUpdateController::class, 'update']);


    //Clientes
    Route::get('clientes', [ClienteController::class, 'getClientes']);
    Route::get('cliente/{id}', [ClienteController::class, 'getCliente']);
    Route::post('cadastrarCliente', [ClienteController::class, 'store']);
    Route::put('actualizarCliente/{id}', [ClienteController::class, 'update']);
    Route::get('imprimirClientes', [ClienteController::class, 'imprimirClientes']);

    //FORNECEDORES
    Route::get('fornecedores', [FornecedorController::class, 'getFornecedores']);
    Route::get('fornecedor/{id}', [FornecedorController::class, 'getFornecedor']);
    Route::post('cadastrarFornecedores', [FornecedorController::class, 'store']);
    Route::put('actualizarFornecedor/{id}', [FornecedorController::class, 'update']);

    //FABRICANTE
    Route::get('fabricantes', [FabricanteController::class, 'getFabricantes']);
    Route::get('fabricante/{id}', [FabricanteController::class, 'getFabricante']);
    Route::post('CadastrarFabricante', [FabricanteController::class, 'store']);
    Route::put('actualizarFabricante/{id}', [FabricanteController::class, 'update']);

    //PRODUTO
    Route::get('produtos', [ProdutoController::class, 'getProdutos']);
    Route::get('produto/{id}', [ProdutoController::class, 'getproduto']);
    Route::get('produtos/armazem/{id}', [ProdutoController::class, 'listarProdutosPeloIdArmazem']);
    Route::post('cadastrarproduto', [ProdutoController::class, 'store']);
    Route::put('actualizarproduto/{id}', [ProdutoController::class, 'update']);

    //FECHO DE CAIXA
    Route::post('imprimirFechoCaixa',[FechoCaixaController::class,'imprimirFechoCaixa']);

    //RELATORIO DE VENDA
    Route::post('imprimirVendaDiaria',[RelatorioVendasController::class,'imprimirVendaDiaria']);
    Route::post('imprimirVendaMensal',[RelatorioVendasController::class,'imprimirVendaMensal']);
    Route::post('imprimirVendaAnual',[RelatorioVendasController::class,'imprimirVendaAnual']);

    //IVA
    Route::get('taxas', [TaxaIvaController::class, 'listarTaxas']);
    Route::get('motivos', [MotivoIvaController::class, 'listarMotivos']);
});

//admin
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('usuario/me', [AdminAuthController::class, 'me']);
    Route::post('usuario/logout', [AdminAuthController::class, 'logout']);
});





/**
 * ROTAS ANTIGAS
 */

/*

Route::post('empresa/usuario/login', 'Api\\EmpresaAuthController@login'); //feito
Route::post('empresa/usuario/logout', 'Api\\EmpresaAuthController@logout'); //feito
Route::get('empresa/usuario/me', 'Api\\EmpresaAuthController@me'); //feito
Route::get('listarRegimeEmpresa', 'admin\EmpresaController@listarRegimeEmpresa'); //feito
Route::get('listarTipoEmpresa', 'admin\EmpresaController@listarTipoEmpresa'); //feito
Route::get('empresa/listarPaises', 'empresa\ClienteController@listarPaisesApi'); //feito

Route::post('admin/empresa/adicionar', 'admin\EmpresaController@cadastrarEmpresaApi'); //FALTA

Route::group(['middleware' => 'apiJwt'], function () {

    Route::get('empresa/facturas', 'empresa\FacturaController@indexApi'); //feito
    Route::post('empresa/facturas/adicionar', 'empresa\FacturacaoController@storeApi');
    Route::get('empresa/facturas/imprimir-factura/{id}/{tipoFolha}', 'empresa\FacturacaoController@imprimirFacturaApi');
    Route::get('empresa/facturas/reimprimir-factura/{id}', 'empresa\FacturacaoController@reimprimirFacturaApi');
    Route::get('empresa/facturacao/produtos/{armazen_id}', 'empresa\FacturacaoController@listarProdutosApi');



    Route::get('empresa/taxas', 'empresa\TaxaIvaController@listarTaxas');
    Route::get('empresa/motivos', 'admin\MotivoIvaController@listarMotivos');
    Route::get('empresa/fabricantes', 'empresa\FabricanteController@listarFabricantes');
    Route::get('empresa/listarArmazens', 'empresa\ArmazenController@listarArmazens');
    Route::get('empresa/listarFornecedores', 'empresa\FornecedorController@listarFornecedoresApi');
    Route::get('empresa/formaPagamentos', 'empresa\FormaPagamentoController@listarFormaPagamento');
    Route::get('empresa/tipoDocumentos', 'empresa\TipoDocumentoController@listarTipoDocumentos');
    Route::get('empresa/marcas', 'empresa\MarcaController@listarMarcas');
    Route::get('empresa/categorias', 'empresa\CategoriaController@listarCategorias');
    Route::get('empresa/classes', 'empresa\ClasseController@listarClasses');
    Route::get('empresa/unidadeMedidas', 'empresa\UnidadeController@listarUnidadeMedidas');
    Route::get('empresa/clientes', 'empresa\ClienteController@listarClienteApi');
    Route::get('empresa/cliente/{clienteId}', 'empresa\ClienteController@ApiListaCliente');
    Route::get('empresa/listarClientes', 'empresa\ClienteController@listarClientesApi');
    Route::get('empresa/produtos', 'empresa\ProdutoController@indexApi');
    Route::get('/empresa/produto/{produtoId}', 'empresa\ProdutoController@listarProduto');
    Route::post('/empresa/armazens/adicionar', 'empresa\ArmazenController@storeApi');
    Route::post('empresa/clientes/adicionar', 'empresa\ClienteController@storeApi');
    Route::post('empresa/clientes/actualizar/{id}', 'empresa\ClienteController@updateApi');
    Route::post('/empresa/produtos/adicionar', 'empresa\ProdutoController@storeApi');
    Route::put('empresa/usuario/alterarSenha/{userId}', 'empresa\UserController@alterarSenhaApi');

    //Dependencias
    //Route::get('empresa/listarPaises', 'empresa\ClienteController@listarPaisesApi');
    Route::get('empresa/listarTipoClientes', 'empresa\ClienteController@listarTipoClienteApi');
    Route::post('empresa/fechocaixa/imprimir', 'empresa\FechoCaixaController@imprimirFechoCaixaApi'); //falta
    Route::post('/empresa/produtos/editar/{id}', 'empresa\ProdutoController@updateApi');
    Route::post('empresa/entradaStock', 'empresa\StockController@entradaStockApi'); //FALTA
    //fecho de caixa
});

*/

/**
 * ROTAS ADMIN
 */

// Route::post('admin/usuario/login', 'Api\\AdminAuthController@login');
// Route::post('admin/usuario/logout', 'Api\\AdminAuthController@logout');














//faltantes






Route::group(['middleware' => ['apiJwt']], function () {

    Route::post('admin/logout', 'Api\\AdminAuthController@logout');
    Route::post('admin/me', 'Api\\AdminAuthController@me');
    Route::get('admin/usuarios', 'Api\\UserController@index');


    //licencas
    Route::post('/admin/licencas/adicionar', 'admin\LicencaController@store');
});

/**
 * ROTAS DA EMPRESA
 */
//Route::post('empresa/login', 'Api\\FuncionarioAuthController@login');

Route::group(['middleware' => ['apiJwt']], function () {

    Route::post('empresa/logout', 'Api\\FuncionarioAuthController@logout');
    Route::post('empresa/me', 'Api\\FuncionarioAuthController@me');
    //produtos
    //Route::post('/empresa/produtos/editar/{id}', 'empresa\ProdutoController@update');

    //stock
    //Route::post('empresa/entradaStock', 'empresa\StockController@entradaStock');

    //fornecedores

    //Route::post('empresa/produtos/actualizarStock', 'empresa\ExistenciaStockController@actualizarStock');//++
    //Route::post('empresa/produtos/transferencia/salvar', 'empresa\StockController@transferenciaSalvar');//++



    //fecho de caixa
    // Route::post('empresa/fechocaixa/imprimir', 'empresa\FechoCaixaController@imprimirFechoCaixa');//falta

    //usuário
});
