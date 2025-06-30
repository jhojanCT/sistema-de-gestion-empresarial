@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Encabezado del Dashboard -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-flex">
            <a href="{{ route('reportes.ventas') }}" class="btn btn-primary shadow-sm mr-2">
                <i class="fas fa-download fa-sm text-white-50"></i> Generar Reporte
            </a>
            <a href="{{ route('ventas.create') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Venta
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Saldo Actual -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Saldo Actual en Caja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Bs {{ number_format($saldoActual ?? 0, 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-muted mt-2">
                                <strong>Saldo Inicial del Día:</strong><br>
                                Bs {{ number_format($saldoInicial ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas del Día -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ventas del Día</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Bs {{ number_format(($ventasContadoHoy ?? 0) + ($ventasCreditoHoy ?? 0), 2, ',', '.') }}
                            </div>
                            <div class="text-xs mt-2">
                                <span class="badge bg-success text-white">Contado (Afecta Caja):</span> Bs {{ number_format($ventasContadoHoy ?? 0, 2, ',', '.') }}<br>
                                <span class="badge bg-secondary text-white">Crédito (Informativo):</span> Bs {{ number_format($ventasCreditoHoy ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compras del Día -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Compras del Día</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Bs {{ number_format(($comprasContadoHoy ?? 0) + ($comprasCreditoHoy ?? 0), 2, ',', '.') }}
                            </div>
                            <div class="text-xs mt-2">
                                <span class="badge bg-danger text-white">Contado (Afecta Caja):</span> Bs {{ number_format($comprasContadoHoy ?? 0, 2, ',', '.') }}<br>
                                <span class="badge bg-secondary text-white">Crédito (Informativo):</span> Bs {{ number_format($comprasCreditoHoy ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-basket fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos en Stock -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Productos en Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($productosStock ?? 0) }}
                            </div>
                            <div class="text-xs text-danger">
                                {{ $productosStockBajo ?? 0 }} productos con stock bajo
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes Activos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Clientes Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($clientesActivos ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Ventas -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas Mensuales</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Cierres -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimos Cierres</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Saldo Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimosCierres as $cierre)
                                <tr>
                                    <td>{{ $cierre->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $cierre->usuario->name }}</td>
                                    <td>Bs {{ number_format($cierre->saldo_final, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
}

.border-left-primary {
    border-left: 4px solid var(--primary-color);
}

.border-left-success {
    border-left: 4px solid var(--success-color);
}

.border-left-info {
    border-left: 4px solid var(--info-color);
}

.text-gray-300 {
    color: #dddfeb;
}

.text-gray-800 {
    color: #5a5c69;
}

.chart-area {
    position: relative;
    height: 300px;
    width: 100%;
}

.progress {
    height: 0.5rem;
    border-radius: 0.25rem;
    background-color: #f8f9fc;
}

.progress-bar {
    border-radius: 0.25rem;
}

.btn {
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-menu {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fc;
    transform: translateX(5px);
}

.badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
    margin-right: 0.5rem;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de ventas
    var ventasData = @json($ventasMensuales);
    
    // Preparar los datos para el gráfico
    var meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    var ventas = new Array(12).fill(0);
    
    ventasData.forEach(function(item) {
        ventas[item.mes - 1] = item.total;
    });

    // Crear el gráfico
    var ctx = document.getElementById('ventasChart').getContext('2d');
    var ventasChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{
                label: 'Ventas Mensuales',
                data: ventas,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return 'Bs ' + number_format(value);
                        }
                    },
                    gridLines: {
                        color: 'rgb(234, 236, 244)',
                        zeroLineColor: 'rgb(234, 236, 244)',
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: 'rgb(255, 255, 255)',
                bodyFontColor: '#858796',
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': Bs ' + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });
});

// Función para formatear números
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>
@endpush
@endsection
