<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-gray-800">
        <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900">
            <h1 class="text-white font-bold text-lg">Sistema de Gestión</h1>
        </div>
        <div class="flex-1 flex flex-col overflow-y-auto">
            <nav class="flex-1 px-2 py-4 space-y-1">
                <!-- Dashboard -->
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    <x-icons.dashboard class="mr-3" />
                    Dashboard
                </x-nav-link>

                <!-- Compras -->
                <x-nav-dropdown title="Compras" :active="request()->is('compras*')">
                    <x-slot name="icon">
                        <x-icons.shopping-cart class="mr-3" />
                    </x-slot>
                    <x-dropdown-link href="{{ route('compras.index') }}">Lista de Compras</x-dropdown-link>
                    <x-dropdown-link href="{{ route('compras.create') }}">Nueva Compra</x-dropdown-link>
                    <x-dropdown-link href="{{ route('proveedores.index') }}">Proveedores</x-dropdown-link>
                </x-nav-dropdown>

                <!-- Ventas -->
                <x-nav-dropdown title="Ventas" :active="request()->is('ventas*')">
                    <x-slot name="icon">
                        <x-icons.cash class="mr-3" />
                    </x-slot>
                    <x-dropdown-link href="{{ route('ventas.index') }}">Lista de Ventas</x-dropdown-link>
                    <x-dropdown-link href="{{ route('ventas.create') }}">Nueva Venta</x-dropdown-link>
                    <x-dropdown-link href="{{ route('clientes.index') }}">Clientes</x-dropdown-link>
                </x-nav-dropdown>

                <!-- Producción -->
                <x-nav-dropdown title="Producción" :active="request()->is('produccion*', 'filtrado*', 'molido*')">
                    <x-slot name="icon">
                        <x-icons.factory class="mr-3" />
                    </x-slot>
                    <x-dropdown-link href="{{ route('produccion.index') }}">Registro Producción</x-dropdown-link>
                    <x-dropdown-link href="{{ route('produccion.create') }}">Nueva Producción</x-dropdown-link>
                    <x-dropdown-link href="{{ route('filtrado.index') }}">Proceso de Filtrado</x-dropdown-link>
                    <x-dropdown-link href="{{ route('molido.index') }}">Procesos de Molido</x-dropdown-link>
                    <x-dropdown-link href="{{ route('produccion-especial.index') }}">Producción Especial</x-dropdown-link>
                </x-nav-dropdown>

                <!-- Inventarios -->
                <x-nav-dropdown title="Inventarios" :active="request()->is('materia-prima*', 'productos*')">
                    <x-slot name="icon">
                        <x-icons.inventory class="mr-3" />
                    </x-slot>
                    <x-dropdown-link href="{{ route('materia-prima-sin-filtrar.index') }}">MP Sin Filtrar</x-dropdown-link>
                    <x-dropdown-link href="{{ route('materia-prima-filtrada.index') }}">MP Filtrada</x-dropdown-link>
                    <x-dropdown-link href="{{ route('productos.index') }}">Productos Terminados</x-dropdown-link>
                    <x-dropdown-link href="{{ route('molido.inventario') }}">Inventario Molido</x-dropdown-link>
                </x-nav-dropdown>

                <!-- Finanzas -->
                <x-nav-dropdown title="Finanzas" :active="request()->is('cuentas*', 'cierre*', 'pagos*')">
                    <x-slot name="icon">
                        <x-icons.finance class="mr-3" />
                    </x-slot>
                    <x-dropdown-link href="{{ route('cuentas-bancarias.index') }}">Cuentas Bancarias</x-dropdown-link>
                    <x-dropdown-link href="{{ route('cierre.index') }}">Cierre Diario</x-dropdown-link>
                    <x-dropdown-link href="{{ route('pagos.clientes.index') }}">Pagos de Clientes</x-dropdown-link>
                    <x-dropdown-link href="{{ route('pagos.proveedores.index') }}">Pagos a Proveedores</x-dropdown-link>
                </x-nav-dropdown>

                <!-- Contabilidad -->
                <x-nav-dropdown title="Contabilidad" :active="request()->is('contabilidad*', 'centros-costo*')">
                    <x-slot name="icon">
                        <x-icons.calculator class="mr-3" />
                    </x-slot>
                    <x-dropdown-link href="{{ route('contabilidad.asientos.index') }}">Asientos Contables</x-dropdown-link>
                    <x-dropdown-link href="{{ route('contabilidad.cuentas.index') }}">Plan de Cuentas</x-dropdown-link>
                    <x-dropdown-link href="{{ route('contabilidad.cierres.diario.index') }}">Cierre Diario</x-dropdown-link>
                    <x-dropdown-link href="{{ route('contabilidad.centros-costo.index') }}">Centros de Costo</x-dropdown-link>
                </x-nav-dropdown>

                <!-- Reportes -->
                <x-nav-dropdown title="Reportes" :active="request()->is('reportes*')">
                    <x-slot name="icon">
                        <x-icons.report class="mr-3" />
                    </x-slot>
                    <x-dropdown-link href="{{ route('reportes.inventario') }}">Inventarios</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.desperdicio') }}">Desperdicio</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.produccion') }}">Producción</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.ventas') }}">Ventas</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.compras') }}">Compras</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.balance') }}">Balance General</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.libro-diario') }}">Libro Diario</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.libro-mayor') }}">Libro Mayor</x-dropdown-link>
                    <x-dropdown-link href="{{ route('reportes.estado-resultados') }}">Estado de Resultados</x-dropdown-link>
                </x-nav-dropdown>

                <!-- Empleados -->
                <x-nav-link href="{{ route('empleados.index') }}" :active="request()->routeIs('empleados.*')">
                    <x-icons.user class="mr-3" />
                    Empleados
                </x-nav-link>

                <!-- Pagos de Salarios -->
                <x-nav-link href="{{ route('pagos.salarios.index') }}" :active="request()->routeIs('pagos.salarios.*')">
                    <x-icons.money class="mr-3" />
                    Pagos de Salarios
                </x-nav-link>
            </nav>
        </div>
    </div>
</div>