<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Produccion;
use App\Models\MateriaPrimaFiltrada;
use App\Models\MateriaPrimaSinFiltrar;
use App\Models\Filtrado;
use App\Models\PagoCliente;
use App\Models\PagoProveedor;
use App\Models\CierreDiario;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        // Observers
        Producto::observe(\App\Observers\ProductoObserver::class);
        Cliente::observe(\App\Observers\ClienteObserver::class);
        Proveedor::observe(\App\Observers\ProveedorObserver::class);
        Venta::observe(\App\Observers\VentaObserver::class);
        Compra::observe(\App\Observers\CompraObserver::class);
        Produccion::observe(\App\Observers\ProduccionObserver::class);
        MateriaPrimaFiltrada::observe(\App\Observers\MateriaPrimaFiltradaObserver::class);
        MateriaPrimaSinFiltrar::observe(\App\Observers\MateriaPrimaSinFiltrarObserver::class);
        Filtrado::observe(\App\Observers\FiltradoObserver::class);
        PagoCliente::observe(\App\Observers\PagoClienteObserver::class);
        PagoProveedor::observe(\App\Observers\PagoProveedorObserver::class);
        CierreDiario::observe(\App\Observers\CierreDiarioObserver::class);
        User::observe(\App\Observers\UserObserver::class);
        Role::observe(\App\Observers\RoleObserver::class);
        Permission::observe(\App\Observers\PermissionObserver::class);
    }
}
