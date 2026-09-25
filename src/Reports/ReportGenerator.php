<?php
class ReportGenerator
{
    public function generate(string $contenido): Report
    {
        return new BasicReport($contenido);
    }
}
