<?php
/**
 * ============================================================================
 *  GENERACION DE REPORTES
 *  Patron esperado: DECORATOR (estructural)
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     El metodo recibe banderas booleanas para activar agregados.
 *     Con 3 banderas hay 8 combinaciones dentro del mismo metodo.
 *     Con 5 banderas hay 32. El metodo se vuelve imposible de probar.
 *     Ademas, en la llamada nadie entiende que significa generate(true, false, true).
 *
 *  ✅ FORMA CORRECTA
 *     Un contrato Report + decoradores que envuelven al reporte base.
 *     Cada agregado es una clase que se puede combinar en tiempo de ejecucion.
 * ============================================================================
 */

class ReportGenerator
{
    /**
     * ❌ METODO MAL APLICADO: generate()
     *    Sintoma: "boolean trap". El llamador escribe generate(true, false, true)
     *    y seis meses despues nadie recuerda cual bandera era cual.
     *    Viola OCP: agregar "codigo QR" implica un parametro mas y un if mas.
     */
    public function generate(
        string $contenido,
        bool $conFirmaDigital = false,
        bool $conPdf = false,
        bool $conMarcaDeAgua = false
    ): string {
        $reporte = "Reporte: {$contenido}";

        if ($conFirmaDigital) {
            $reporte .= ' + firma digital';
        }

        if ($conPdf) {
            $reporte .= ' + PDF';
        }

        if ($conMarcaDeAgua) {
            $reporte .= ' + marca de agua';
        }

        // ❌ El proximo agregado suma otro parametro y otro if.
        return $reporte;
    }
}

/*
 * ============================================================================
 *  ✅ FORMA CORRECTA — DECORATOR
 * ============================================================================
 *
 * interface Report
 * {
 *     public function generate(): string;
 * }
 *
 * final class BasicReport implements Report
 * {
 *     public function __construct(private string $contenido) {}
 *
 *     public function generate(): string
 *     {
 *         return "Reporte: {$this->contenido}";
 *     }
 * }
 *
 * final class DigitalSignatureDecorator implements Report
 * {
 *     public function __construct(private Report $report) {}   // envuelve
 *
 *     public function generate(): string
 *     {
 *         return $this->report->generate() . ' + firma digital';
 *     }
 * }
 *
 * Uso:
 *   $reporte = new BasicReport('Pedido 1');
 *   $reporte = new DigitalSignatureDecorator($reporte);
 *   $reporte = new PdfReportDecorator($reporte);
 *   echo $reporte->generate();
 *
 * Encadenamiento:
 *   BasicReport -> Signature -> Pdf -> Watermark
 *
 * ----------------------------------------------------------------------------
 * EJERCICIO 4 (TP): implementar PdfReportDecorator y WatermarkDecorator.
 *
 * CONSECUENCIA A JUSTIFICAR EN EL PR: el orden de los decoradores cambia el
 * resultado. Firmar y despues pasar a PDF no es lo mismo que al reves.
 * Como documentan esa restriccion para el resto del equipo?
 * ============================================================================
 */
