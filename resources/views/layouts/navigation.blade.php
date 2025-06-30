<!-- Sidebar -->
<nav id="sidebar" class="sidebar d-print-none">
    <!-- Botón para contraer/expandir -->
    <button id="sidebarToggle" class="btn btn-link text-white position-absolute top-0 end-0 mt-3 me-3" onclick="toggleSidebar()">
        <i class="fas fa-chevron-left" id="toggleIcon"></i>
    </button>

    <!-- Logo -->
    <div class="sidebar-header">
        <div class="logo-container">
            <i class="fas fa-wine-bottle"></i>
        </div>
    </div>

    <!-- Perfil de Usuario -->
    <div class="user-profile">
        <div class="user-info">
            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=3498db&color=fff" 
                 class="user-avatar" 
                 alt="{{ auth()->user()->name }}">
            <div class="user-details">
                <h6>{{ auth()->user()->name }}</h6>
                <small>{{ auth()->user()->email }}</small>
            </div>
        </div>
        <div class="user-actions">
            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-user-edit"></i>
                <span class="menu-text">Perfil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="menu-text">Salir</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Menú Principal -->
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" 
                   href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <!-- Módulo de Producción -->
            @canany(['ver-produccion', 'ver-filtrado', 'ver-molido'])
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('produccion.*') || request()->routeIs('filtrado.*') || request()->routeIs('molido.*') ? 'active' : '' }}" 
                   href="#produccionSubmenu" 
                   data-bs-toggle="collapse">
                    <i class="fas fa-industry"></i>
                    <span class="menu-text">Producción</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('produccion.*') || request()->routeIs('filtrado.*') || request()->routeIs('molido.*') ? 'show' : '' }}" id="produccionSubmenu">
                    <ul class="nav flex-column submenu">
                        @can('ver-produccion')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('produccion.*') ? 'active' : '' }}" 
                               href="{{ route('produccion.index') }}">
                                <i class="fas fa-industry"></i>
                                <span class="menu-text">Registro Producción</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('produccion.create') ? 'active' : '' }}" 
                               href="{{ route('produccion.create') }}">
                                <i class="fas fa-plus"></i>
                                <span class="menu-text">Nueva Producción</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-produccion-especial')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('produccion-especial.*') ? 'active' : '' }}" href="{{ route('produccion-especial.index') }}">
                                <i class="fas fa-star"></i>
                                <span class="menu-text">Producción Especial</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-filtrado')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('filtrado.*') ? 'active' : '' }}" 
                               href="{{ route('filtrado.index') }}">
                                <i class="fas fa-filter"></i>
                                <span class="menu-text">Proceso de Filtrado</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-molido')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('molido.index') ? 'active' : '' }}" href="{{ route('molido.index') }}">
                                <i class="fas fa-blender"></i>
                                <span class="menu-text">Procesos de Molido</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            <!-- Módulo de Inventario -->
            @canany(['ver-materias-primas', 'ver-productos', 'ver-inventario', 'ver-molido'])
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('materia-prima-sin-filtrar.*') || request()->routeIs('materia-prima-filtrada.*') || request()->routeIs('productos.*') || request()->routeIs('molido.inventario') ? 'active' : '' }}" 
                   href="#inventarioSubmenu" 
                   data-bs-toggle="collapse">
                    <i class="fas fa-boxes"></i>
                    <span class="menu-text">Inventario</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('materia-prima-sin-filtrar.*') || request()->routeIs('materia-prima-filtrada.*') || request()->routeIs('productos.*') || request()->routeIs('molido.inventario') ? 'show' : '' }}" id="inventarioSubmenu">
                    <ul class="nav flex-column submenu">
                        @can('ver-materias-primas')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('materia-prima-sin-filtrar.*') ? 'active' : '' }}" 
                               href="{{ route('materia-prima-sin-filtrar.index') }}">
                                <i class="fas fa-box-open"></i>
                                <span class="menu-text">MP Sin Filtrar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('materia-prima-filtrada.*') ? 'active' : '' }}" 
                               href="{{ route('materia-prima-filtrada.index') }}">
                                <i class="fas fa-box"></i>
                                <span class="menu-text">MP Filtrada</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-productos')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" 
                               href="{{ route('productos.index') }}">
                                <i class="fas fa-wine-bottle"></i>
                                <span class="menu-text">Productos Terminados</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-molido')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('molido.inventario') ? 'active' : '' }}" href="{{ route('molido.inventario') }}">
                                <i class="fas fa-box"></i>
                                <span class="menu-text">Inventario Molido</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            <!-- Módulo de Ventas -->
            @can('ver-ventas')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ventas.*') || request()->routeIs('clientes.*') ? 'active' : '' }}" 
                   href="#ventasSubmenu" 
                   data-bs-toggle="collapse">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="menu-text">Ventas</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('ventas.*') || request()->routeIs('clientes.*') ? 'show' : '' }}" id="ventasSubmenu">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}" 
                               href="{{ route('ventas.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="menu-text">Ventas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" 
                               href="{{ route('clientes.index') }}">
                                <i class="fas fa-users"></i>
                                <span class="menu-text">Clientes</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            <!-- Módulo de Compras -->
            @canany(['ver-compras', 'ver-proveedores'])
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('compras.*') || request()->routeIs('proveedores.*') ? 'active' : '' }}" 
                   href="#comprasSubmenu" 
                   data-bs-toggle="collapse">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="menu-text">Compras</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('compras.*') || request()->routeIs('proveedores.*') ? 'show' : '' }}" id="comprasSubmenu">
                    <ul class="nav flex-column submenu">
                        @can('ver-compras')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}" 
                               href="{{ route('compras.index') }}">
                                <i class="fas fa-shopping-bag"></i>
                                <span class="menu-text">Compras</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-proveedores')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" 
                               href="{{ route('proveedores.index') }}">
                                <i class="fas fa-truck"></i>
                                <span class="menu-text">Proveedores</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            <!-- Módulo de Finanzas -->
            @canany(['ver-cierre', 'ver-reporte.flujo-caja', 'ver-boveda'])
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cierre.*') || request()->routeIs('reporte.flujo-caja') || request()->routeIs('boveda.*') ? 'active' : '' }}" 
                   href="#finanzasSubmenu" 
                   data-bs-toggle="collapse">
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="menu-text">Finanzas</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('cierre.*') || request()->routeIs('reporte.flujo-caja') || request()->routeIs('boveda.*') ? 'show' : '' }}" id="finanzasSubmenu">
                    <ul class="nav flex-column submenu">
                        @can('ver-boveda')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('boveda.*') ? 'active' : '' }}" 
                               href="{{ route('boveda.index') }}">
                                <i class="fas fa-vault"></i>
                                <span class="menu-text">Bóveda</span>
                            </a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cierre.*') ? 'active' : '' }}" 
                               href="{{ route('cierre.index') }}">
                                <i class="fas fa-calendar-check"></i>
                                <span class="menu-text">Cierre Diario</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reporte.flujo-caja') ? 'active' : '' }}" 
                               href="{{ route('reporte.flujo-caja') }}">
                                <i class="fas fa-chart-line"></i>
                                <span class="menu-text">Flujo de Caja</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany

            <!-- Módulo Contable -->
            @canany(['ver-contabilidad', 'ver-centros-costo'])
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('contabilidad.*') ? 'active' : '' }}" 
                   href="#contabilidadSubmenu" 
                   data-bs-toggle="collapse">
                    <i class="fas fa-calculator"></i>
                    <span class="menu-text">Contabilidad</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('contabilidad.*') ? 'show' : '' }}" id="contabilidadSubmenu">
                    <ul class="nav flex-column submenu">
                        @can('ver-contabilidad')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contabilidad.asientos.*') ? 'active' : '' }}" 
                               href="{{ route('contabilidad.asientos.index') }}">
                                <i class="fas fa-book"></i>
                                <span class="menu-text">Asientos Contables</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contabilidad.cuentas.*') ? 'active' : '' }}" 
                               href="{{ route('contabilidad.cuentas.index') }}">
                                <i class="fas fa-list"></i>
                                <span class="menu-text">Plan de Cuentas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contabilidad.cierres.diario.*') ? 'active' : '' }}" 
                               href="{{ route('contabilidad.cierres.diario.index') }}">
                                <i class="fas fa-lock"></i>
                                <span class="menu-text">Cierre Diario</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-centros-costo')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contabilidad.centros-costo.*') ? 'active' : '' }}" 
                               href="{{ route('contabilidad.centros-costo.index') }}">
                                <i class="fas fa-building"></i>
                                <span class="menu-text">Centros de Costo</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            <!-- Módulo de Reportes -->
            @can('ver-reportes')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" 
                   data-bs-toggle="collapse" href="#reportes" role="button">
                    <i class="fas fa-chart-bar"></i>
                    <span class="menu-text">Reportes</span>
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('reportes.*') ? 'show' : '' }}" id="reportes">
                    <ul class="nav flex-column sub-menu">
                        <!-- Reportes de Producción -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.produccion') ? 'active' : '' }}" 
                               href="{{ route('reportes.produccion') }}">
                                <i class="fas fa-industry"></i>
                                <span class="menu-text">Producción</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.desperdicio') ? 'active' : '' }}" 
                               href="{{ route('reportes.desperdicio') }}">
                                <i class="fas fa-trash"></i>
                                <span class="menu-text">Desperdicio</span>
                            </a>
                        </li>
                        <!-- Reportes de Inventario -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.inventario') ? 'active' : '' }}" 
                               href="{{ route('reportes.inventario') }}">
                                <i class="fas fa-boxes"></i>
                                <span class="menu-text">Inventario</span>
                            </a>
                        </li>
                        <!-- Reportes de Ventas -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.ventas') ? 'active' : '' }}" 
                               href="{{ route('reportes.ventas') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="menu-text">Ventas</span>
                            </a>
                        </li>
                        <!-- Reportes de Compras -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.compras') ? 'active' : '' }}" 
                               href="{{ route('reportes.compras') }}">
                                <i class="fas fa-truck"></i>
                                <span class="menu-text">Compras</span>
                            </a>
                        </li>
                        <!-- Reportes Financieros -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.balance') ? 'active' : '' }}" 
                               href="{{ route('reportes.balance') }}">
                                <i class="fas fa-balance-scale"></i>
                                <span class="menu-text">Balance General</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.libro-diario') ? 'active' : '' }}" 
                               href="{{ route('reportes.libro-diario') }}">
                                <i class="fas fa-book"></i>
                                <span class="menu-text">Libro Diario</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.libro-mayor') ? 'active' : '' }}" 
                               href="{{ route('reportes.libro-mayor') }}">
                                <i class="fas fa-book-open"></i>
                                <span class="menu-text">Libro Mayor</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.estado-resultados') ? 'active' : '' }}" 
                               href="{{ route('reportes.estado-resultados') }}">
                                <i class="fas fa-chart-line"></i>
                                <span class="menu-text">Estado de Resultados</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.balance-comprobacion') ? 'active' : '' }}" 
                               href="{{ route('reportes.balance-comprobacion') }}">
                                <i class="fas fa-calculator"></i>
                                <span class="menu-text">Balance de Comprobación</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            <!-- Módulo de Administración -->
            @canany(['ver-roles', 'ver-permisos', 'ver-usuarios'])
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('users.*') ? 'active' : '' }}" 
                   href="#adminSubmenu" 
                   data-bs-toggle="collapse">
                    <i class="fas fa-cogs"></i>
                    <span class="menu-text">Administración</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('users.*') ? 'show' : '' }}" id="adminSubmenu">
                    <ul class="nav flex-column submenu">
                        @can('ver-usuarios')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                               href="{{ route('users.index') }}">
                                <i class="fas fa-users-cog"></i>
                                <span class="menu-text">Usuarios</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-roles')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" 
                               href="{{ route('roles.index') }}">
                                <i class="fas fa-user-tag"></i>
                                <span class="menu-text">Roles</span>
                            </a>
                        </li>
                        @endcan
                        @can('ver-permisos')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}" 
                               href="{{ route('permissions.index') }}">
                                <i class="fas fa-key"></i>
                                <span class="menu-text">Permisos</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            <!-- Módulo de Empleados -->
            @can('ver-empleados')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}" 
                   href="{{ route('empleados.index') }}">
                    <i class="fas fa-user-friends"></i>
                    <span class="menu-text">Empleados</span>
                </a>
            </li>
            @endcan

            <!-- Módulo de Pagos de Salarios -->
            @can('ver-pagos-salarios')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pagos.salarios.*') ? 'active' : '' }}" 
                   href="{{ route('pagos.salarios.index') }}">
                    <i class="fas fa-money-check-alt"></i>
                    <span class="menu-text">Pagos de Salarios</span>
                </a>
            </li>
            @endcan
        </ul>
    </div>
