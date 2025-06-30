<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\FiltradoController;
use App\Http\Controllers\MateriaPrimaFiltradaController;
use App\Http\Controllers\MateriaPrimaSinFiltrarController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BovedaController;
use App\Http\Controllers\Contabilidad\ReporteContableController;
use App\Http\Controllers\Contabilidad\AsientoContableController;
use App\Http\Controllers\Contabilidad\CuentaContableController;
use App\Http\Controllers\Contabilidad\CentroCostoController;
use App\Http\Controllers\Contabilidad\CierreDiarioController;
use App\Http\Controllers\Contabilidad\CierreAnualController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\PagoSalarioController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\MolidoController;
use App\Http\Controllers\MateriaPrimaMolidaController;
use App\Http\Controllers\ProduccionEspecialController;
// Ruta pública de bienvenida
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cierre Diario

    // Clientes
    Route::middleware(['can:ver-clientes'])->group(function () {
        Route::resource('clientes', ClienteController::class);
    });

    // Compras
    Route::middleware(['can:ver-compras'])->group(function () {
        Route::resource('compras', CompraController::class);
    });

    // Filtrado
    Route::middleware(['can:ver-filtrado'])->group(function () {
        Route::prefix('filtrado')->group(function () {
            Route::get('/', [FiltradoController::class, 'index'])->name('filtrado.index');
            Route::get('/create', [FiltradoController::class, 'create'])->name('filtrado.create');
            Route::post('/', [FiltradoController::class, 'store'])->name('filtrado.store');
        });
    });

    // Rutas para el módulo de filtrado
    Route::resource('filtrado', FiltradoController::class);

    // Materias Primas
    Route::middleware(['can:ver-materias-primas'])->group(function () {
        Route::resource('materia-prima-sin-filtrar', MateriaPrimaSinFiltrarController::class)->parameters([
            'materia-prima-sin-filtrar' => 'materia_prima_sin_filtrar'
        ]);
        Route::resource('materia-prima-filtrada', MateriaPrimaFiltradaController::class)->parameters([
            'materia-prima-filtrada' => 'materia_prima_filtrada'
        ]);
    });

    // Pagos
    Route::middleware(['can:ver-pagos'])->group(function () {
        Route::prefix('pagos')->group(function () {
            Route::prefix('clientes')->group(function () {
                Route::get('/', [PagoController::class, 'indexClientes'])->name('pagos.clientes.index');
                Route::get('/create/{venta}', [PagoController::class, 'createCliente'])->name('pagos.clientes.create');
                Route::post('/store/{venta}', [PagoController::class, 'storeCliente'])->name('pagos.clientes.store');
            });

            Route::prefix('proveedores')->group(function () {
                Route::get('/', [PagoController::class, 'indexProveedores'])->name('pagos.proveedores.index');
                Route::get('/create/{compra}', [PagoController::class, 'createProveedor'])->name('pagos.proveedores.create');
                Route::post('/store/{compra}', [PagoController::class, 'storeProveedor'])->name('pagos.proveedores.store');
            });
        });
    });

    // Producción
    Route::middleware(['can:ver-produccion'])->group(function () {
        Route::resource('produccion', ProduccionController::class);
        Route::resource('produccion-especial', ProduccionEspecialController::class);
    });

    // Productos
    Route::middleware(['can:ver-productos'])->group(function () {
        Route::resource('productos', ProductoController::class);
    });

    // Proveedores
    Route::middleware(['can:ver-proveedores'])->group(function () {
        Route::resource('proveedores', ProveedorController::class);
    });
    
    // Reportes
    Route::middleware(['can:ver-reportes'])->group(function () {
        Route::prefix('reportes')->group(function () {
            Route::get('/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
            Route::get('/desperdicio', [ReporteController::class, 'desperdicio'])->name('reportes.desperdicio');
            Route::get('/produccion', [ReporteController::class, 'produccion'])->name('reportes.produccion');
            Route::get('/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
            Route::get('/compras', [ReporteController::class, 'compras'])->name('reportes.compras');
            Route::get('/flujo-caja', [ReporteController::class, 'flujoCaja'])->name('reportes.flujo-caja');

            // Reportes Contables
            Route::prefix('contabilidad')->group(function () {
                Route::get('/balance', [ReporteContableController::class, 'balance'])->name('reportes.balance');
                Route::get('/libro-diario', [ReporteContableController::class, 'libroDiario'])->name('reportes.libro-diario');
                Route::get('/libro-mayor', [ReporteContableController::class, 'libroMayor'])->name('reportes.libro-mayor');
                Route::get('/estado-resultados', [ReporteContableController::class, 'estadoResultados'])->name('reportes.estado-resultados');
                Route::get('/balance-comprobacion', [ReporteContableController::class, 'balanceComprobacion'])->name('reportes.balance-comprobacion');
            });
        });
    });

    // Rutas para reportes contables
    Route::prefix('contabilidad/reportes')->group(function () {
        Route::get('/balance-general', [ReporteContableController::class, 'balanceGeneral'])->name('contabilidad.reportes.balance-general');
        Route::get('/estado-resultados', [ReporteContableController::class, 'estadoResultados'])->name('contabilidad.reportes.estado-resultados');
        Route::get('/libro-mayor', [ReporteContableController::class, 'libroMayor'])->name('contabilidad.reportes.libro-mayor');
        Route::get('/libro-diario', [ReporteContableController::class, 'libroDiario'])->name('contabilidad.reportes.libro-diario');
        Route::get('/balance-comprobacion', [ReporteContableController::class, 'balanceComprobacion'])->name('contabilidad.reportes.balance-comprobacion');
    });

    // Ventas
    Route::middleware(['can:ver-ventas'])->group(function () {
        Route::resource('ventas', VentaController::class);
    });

    // Rutas de roles y permisos
    Route::middleware(['can:ver-roles'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Rutas de Permisos
    Route::middleware(['can:ver-permisos'])->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Usuarios
    Route::middleware(['can:ver-usuarios'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Rutas para la Bóveda
    Route::get('/boveda', [BovedaController::class, 'index'])->name('boveda.index');
    Route::post('/boveda/transferir', [BovedaController::class, 'transferir'])->name('boveda.transferir');

    // Rutas de Contabilidad
    Route::middleware(['can:ver-contabilidad'])->group(function () {
        Route::prefix('contabilidad')->name('contabilidad.')->group(function () {
            // Rutas de Asientos Contables
            Route::prefix('asientos')->name('asientos.')->group(function () {
                Route::get('/', [AsientoContableController::class, 'index'])->name('index');
                Route::get('/create', [AsientoContableController::class, 'create'])->name('create');
                Route::post('/', [AsientoContableController::class, 'store'])->name('store');
                
                // Nuevas rutas para asientos pendientes (mover antes de las rutas con parámetros)
                Route::get('/pendientes', [AsientoContableController::class, 'pendientes'])->name('pendientes');
                Route::get('/pendientes-pago', [AsientoContableController::class, 'pendientesPago'])->name('pendientes-pago');
                Route::post('/generar-pago', [AsientoContableController::class, 'generarAsientoPago'])->name('generar-pago');
                Route::post('/generar-pendiente', [AsientoContableController::class, 'generarAsientoPendiente'])->name('generar-pendiente');
                
                // Rutas con parámetros
                Route::get('/{asiento}', [AsientoContableController::class, 'show'])->name('show');
                Route::get('/{asiento}/edit', [AsientoContableController::class, 'edit'])->name('edit');
                Route::put('/{asiento}', [AsientoContableController::class, 'update'])->name('update');
                Route::delete('/{asiento}', [AsientoContableController::class, 'destroy'])->name('destroy');
                Route::put('/{asiento}/publicar', [AsientoContableController::class, 'publicar'])->name('publicar');
                Route::put('/{asiento}/anular', [AsientoContableController::class, 'anular'])->name('anular');
            });
            
            // Plan de Cuentas
            Route::prefix('cuentas')->name('cuentas.')->group(function () {
                Route::get('/', [CuentaContableController::class, 'index'])->name('index');
                Route::get('/create', [CuentaContableController::class, 'create'])->name('create');
                Route::post('/', [CuentaContableController::class, 'store'])->name('store');
                Route::get('/{cuenta}', [CuentaContableController::class, 'show'])->name('show');
                Route::get('/{cuenta}/edit', [CuentaContableController::class, 'edit'])->name('edit');
                Route::put('/{cuenta}', [CuentaContableController::class, 'update'])->name('update');
                Route::delete('/{cuenta}', [CuentaContableController::class, 'destroy'])->name('destroy');
            });
            
            // Cierres Contables
            Route::prefix('cierres')->name('cierres.')->group(function () {
                // Cierres Diarios
                Route::prefix('diario')->name('diario.')->group(function () {
                    Route::get('/', [CierreDiarioController::class, 'index'])->name('index');
                    Route::get('/create', [CierreDiarioController::class, 'create'])->name('create');
                    Route::post('/', [CierreDiarioController::class, 'store'])->name('store');
                    Route::get('/{cierre}', [CierreDiarioController::class, 'show'])->name('show');
                    Route::get('/{cierre}/edit', [CierreDiarioController::class, 'edit'])->name('edit');
                    Route::put('/{cierre}', [CierreDiarioController::class, 'update'])->name('update');
                    Route::delete('/{cierre}', [CierreDiarioController::class, 'destroy'])->name('destroy');
                    Route::post('/{cierre}/cerrar', [CierreDiarioController::class, 'cerrar'])->name('cerrar');
                    Route::get('/{cierre}/export-excel', [CierreDiarioController::class, 'exportExcel'])->name('export-excel');
                });

                // Cierres Anuales
                Route::prefix('anual')->name('anual.')->group(function () {
                    Route::get('/', [CierreAnualController::class, 'index'])->name('index');
                    Route::get('/create', [CierreAnualController::class, 'create'])->name('create');
                    Route::post('/', [CierreAnualController::class, 'store'])->name('store');
                    Route::get('/{cierre}', [CierreAnualController::class, 'show'])->name('show');
                    Route::get('/{cierre}/edit', [CierreAnualController::class, 'edit'])->name('edit');
                    Route::put('/{cierre}', [CierreAnualController::class, 'update'])->name('update');
                    Route::delete('/{cierre}', [CierreAnualController::class, 'destroy'])->name('destroy');
                    Route::post('/{cierre}/cerrar', [CierreAnualController::class, 'cerrar'])->name('cerrar');
                });
            });
            
            // Centros de Costo
            Route::prefix('centros-costo')->name('centros-costo.')->group(function () {
                Route::get('/', [CentroCostoController::class, 'index'])->name('index');
                Route::get('/create', [CentroCostoController::class, 'create'])->name('create');
                Route::post('/', [CentroCostoController::class, 'store'])->name('store');
                Route::get('/{centroCosto}', [CentroCostoController::class, 'show'])->name('show');
                Route::get('/{centroCosto}/edit', [CentroCostoController::class, 'edit'])->name('edit');
                Route::put('/{centroCosto}', [CentroCostoController::class, 'update'])->name('update');
                Route::delete('/{centroCosto}', [CentroCostoController::class, 'destroy'])->name('destroy');
            });
        });
    });

    // Rutas para backups
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'create'])->name('backups.create');
    Route::post('/backups/upload', [BackupController::class, 'upload'])->name('backups.upload');
    Route::get('/backups/{filename}', [BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/{filename}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');

    // Rutas para Pagos de Salarios
    Route::middleware(['can:ver-pagos-salarios'])->group(function () {
        Route::prefix('pagos')->name('pagos.')->group(function () {
            Route::prefix('salarios')->name('salarios.')->group(function () {
                Route::get('/', [PagoSalarioController::class, 'index'])->name('index');
                Route::get('/create', [PagoSalarioController::class, 'create'])->name('create');
                Route::post('/', [PagoSalarioController::class, 'store'])->name('store');
                Route::get('/{pago}', [PagoSalarioController::class, 'show'])->name('show');
                Route::get('/{pago}/edit', [PagoSalarioController::class, 'edit'])->name('edit');
                Route::put('/{pago}', [PagoSalarioController::class, 'update'])->name('update');
                Route::delete('/{pago}', [PagoSalarioController::class, 'destroy'])->name('destroy');
                Route::get('/vista-generar-asiento', [PagoSalarioController::class, 'vistaGenerarAsiento'])->name('vista-generar-asiento');
                Route::post('/generar-asiento', [PagoSalarioController::class, 'generarAsiento'])->name('generar-asiento');
            });
        });
    });

    // Empleados
    Route::middleware(['can:ver-empleados'])->group(function () {
        Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
        Route::get('/empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create')->middleware('can:crear-empleados');
        Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store')->middleware('can:crear-empleados');
        Route::get('/empleados/{empleado}', [EmpleadoController::class, 'show'])->name('empleados.show');
        Route::get('/empleados/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit')->middleware('can:editar-empleados');
        Route::put('/empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update')->middleware('can:editar-empleados');
        Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy')->middleware('can:eliminar-empleados');
    });

    // Pagos de Salarios
    Route::middleware(['can:ver-pagos-salarios'])->group(function () {
        Route::resource('pagos-salarios', PagoSalarioController::class);
    });

    // Molido
    Route::middleware(['can:ver-molido'])->group(function () {
        Route::get('molido', [MolidoController::class, 'index'])->name('molido.index');
        Route::get('molido/create', [MolidoController::class, 'create'])->name('molido.create');
        Route::post('molido', [MolidoController::class, 'store'])->name('molido.store');
        Route::delete('molido/{molido}', [MolidoController::class, 'destroy'])->name('molido.destroy');
        Route::get('molido/inventario', [MateriaPrimaMolidaController::class, 'index'])->name('molido.inventario');
        Route::get('molido/molida/create', [MateriaPrimaMolidaController::class, 'create'])->name('molido.molida.create');
        Route::post('molido/molida', [MateriaPrimaMolidaController::class, 'store'])->name('molido.molida.store');
        Route::get('molido/molida/{materiaPrimaMolida}/edit', [MateriaPrimaMolidaController::class, 'edit'])->name('molido.molida.edit');
        Route::put('molido/molida/{materiaPrimaMolida}', [MateriaPrimaMolidaController::class, 'update'])->name('molido.molida.update');
        Route::delete('molido/molida/{materiaPrimaMolida}', [MateriaPrimaMolidaController::class, 'destroy'])->name('molido.molida.destroy');
    });
});

// Rutas de autenticación (Breeze)
require __DIR__.'/auth.php';

// Redirección post-login
Route::get('/home', function () {
    return redirect()->route('dashboard');
});
