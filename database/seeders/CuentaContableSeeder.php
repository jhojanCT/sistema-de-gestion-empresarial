<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuentaContable;

class CuentaContableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ACTIVOS
        // 1.1. Activos circulantes     
        CuentaContable::create(['codigo' => '1', 'nombre' => 'ACTIVO', 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'nivel' => 1]);

        CuentaContable::create(['codigo' => '1.1', 'nombre' => 'ACTIVO CORRIENTE', 'cuenta_padre_id' => '1', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '1.1.1', 'nombre' => 'CAJA Y BANCOS', 'cuenta_padre_id' => '1.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.1.1.1', 'nombre' => 'CAJA GENERAL', 'cuenta_padre_id' => '1.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.1.2', 'nombre' => 'CAJA CHICA', 'cuenta_padre_id' => '1.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.1.3', 'nombre' => 'BANCOS - CUENTA CORRIENTE', 'cuenta_padre_id' => '1.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.1.4', 'nombre' => 'BANCOS - CUENTA DE AHORROS', 'cuenta_padre_id' => '1.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.1.5', 'nombre' => 'FONDOS FIJOS', 'cuenta_padre_id' => '1.1.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.1.2', 'nombre' => 'INVERSIONES TEMPORALES', 'cuenta_padre_id' => '1.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.1.2.1', 'nombre' => 'DEPÓSITOS A PLAZO FIJO', 'cuenta_padre_id' => '1.1.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.2.2', 'nombre' => 'FONDOS MUTUOS', 'cuenta_padre_id' => '1.1.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.1.3', 'nombre' => 'CUENTAS POR COBRAR', 'cuenta_padre_id' => '1.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.1.3.1', 'nombre' => 'CLIENTES', 'cuenta_padre_id' => '1.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.3.2', 'nombre' => 'CUENTAS POR COBRAR A EMPLEADOS', 'cuenta_padre_id' => '1.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.3.3', 'nombre' => 'CUENTAS POR COBRAR A SOCIOS', 'cuenta_padre_id' => '1.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.3.4', 'nombre' => 'ANTICIPOS A PROVEEDORES', 'cuenta_padre_id' => '1.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.3.5', 'nombre' => 'PROVISION PARA CUENTAS INCOBRABLES', 'cuenta_padre_id' => '1.1.3', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.1.4', 'nombre' => 'INVENTARIOS', 'cuenta_padre_id' => '1.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.1.4.1', 'nombre' => 'MATERIAS PRIMAS', 'cuenta_padre_id' => '1.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.4.2', 'nombre' => 'PRODUCTOS EN PROCESO', 'cuenta_padre_id' => '1.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.4.3', 'nombre' => 'PRODUCTOS TERMINADOS', 'cuenta_padre_id' => '1.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.4.4', 'nombre' => 'MERCADERÍAS', 'cuenta_padre_id' => '1.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.4.5', 'nombre' => 'MATERIALES Y SUMINISTROS', 'cuenta_padre_id' => '1.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.4.6', 'nombre' => 'PROVISION PARA OBSOLESCENCIA', 'cuenta_padre_id' => '1.1.4', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.1.5', 'nombre' => 'GASTOS PAGADOS POR ANTICIPADO', 'cuenta_padre_id' => '1.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.1.5.1', 'nombre' => 'SEGUROS PAGADOS POR ANTICIPADO', 'cuenta_padre_id' => '1.1.5', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.5.2', 'nombre' => 'ARRENDAMIENTOS PAGADOS POR ANTICIPADO', 'cuenta_padre_id' => '1.1.5', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.5.3', 'nombre' => 'INTERESES PAGADOS POR ANTICIPADO', 'cuenta_padre_id' => '1.1.5', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.5.4', 'nombre' => 'IMPUESTOS PAGADOS POR ANTICIPADO', 'cuenta_padre_id' => '1.1.5', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.1.6', 'nombre' => 'OTROS ACTIVOS CORRIENTES', 'cuenta_padre_id' => '1.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.1.6.1', 'nombre' => 'IVA CREDITO FISCAL', 'cuenta_padre_id' => '1.1.6', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.6.2', 'nombre' => 'RETENCIONES A FAVOR', 'cuenta_padre_id' => '1.1.6', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.6.3', 'nombre' => 'DEPÓSITOS EN GARANTÍA', 'cuenta_padre_id' => '1.1.6', 'nivel' => 4]);

        // RETENCIONES SUFRIDAS (a favor de la empresa)
        CuentaContable::create(['codigo' => '1.1.7', 'nombre' => 'RETENCIONES SUFRIDAS', 'cuenta_padre_id' => '1.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.1.7.1', 'nombre' => 'RETENCIÓN IVA SUFRIDA', 'cuenta_padre_id' => '1.1.7', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.1.7.2', 'nombre' => 'RETENCIÓN IUE/RENTA SUFRIDA', 'cuenta_padre_id' => '1.1.7', 'nivel' => 4]);
        
        // 1.2. Activos no corrientes
        CuentaContable::create(['codigo' => '1.2', 'nombre' => 'ACTIVO NO CORRIENTE', 'cuenta_padre_id' => '1', 'nivel' => 2]);
        
        CuentaContable::create(['codigo' => '1.2.1', 'nombre' => 'PROPIEDAD, PLANTA Y EQUIPO', 'cuenta_padre_id' => '1.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.2.1.1', 'nombre' => 'TERRENOS', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.1.2', 'nombre' => 'EDIFICIOS', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.1.3', 'nombre' => 'MAQUINARIA Y EQUIPO', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.1.4', 'nombre' => 'EQUIPO DE COMPUTACIÓN', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.1.5', 'nombre' => 'MUEBLES Y ENSERES', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.1.6', 'nombre' => 'EQUIPO DE TRANSPORTE', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.1.7', 'nombre' => 'HERRAMIENTAS', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.1.8', 'nombre' => 'CONSTRUCCIONES EN CURSO', 'cuenta_padre_id' => '1.2.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.2.2', 'nombre' => 'DEPRECIACIÓN ACUMULADA', 'cuenta_padre_id' => '1.2', 'naturaleza' => 'ACREEDORA', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.2.2.1', 'nombre' => 'DEPRECIACIÓN ACUMULADA EDIFICIOS', 'cuenta_padre_id' => '1.2.2', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.2.2', 'nombre' => 'DEPRECIACIÓN ACUMULADA MAQUINARIA', 'cuenta_padre_id' => '1.2.2', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.2.3', 'nombre' => 'DEPRECIACIÓN ACUMULADA EQUIPO', 'cuenta_padre_id' => '1.2.2', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.2.4', 'nombre' => 'DEPRECIACIÓN ACUMULADA VEHÍCULOS', 'cuenta_padre_id' => '1.2.2', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.2.3', 'nombre' => 'ACTIVOS INTANGIBLES', 'cuenta_padre_id' => '1.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.2.3.1', 'nombre' => 'MARCAS Y PATENTES', 'cuenta_padre_id' => '1.2.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.3.2', 'nombre' => 'LICENCIAS', 'cuenta_padre_id' => '1.2.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.3.3', 'nombre' => 'SOFTWARE', 'cuenta_padre_id' => '1.2.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.3.4', 'nombre' => 'GASTOS DE ORGANIZACIÓN', 'cuenta_padre_id' => '1.2.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.3.5', 'nombre' => 'AMORTIZACIÓN ACUMULADA INTANGIBLES', 'cuenta_padre_id' => '1.2.3', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.2.4', 'nombre' => 'INVERSIONES A LARGO PLAZO', 'cuenta_padre_id' => '1.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.2.4.1', 'nombre' => 'ACCIONES Y PARTICIPACIONES', 'cuenta_padre_id' => '1.2.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.4.2', 'nombre' => 'BONOS Y VALORES', 'cuenta_padre_id' => '1.2.4', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '1.2.5', 'nombre' => 'OTROS ACTIVOS NO CORRIENTES', 'cuenta_padre_id' => '1.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '1.2.5.1', 'nombre' => 'DEPÓSITOS EN GARANTÍA A LARGO PLAZO', 'cuenta_padre_id' => '1.2.5', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '1.2.5.2', 'nombre' => 'PRÉSTAMOS A SOCIOS Y EMPLEADOS', 'cuenta_padre_id' => '1.2.5', 'nivel' => 4]);

        // 2. PASIVOS        
        CuentaContable::create(['codigo' => '2', 'nombre' => 'PASIVO', 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'nivel' => 1]);

        // 2.1. Pasivos corrientes
        CuentaContable::create(['codigo' => '2.1', 'nombre' => 'PASIVO CORRIENTE', 'cuenta_padre_id' => '2', 'nivel' => 2]);
        
        CuentaContable::create(['codigo' => '2.1.1', 'nombre' => 'CUENTAS POR PAGAR', 'cuenta_padre_id' => '2.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.1.1.1', 'nombre' => 'PROVEEDORES NACIONALES', 'cuenta_padre_id' => '2.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.1.2', 'nombre' => 'PROVEEDORES EXTRANJEROS', 'cuenta_padre_id' => '2.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.1.3', 'nombre' => 'CUENTAS POR PAGAR A SOCIOS', 'cuenta_padre_id' => '2.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.1.4', 'nombre' => 'ANTICIPOS DE CLIENTES', 'cuenta_padre_id' => '2.1.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.1.2', 'nombre' => 'OBLIGACIONES FINANCIERAS', 'cuenta_padre_id' => '2.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.1.2.1', 'nombre' => 'PRÉSTAMOS BANCARIOS', 'cuenta_padre_id' => '2.1.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.2.2', 'nombre' => 'SOBREGIROS BANCARIOS', 'cuenta_padre_id' => '2.1.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.2.3', 'nombre' => 'PARTES RELACIONADAS', 'cuenta_padre_id' => '2.1.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.1.3', 'nombre' => 'IMPUESTOS POR PAGAR', 'cuenta_padre_id' => '2.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.1.3.1', 'nombre' => 'IVA POR PAGAR', 'cuenta_padre_id' => '2.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.3.2', 'nombre' => 'RETENCIONES POR PAGAR', 'cuenta_padre_id' => '2.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.3.3', 'nombre' => 'IMPUESTO A LA RENTA POR PAGAR', 'cuenta_padre_id' => '2.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.3.4', 'nombre' => 'IMPUESTOS MUNICIPALES', 'cuenta_padre_id' => '2.1.3', 'nivel' => 4]);

        CuentaContable::create(['codigo' => '2.1.3.5', 'nombre' => 'IUE POR PAGAR', 'cuenta_padre_id' => '2.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.3.6', 'nombre' => 'IT POR PAGAR', 'cuenta_padre_id' => '2.1.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.3.7', 'nombre' => 'MULTAS POR PAGAR', 'cuenta_padre_id' => '2.1.3', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.1.4', 'nombre' => 'REMUNERACIONES POR PAGAR', 'cuenta_padre_id' => '2.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.1.4.1', 'nombre' => 'SUELDOS POR PAGAR', 'cuenta_padre_id' => '2.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.4.2', 'nombre' => 'VACACIONES POR PAGAR', 'cuenta_padre_id' => '2.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.4.3', 'nombre' => 'BONIFICACIONES POR PAGAR', 'cuenta_padre_id' => '2.1.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.4.4', 'nombre' => 'APORTES PATRONALES POR PAGAR', 'cuenta_padre_id' => '2.1.4', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.1.5', 'nombre' => 'GASTOS ACUMULADOS', 'cuenta_padre_id' => '2.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.1.5.1', 'nombre' => 'INTERESES POR PAGAR', 'cuenta_padre_id' => '2.1.5', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.5.2', 'nombre' => 'SERVICIOS PÚBLICOS POR PAGAR', 'cuenta_padre_id' => '2.1.5', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.5.3', 'nombre' => 'HONORARIOS POR PAGAR', 'cuenta_padre_id' => '2.1.5', 'nivel' => 4]);
        // RETENCIONES PRACTICADAS (como agente de retención)
        CuentaContable::create(['codigo' => '2.1.6', 'nombre' => 'RETENCIONES PRACTICADAS', 'cuenta_padre_id' => '2.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.1.6.1', 'nombre' => 'RETENCIÓN IVA', 'cuenta_padre_id' => '2.1.6', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.6.2', 'nombre' => 'RETENCIÓN IUE/RENTA', 'cuenta_padre_id' => '2.1.6', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.6.3', 'nombre' => 'RETENCIÓN IT', 'cuenta_padre_id' => '2.1.6', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.1.6.4', 'nombre' => 'RETENCIÓN RC-IVA', 'cuenta_padre_id' => '2.1.6', 'nivel' => 4]); // Régimen Complementario
        
        // 2.2. Pasivos no corrientes
        CuentaContable::create(['codigo' => '2.2', 'nombre' => 'PASIVO NO CORRIENTE', 'cuenta_padre_id' => '2', 'nivel' => 2]);
        
        CuentaContable::create(['codigo' => '2.2.1', 'nombre' => 'PRÉSTAMOS A LARGO PLAZO', 'cuenta_padre_id' => '2.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.2.1.1', 'nombre' => 'BANCOS NACIONALES', 'cuenta_padre_id' => '2.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.2.1.2', 'nombre' => 'BANCOS EXTRANJEROS', 'cuenta_padre_id' => '2.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.2.1.3', 'nombre' => 'PARTES RELACIONADAS', 'cuenta_padre_id' => '2.2.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.2.2', 'nombre' => 'BONOS POR PAGAR', 'cuenta_padre_id' => '2.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.2.2.1', 'nombre' => 'BONOS CONVERTIBLES', 'cuenta_padre_id' => '2.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.2.2.2', 'nombre' => 'BONOS NO CONVERTIBLES', 'cuenta_padre_id' => '2.2.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.2.3', 'nombre' => 'OBLIGACIONES LABORALES', 'cuenta_padre_id' => '2.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.2.3.1', 'nombre' => 'INDEMNIZACIONES POR PAGAR', 'cuenta_padre_id' => '2.2.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.2.3.2', 'nombre' => 'PENSIONES POR PAGAR', 'cuenta_padre_id' => '2.2.3', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.2.4', 'nombre' => 'IMPUESTOS DIFERIDOS', 'cuenta_padre_id' => '2.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.2.4.1', 'nombre' => 'IMPUESTO A LA RENTA DIFERIDO', 'cuenta_padre_id' => '2.2.4', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '2.2.5', 'nombre' => 'OTROS PASIVOS NO CORRIENTES', 'cuenta_padre_id' => '2.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '2.2.5.1', 'nombre' => 'ARRENDAMIENTOS FINANCIEROS', 'cuenta_padre_id' => '2.2.5', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '2.2.5.2', 'nombre' => 'GARANTÍAS A LARGO PLAZO', 'cuenta_padre_id' => '2.2.5', 'nivel' => 4]);

        // 3. PATRIMONIO 
        CuentaContable::create(['codigo' => '3', 'nombre' => 'PATRIMONIO', 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'nivel' => 1]);
        
        CuentaContable::create(['codigo' => '3.1', 'nombre' => 'CAPITAL SOCIAL', 'cuenta_padre_id' => '3', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '3.1.1', 'nombre' => 'CAPITAL SUSCRITO', 'cuenta_padre_id' => '3.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.1.2', 'nombre' => 'CAPITAL PAGADO', 'cuenta_padre_id' => '3.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.1.3', 'nombre' => 'APORTES PARA FUTUROS AUMENTOS DE CAPITAL', 'cuenta_padre_id' => '3.1', 'nivel' => 3]);
        
        CuentaContable::create(['codigo' => '3.2', 'nombre' => 'RESERVAS', 'cuenta_padre_id' => '3', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '3.2.1', 'nombre' => 'RESERVA LEGAL', 'cuenta_padre_id' => '3.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.2.2', 'nombre' => 'RESERVAS ESTATUTARIAS', 'cuenta_padre_id' => '3.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.2.3', 'nombre' => 'RESERVAS VOLUNTARIAS', 'cuenta_padre_id' => '3.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.2.4', 'nombre' => 'RESERVA POR REVALUACIÓN', 'cuenta_padre_id' => '3.2', 'nivel' => 3]);
        
        CuentaContable::create(['codigo' => '3.3', 'nombre' => 'RESULTADOS ACUMULADOS', 'cuenta_padre_id' => '3', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '3.3.1', 'nombre' => 'UTILIDADES ACUMULADAS', 'cuenta_padre_id' => '3.3', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.3.2', 'nombre' => 'PÉRDIDAS ACUMULADAS', 'cuenta_padre_id' => '3.3', 'nivel' => 3]);
        
        CuentaContable::create(['codigo' => '3.4', 'nombre' => 'RESULTADO DEL EJERCICIO', 'cuenta_padre_id' => '3', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '3.4.1', 'nombre' => 'UTILIDAD DEL EJERCICIO', 'cuenta_padre_id' => '3.4', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.4.2', 'nombre' => 'PÉRDIDA DEL EJERCICIO', 'cuenta_padre_id' => '3.4', 'nivel' => 3]);
        
        CuentaContable::create(['codigo' => '3.5', 'nombre' => 'AJUSTES AL PATRIMONIO', 'cuenta_padre_id' => '3', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '3.5.1', 'nombre' => 'REVALUACIÓN DE ACTIVOS', 'cuenta_padre_id' => '3.5', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '3.5.2', 'nombre' => 'DIFERENCIAS DE CAMBIO', 'cuenta_padre_id' => '3.5', 'nivel' => 3]);

        // 4. INGRESOS
        CuentaContable::create(['codigo' => '4', 'nombre' => 'INGRESOS', 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'nivel' => 1]);
        
        CuentaContable::create(['codigo' => '4.1', 'nombre' => 'VENTAS', 'cuenta_padre_id' => '4', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '4.1.1', 'nombre' => 'VENTAS DE PRODUCTOS', 'cuenta_padre_id' => '4.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '4.1.1.1', 'nombre' => 'VENTAS NACIONALES', 'cuenta_padre_id' => '4.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.1.1.2', 'nombre' => 'VENTAS DE EXPORTACIÓN', 'cuenta_padre_id' => '4.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.1.1.3', 'nombre' => 'DEVOLUCIONES EN VENTAS', 'cuenta_padre_id' => '4.1.1', 'naturaleza' => 'DEUDORA', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.1.1.4', 'nombre' => 'DESCUENTOS EN VENTAS', 'cuenta_padre_id' => '4.1.1', 'naturaleza' => 'DEUDORA', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '4.1.2', 'nombre' => 'VENTAS DE SERVICIOS', 'cuenta_padre_id' => '4.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '4.1.2.1', 'nombre' => 'SERVICIOS PROFESIONALES', 'cuenta_padre_id' => '4.1.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.1.2.2', 'nombre' => 'SERVICIOS TÉCNICOS', 'cuenta_padre_id' => '4.1.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.1.2.3', 'nombre' => 'SERVICIOS DE MANTENIMIENTO', 'cuenta_padre_id' => '4.1.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '4.2', 'nombre' => 'OTROS INGRESOS', 'cuenta_padre_id' => '4', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '4.2.1', 'nombre' => 'INGRESOS FINANCIEROS', 'cuenta_padre_id' => '4.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '4.2.1.1', 'nombre' => 'INTERESES GANADOS', 'cuenta_padre_id' => '4.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.2.1.2', 'nombre' => 'GANANCIAS POR DIFERENCIA DE CAMBIO', 'cuenta_padre_id' => '4.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.2.1.3', 'nombre' => 'DIVIDENDOS RECIBIDOS', 'cuenta_padre_id' => '4.2.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '4.2.2', 'nombre' => 'INGRESOS POR ARRENDAMIENTOS', 'cuenta_padre_id' => '4.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '4.2.2.1', 'nombre' => 'ARRENDAMIENTO DE INMUEBLES', 'cuenta_padre_id' => '4.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.2.2.2', 'nombre' => 'ARRENDAMIENTO DE EQUIPOS', 'cuenta_padre_id' => '4.2.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '4.2.3', 'nombre' => 'INGRESOS EXTRAORDINARIOS', 'cuenta_padre_id' => '4.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '4.2.3.1', 'nombre' => 'GANANCIAS POR VENTA DE ACTIVOS', 'cuenta_padre_id' => '4.2.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.2.3.2', 'nombre' => 'DONACIONES RECIBIDAS', 'cuenta_padre_id' => '4.2.3', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '4.2.4', 'nombre' => 'SUBSIDIOS', 'cuenta_padre_id' => '4.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '4.2.4.1', 'nombre' => 'SUBSIDIOS GUBERNAMENTALES', 'cuenta_padre_id' => '4.2.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '4.2.4.2', 'nombre' => 'SUBSIDIOS NO GUBERNAMENTALES', 'cuenta_padre_id' => '4.2.4', 'nivel' => 4]);

        // 5. EGRESOS
        CuentaContable::create(['codigo' => '5', 'nombre' => 'EGRESOS', 'tipo' => 'EGRESO', 'naturaleza' => 'DEUDORA', 'nivel' => 1]);
        
        // 5.1. Costo de ventas
        CuentaContable::create(['codigo' => '5.1', 'nombre' => 'COSTO DE VENTAS', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.1.1', 'nombre' => 'COSTO DE PRODUCTOS VENDIDOS', 'cuenta_padre_id' => '5.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.1.1.1', 'nombre' => 'MATERIAS PRIMAS', 'cuenta_padre_id' => '5.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.1.1.2', 'nombre' => 'MANO DE OBRA DIRECTA', 'cuenta_padre_id' => '5.1.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.1.1.3', 'nombre' => 'COSTOS INDIRECTOS DE FABRICACIÓN', 'cuenta_padre_id' => '5.1.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.1.2', 'nombre' => 'COSTO DE SERVICIOS PRESTADOS', 'cuenta_padre_id' => '5.1', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.1.2.1', 'nombre' => 'MATERIALES PARA SERVICIOS', 'cuenta_padre_id' => '5.1.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.1.2.2', 'nombre' => 'MANO DE OBRA', 'cuenta_padre_id' => '5.1.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.1.2.3', 'nombre' => 'COSTOS ASOCIADOS A SERVICIOS', 'cuenta_padre_id' => '5.1.2', 'nivel' => 4]);
        
        // 5.2. Gastos de administración
        CuentaContable::create(['codigo' => '5.2', 'nombre' => 'GASTOS DE ADMINISTRACIÓN', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.2.1', 'nombre' => 'GASTOS DE PERSONAL', 'cuenta_padre_id' => '5.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.2.1.1', 'nombre' => 'SALARIOS', 'cuenta_padre_id' => '5.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.1.2', 'nombre' => 'BONIFICACIONES', 'cuenta_padre_id' => '5.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.1.3', 'nombre' => 'COMISIONES', 'cuenta_padre_id' => '5.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.1.4', 'nombre' => 'APORTES PATRONALES', 'cuenta_padre_id' => '5.2.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.1.5', 'nombre' => 'BENEFICIOS SOCIALES', 'cuenta_padre_id' => '5.2.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.2.2', 'nombre' => 'GASTOS GENERALES', 'cuenta_padre_id' => '5.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.2.2.1', 'nombre' => 'ALQUILERES', 'cuenta_padre_id' => '5.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.2.2', 'nombre' => 'SERVICIOS PÚBLICOS', 'cuenta_padre_id' => '5.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.2.3', 'nombre' => 'MANTENIMIENTO Y REPARACIONES', 'cuenta_padre_id' => '5.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.2.4', 'nombre' => 'SEGUROS', 'cuenta_padre_id' => '5.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.2.5', 'nombre' => 'GASTOS DE OFICINA', 'cuenta_padre_id' => '5.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.2.6', 'nombre' => 'GASTOS DE VIAJE', 'cuenta_padre_id' => '5.2.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.2.7', 'nombre' => 'GASTOS DE REPRESENTACIÓN', 'cuenta_padre_id' => '5.2.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.2.3', 'nombre' => 'GASTOS TRIBUTARIOS', 'cuenta_padre_id' => '5.2', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.2.3.1', 'nombre' => 'IMPUESTOS MUNICIPALES', 'cuenta_padre_id' => '5.2.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.2.3.2', 'nombre' => 'LICENCIAS Y PERMISOS', 'cuenta_padre_id' => '5.2.3', 'nivel' => 4]);
        
        // 5.3. Gastos de ventas
        CuentaContable::create(['codigo' => '5.3', 'nombre' => 'GASTOS DE VENTAS', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.3.1', 'nombre' => 'PUBLICIDAD Y PROMOCIÓN', 'cuenta_padre_id' => '5.3', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.3.1.1', 'nombre' => 'MEDIOS PUBLICITARIOS', 'cuenta_padre_id' => '5.3.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.3.1.2', 'nombre' => 'MATERIAL PROMOCIONAL', 'cuenta_padre_id' => '5.3.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.3.2', 'nombre' => 'COMISIONES DE VENTAS', 'cuenta_padre_id' => '5.3', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.3.2.1', 'nombre' => 'COMISIONES A VENDEDORES', 'cuenta_padre_id' => '5.3.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.3.3', 'nombre' => 'GASTOS DE DISTRIBUCIÓN', 'cuenta_padre_id' => '5.3', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.3.3.1', 'nombre' => 'FLETES Y ACARREOS', 'cuenta_padre_id' => '5.3.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.3.3.2', 'nombre' => 'ALMACENAMIENTO', 'cuenta_padre_id' => '5.3.3', 'nivel' => 4]);
        
        // 5.4. Gastos financieros
        CuentaContable::create(['codigo' => '5.4', 'nombre' => 'GASTOS FINANCIEROS', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.4.1', 'nombre' => 'INTERESES', 'cuenta_padre_id' => '5.4', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.4.1.1', 'nombre' => 'INTERESES BANCARIOS', 'cuenta_padre_id' => '5.4.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.4.1.2', 'nombre' => 'INTERESES POR PRÉSTAMOS', 'cuenta_padre_id' => '5.4.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.4.2', 'nombre' => 'COMISIONES BANCARIAS', 'cuenta_padre_id' => '5.4', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.4.2.1', 'nombre' => 'COMISIONES POR TARJETAS', 'cuenta_padre_id' => '5.4.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.4.2.2', 'nombre' => 'COMISIONES POR TRANSFERENCIAS', 'cuenta_padre_id' => '5.4.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.4.3', 'nombre' => 'PÉRDIDAS POR DIFERENCIA DE CAMBIO', 'cuenta_padre_id' => '5.4', 'nivel' => 3]);
        
        // 5.5. Otros gastos
        CuentaContable::create(['codigo' => '5.5', 'nombre' => 'OTROS GASTOS', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.5.1', 'nombre' => 'GASTOS EXTRAORDINARIOS', 'cuenta_padre_id' => '5.5', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.5.1.1', 'nombre' => 'PÉRDIDAS POR SINIESTROS', 'cuenta_padre_id' => '5.5.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.5.1.2', 'nombre' => 'PÉRDIDAS POR VENTA DE ACTIVOS', 'cuenta_padre_id' => '5.5.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.5.2', 'nombre' => 'GASTOS POR IMPUESTOS', 'cuenta_padre_id' => '5.5', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.5.2.1', 'nombre' => 'IMPUESTO A LA RENTA (IUE)', 'cuenta_padre_id' => '5.5.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.5.2.2', 'nombre' => 'IMPUESTO A LAS TRANSACCIONES (IT)', 'cuenta_padre_id' => '5.5.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.5.2.3', 'nombre' => 'MULTAS TRIBUTARIAS', 'cuenta_padre_id' => '5.5.2', 'nivel' => 4]);
        // GASTOS POR IMPUESTOS (IUE, IT, multas)

        CuentaContable::create(['codigo' => '5.5.3', 'nombre' => 'GASTOS POR SEGUROS', 'cuenta_padre_id' => '5.5', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.5.3.1', 'nombre' => 'SEGUROS DE VIDA', 'cuenta_padre_id' => '5.5.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.5.3.2', 'nombre' => 'SEGUROS DE ACCIDENTES', 'cuenta_padre_id' => '5.5.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.5.3.3', 'nombre' => 'SEGUROS DE ACCIDENTES DE TRABAJO', 'cuenta_padre_id' => '5.5.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.5.3.4', 'nombre' => 'SEGUROS DE ACCIDENTES DE TRABAJO', 'cuenta_padre_id' => '5.5.3', 'nivel' => 4]);
        
        // 5.6. Compras
        CuentaContable::create(['codigo' => '5.6', 'nombre' => 'COMPRAS', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.6.1', 'nombre' => 'COMPRA DE MERCADERÍAS', 'cuenta_padre_id' => '5.6', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.6.1.1', 'nombre' => 'COMPRAS NACIONALES', 'cuenta_padre_id' => '5.6.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.1.2', 'nombre' => 'COMPRAS DE IMPORTACIÓN', 'cuenta_padre_id' => '5.6.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.1.3', 'nombre' => 'DEVOLUCIONES EN COMPRAS', 'cuenta_padre_id' => '5.6.1', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.1.4', 'nombre' => 'DESCUENTOS EN COMPRAS', 'cuenta_padre_id' => '5.6.1', 'naturaleza' => 'ACREEDORA', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.6.2', 'nombre' => 'COMPRA DE MATERIA PRIMA', 'cuenta_padre_id' => '5.6', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.6.2.1', 'nombre' => 'COMPRAS NACIONALES', 'cuenta_padre_id' => '5.6.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.2.2', 'nombre' => 'COMPRAS DE IMPORTACIÓN', 'cuenta_padre_id' => '5.6.2', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.6.3', 'nombre' => 'COMPRA DE INSUMOS Y SUMINISTROS', 'cuenta_padre_id' => '5.6', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.6.3.1', 'nombre' => 'MATERIALES DE OFICINA', 'cuenta_padre_id' => '5.6.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.3.2', 'nombre' => 'MATERIALES DE LIMPIEZA', 'cuenta_padre_id' => '5.6.3', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.3.3', 'nombre' => 'COMBUSTIBLES Y LUBRICANTES', 'cuenta_padre_id' => '5.6.3', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.6.4', 'nombre' => 'GASTOS DE ADUANA Y TRANSPORTE', 'cuenta_padre_id' => '5.6', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.6.4.1', 'nombre' => 'DERECHOS DE IMPORTACIÓN', 'cuenta_padre_id' => '5.6.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.4.2', 'nombre' => 'FLETES INTERNACIONALES', 'cuenta_padre_id' => '5.6.4', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.6.4.3', 'nombre' => 'SEGUROS DE TRANSPORTE', 'cuenta_padre_id' => '5.6.4', 'nivel' => 4]);
        
        // 5.7. Depreciación y amortización
        CuentaContable::create(['codigo' => '5.7', 'nombre' => 'DEPRECIACIÓN Y AMORTIZACIÓN', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.7.1', 'nombre' => 'DEPRECIACIÓN DE ACTIVOS FIJOS', 'cuenta_padre_id' => '5.7', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.7.1.1', 'nombre' => 'DEPRECIACIÓN EDIFICIOS', 'cuenta_padre_id' => '5.7.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.7.1.2', 'nombre' => 'DEPRECIACIÓN MAQUINARIA', 'cuenta_padre_id' => '5.7.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.7.1.3', 'nombre' => 'DEPRECIACIÓN EQUIPOS', 'cuenta_padre_id' => '5.7.1', 'nivel' => 4]);
        
        CuentaContable::create(['codigo' => '5.7.2', 'nombre' => 'AMORTIZACIÓN DE INTANGIBLES', 'cuenta_padre_id' => '5.7', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.7.2.1', 'nombre' => 'AMORTIZACIÓN MARCAS Y PATENTES', 'cuenta_padre_id' => '5.7.2', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.7.2.2', 'nombre' => 'AMORTIZACIÓN SOFTWARE', 'cuenta_padre_id' => '5.7.2', 'nivel' => 4]);

        // GASTOS SIN SOPORTE FISCAL
        CuentaContable::create(['codigo' => '5.8', 'nombre' => 'GASTOS SIN FACTURA', 'cuenta_padre_id' => '5', 'nivel' => 2]);
        CuentaContable::create(['codigo' => '5.8.1', 'nombre' => 'GASTOS NO DEDUCIBLES', 'cuenta_padre_id' => '5.8', 'nivel' => 3]);
        CuentaContable::create(['codigo' => '5.8.1.1', 'nombre' => 'COMPRAS SIN FACTURA', 'cuenta_padre_id' => '5.8.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.8.1.2', 'nombre' => 'SERVICIOS SIN FACTURA', 'cuenta_padre_id' => '5.8.1', 'nivel' => 4]);
        CuentaContable::create(['codigo' => '5.8.1.3', 'nombre' => 'GASTOS PERSONALES NO DEDUCIBLES', 'cuenta_padre_id' => '5.8.1', 'nivel' => 4]);


    }
}