</nav>

<style>
.sidebar {
    min-height: 100vh;
    width: 280px;
    background: linear-gradient(135deg, #1a1a2e 0%, #0f0f1a 100%);
    position: fixed;
    z-index: 1000;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 4px 0 15px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow-y: auto;
    border-right: 1px solid rgba(255,255,255,0.05);
}

.sidebar:hover {
    box-shadow: 6px 0 20px rgba(0,0,0,0.3);
}

.sidebar.collapsed {
    width: 80px;
}

/* Header */
.sidebar-header {
    padding: 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    position: sticky;
    top: 0;
    background: inherit;
    z-index: 2;
    backdrop-filter: blur(10px);
}

.logo-container {
    width: 60px;
    height: 60px;
    background: rgba(52, 152, 219, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid rgba(52, 152, 219, 0.2);
}

.logo-container:hover {
    transform: scale(1.05);
    border-color: rgba(52, 152, 219, 0.4);
    box-shadow: 0 0 20px rgba(52, 152, 219, 0.2);
}

.logo-container i {
    font-size: 1.8rem;
    color: #3498db;
    transition: all 0.4s ease;
}

/* User Profile */
.user-profile {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    position: sticky;
    top: 100px;
    background: inherit;
    z-index: 1;
    backdrop-filter: blur(10px);
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 10px;
    background: rgba(255,255,255,0.03);
    transition: all 0.3s ease;
}

.user-info:hover {
    background: rgba(255,255,255,0.05);
    transform: translateX(5px);
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin-right: 1rem;
    transition: all 0.3s ease;
    border: 2px solid rgba(52, 152, 219, 0.3);
}

.user-avatar:hover {
    border-color: #3498db;
    transform: scale(1.1);
}

.user-details h6 {
    color: #fff;
    margin: 0;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.user-details small {
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.user-actions {
    display: flex;
    gap: 0.5rem;
}

.user-actions .btn {
    flex: 1;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}

.user-actions .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.2),
        transparent
    );
    transition: 0.5s;
}

.user-actions .btn:hover::before {
    left: 100%;
}

.user-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}

