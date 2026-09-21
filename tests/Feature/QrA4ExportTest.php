<?php

namespace Tests\Feature;

use Tests\TestCase;

class QrA4ExportTest extends TestCase
{
    /**
     * Test routes are properly mapped.
     */
    public function test_qr_a4_routes_exist(): void
    {
        $this->assertTrue(app('router')->getRoutes()->hasNamedRoute('qr.exportPdf'));
        $this->assertTrue(app('router')->getRoutes()->hasNamedRoute('qr.pdfPreview'));
        $this->assertTrue(app('router')->getRoutes()->hasNamedRoute('qr.storeBatch'));
        $this->assertTrue(app('router')->getRoutes()->hasNamedRoute('admin.qr.exportPdf'));
        $this->assertTrue(app('router')->getRoutes()->hasNamedRoute('admin.qr.storeBatch'));
    }

    /**
     * Test A4 PDF view renders with grid structure.
     */
    public function test_a4_pdf_view_renders_grid(): void
    {
        $qrs = [
            [
                'id' => 1,
                'voucher_code' => 'ARM-500-TEST1',
                'batch_name' => 'FESTIVE BATCH',
                'amount' => 500,
                'qr_base64' => base64_encode('<svg></svg>')
            ],
            [
                'id' => 2,
                'voucher_code' => 'ARM-500-TEST2',
                'batch_name' => 'FESTIVE BATCH',
                'amount' => 500,
                'qr_base64' => base64_encode('<svg></svg>')
            ]
        ];

        $view = view('qr.pdf', [
            'qrs' => $qrs,
            'cols' => 10,
            'batchName' => 'FESTIVE BATCH',
            'generatedAt' => '21 Sep 2026, 06:00 PM'
        ]);

        $rendered = $view->render();

        $this->assertStringContainsString('Aarambh Garments', $rendered);
        $this->assertStringContainsString('ARM-500-TEST1', $rendered);
        $this->assertStringContainsString('ARM-500-TEST2', $rendered);
        $this->assertStringContainsString('FESTIVE BA', $rendered);
        $this->assertStringContainsString('data:image/svg+xml;base64,', $rendered);
    }
}
