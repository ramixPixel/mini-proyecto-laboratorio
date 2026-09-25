<?php
class DigitalSignatureDecorator implements Report
{
    public function __construct(private Report $report) {}

    public function generate(): string
    {
        return $this->report->generate() . ' + firma digital';
    }
}