/* Menu */
.sidebar-menu {
    padding: 1rem 1.5rem;
    padding-bottom: 2rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.8rem 1rem;
    color: rgba(255,255,255,0.8);
    border-radius: 10px;
    margin-bottom: 0.4rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    position: relative;
    overflow: hidden;
    background: rgba(255,255,255,0.02);
}

.nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: #3498db;
    transform: scaleY(0);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0 4px 4px 0;
}

.nav-link:hover::before {
    transform: scaleY(1);
}

.nav-link::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    background: linear-gradient(90deg, rgba(52, 152, 219, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.nav-link:hover::after {
    opacity: 1;
}

.nav-link:hover {
    color: #fff;
    transform: translateX(4px);
    background: rgba(255,255,255,0.05);
}

.nav-link.active {
    background: linear-gradient(90deg, rgba(52, 152, 219, 0.2), rgba(52, 152, 219, 0.05));
    color: #fff;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
}

.nav-link.active::before {
    transform: scaleY(1);
    box-shadow: 0 0 10px rgba(52, 152, 219, 0.5);
}

.nav-link i {
    width: 24px;
    margin-right: 0.8rem;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    color: rgba(255,255,255,0.7);
}

.nav-link:hover i,
.nav-link.active i {
    color: #3498db;
    transform: scale(1.1);
}

.menu-text {
    flex: 1;
    white-space: nowrap;
    opacity: 1;
    transition: all 0.3s ease;
    font-weight: 500;
}

.arrow {
    margin-left: auto;
    transition: all 0.3s ease;
    opacity: 0.7;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
}

.nav-link:hover .arrow {
    opacity: 1;
    background: rgba(52, 152, 219, 0.1);
    transform: rotate(-90deg);
}

.collapse.show + .nav-link .arrow {
    transform: rotate(180deg);
    background: rgba(52, 152, 219, 0.2);
}

/* Submenu */
.submenu {
    padding-left: 3rem;
    margin-top: 0.3rem;
    position: relative;
    overflow: hidden;
}

.submenu::before {
    content: '';
    position: absolute;
    left: 2.3rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, 
        rgba(52, 152, 219, 0.2),
        rgba(52, 152, 219, 0.1),
        transparent
    );
}

.submenu .nav-link {
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    opacity: 0.8;
    transform: translateX(0);
    margin-bottom: 0.3rem;
    background: transparent;
}

.submenu .nav-link:hover {
    opacity: 1;
    transform: translateX(4px);
    background: rgba(255,255,255,0.03);
}

.submenu .nav-link.active {
    opacity: 1;
    background: linear-gradient(90deg, rgba(52, 152, 219, 0.15), transparent);
    font-weight: 500;
}

/* Collapsed State */
.sidebar.collapsed .menu-text,
.sidebar.collapsed .user-details,
.sidebar.collapsed .user-actions,
.sidebar.collapsed .arrow {
    display: none;
}

.sidebar.collapsed .nav-link {
    padding: 0.8rem;
    justify-content: center;
}

.sidebar.collapsed .nav-link i {
    margin: 0;
    font-size: 1.3rem;
}

.sidebar.collapsed .logo-container {
    width: 45px;
    height: 45px;
    border-width: 1px;
}

.sidebar.collapsed .user-avatar {
    width: 35px;
    height: 35px;
    margin: 0 auto;
}

.sidebar.collapsed .nav-link:hover {
    transform: scale(1.1);
    background: rgba(52, 152, 219, 0.1);
}

.sidebar.collapsed .nav-link.active {
    background: rgba(52, 152, 219, 0.2);
}

/* Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.02);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(52, 152, 219, 0.2);
    border-radius: 3px;
    transition: all 0.3s ease;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(52, 152, 219, 0.3);
}

/* Firefox */
.sidebar {
    scrollbar-width: thin;
    scrollbar-color: rgba(52, 152, 219, 0.2) rgba(255,255,255,0.02);
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.nav-item {
    animation: slideIn 0.3s ease forwards;
}

.submenu .nav-item {
    animation: fadeIn 0.3s ease forwards;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    
    // Restaurar estado del sidebar
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        document.getElementById('toggleIcon').classList.remove('fa-chevron-left');
        document.getElementById('toggleIcon').classList.add('fa-chevron-right');
        document.querySelector('.main-content').style.marginLeft = '80px';
    }

    // Restaurar la posición del scroll
    const savedScrollPosition = sessionStorage.getItem('sidebarScrollPosition');
    if (savedScrollPosition) {
        sidebar.scrollTop = parseInt(savedScrollPosition);
    }

    // Guardar la posición del scroll cuando el usuario se desplaza
    sidebar.addEventListener('scroll', function() {
        sessionStorage.setItem('sidebarScrollPosition', sidebar.scrollTop.toString());
    });

    // Mantener submenús abiertos según la ruta actual
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]');
    
    menuItems.forEach(item => {
        const submenu = document.querySelector(item.getAttribute('href'));
        const links = submenu?.querySelectorAll('.nav-link');
        
        links?.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                submenu.classList.add('show');
                // Asegurarse de que el elemento activo sea visible
                setTimeout(() => {
                    link.scrollIntoView({ behavior: 'auto', block: 'center' });
                }, 100);
            }
        });
    });

    // Animación suave para los submenús
    const collapseElements = document.querySelectorAll('.collapse');
    collapseElements.forEach(collapse => {
        collapse.addEventListener('show.bs.collapse', function() {
            const submenuItems = this.querySelectorAll('.nav-link');
            submenuItems.forEach((item, index) => {
                item.style.transitionDelay = `${index * 50}ms`;
                item.style.transform = 'translateX(0)';
                item.style.opacity = '1';
            });
        });

        collapse.addEventListener('hide.bs.collapse', function() {
            const submenuItems = this.querySelectorAll('.nav-link');
            submenuItems.forEach(item => {
                item.style.transitionDelay = '0ms';
                item.style.transform = 'translateX(-10px)';
                item.style.opacity = '0';
            });
        });
    });
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = document.getElementById('toggleIcon');
    const mainContent = document.querySelector('.main-content');
    
    sidebar.classList.toggle('collapsed');
    
    if (sidebar.classList.contains('collapsed')) {
        toggleIcon.classList.remove('fa-chevron-left');
        toggleIcon.classList.add('fa-chevron-right');
        mainContent.style.marginLeft = '80px';
    } else {
        toggleIcon.classList.remove('fa-chevron-right');
        toggleIcon.classList.add('fa-chevron-left');
        mainContent.style.marginLeft = '280px';
    }
    
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}
</script>