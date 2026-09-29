<?php

namespace App\Http\Controllers;

use App\Models\CustomerRequest;
use App\Models\ReportExport;
use App\Services\ReportService;
use App\Support\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports): InertiaResponse
    {
        abort_unless(in_array($request->user()?->role, ['admin', 'viewer'], true), 403);
        $filters = $this->filters($request);

        return Inertia::render('Reports/Index', ['filters' => $filters, 'summary' => $reports->summary($filters), 'requests' => $reports->requests($filters)->paginate(25)->withQueryString()->through(fn (CustomerRequest $record): array => ['request_number' => $record->request_number, 'request_date' => $record->request_date?->toDateString(), 'institution_name' => $record->customer->institution_name, 'crm_number' => $record->customer->crm_number, 'request_type' => $record->request_type, 'status' => $record->status])]);
    }

    public function export(Request $request, ReportService $reports, string $format): Response
    {
        abort_unless(in_array($request->user()?->role, ['admin', 'viewer'], true), 403);
        abort_unless(in_array($format, ['excel', 'pdf'], true), 404);
        ReportExport::query()->where('expires_at', '<', now())->each(function (ReportExport $export): void {
            Storage::disk('local')->delete($export->path);
            $export->delete();
        });
        $filters = $this->filters($request);
        $rows = $reports->requests($filters)->limit($format === 'excel' ? 5001 : 1001)->get();
        $max = $format === 'excel' ? 5000 : 1000;
        if ($rows->count() > $max) {
            return response('Persempit filter export.', 422);
        }
        $locale = $request->user()->locale === 'en' ? 'en' : 'id';
        $content = $format === 'excel' ? $this->excel($rows, $locale) : $this->pdf($rows, $locale);
        $extension = $format === 'excel' ? 'xls' : 'pdf';
        $path = 'report-exports/'.Str::uuid().'.'.$extension;
        Storage::disk('local')->put($path, $content);
        $export = ReportExport::create(['user_id' => $request->user()->id, 'format' => $format, 'path' => $path, 'filters' => $filters, 'expires_at' => now()->addDays(7)]);
        Audit::record('report.exported', $export, $request->user()->id, ['format' => $format, 'filters' => $filters]);

        return response($content, 200, ['Content-Type' => $format === 'excel' ? 'application/vnd.ms-excel' : 'application/pdf', 'Content-Disposition' => 'attachment; filename="rfid-report.'.$extension.'"']);
    }

    public function download(Request $request, ReportExport $reportExport): BinaryFileResponse
    {
        abort_unless(in_array($request->user()?->role, ['admin', 'viewer'], true), 403);
        abort_if(Carbon::parse($reportExport->expires_at)->isPast() || ! Storage::disk('local')->exists($reportExport->path), 404);

        return response()->download(Storage::disk('local')->path($reportExport->path), 'rfid-report.'.$reportExport->format);
    }

    /** @param iterable<int, mixed> $rows */
    private function excel(iterable $rows, string $locale): string
    {
        $xml = '<?xml version="1.0"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Requests"><Table>';
        $headers = $locale === 'en' ? ['Request', 'Request date', 'Institution', 'CRM', 'Request type', 'Status'] : ['Nomor pengajuan', 'Tanggal permintaan', 'Institusi', 'CRM', 'Jenis pengajuan', 'Status'];
        $xml .= '<Row>'.implode('', array_map(fn (string $value) => '<Cell><Data ss:Type="String">'.htmlspecialchars($value, ENT_XML1).'</Data></Cell>', $headers)).'</Row>';
        foreach ($rows as $row) {
            $values = [$row->request_number, $row->request_date?->toDateString(), $row->customer?->institution_name, $row->customer?->crm_number, $row->request_type, $row->status];
            $xml .= '<Row>'.implode('', array_map(fn ($v) => '<Cell><Data ss:Type="String">'.htmlspecialchars($this->spreadsheetValue($v), ENT_XML1).'</Data></Cell>', $values)).'</Row>';
        }

        return $xml.'</Table></Worksheet></Workbook>';
    }

    private function spreadsheetValue(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) === 1 ? "'".$value : $value;
    }

    /** @return array<string, mixed> */
    private function filters(Request $request): array
    {
        $filters = $request->validate(['period' => ['nullable', 'in:daily,weekly,monthly,yearly,custom'], 'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'], 'status' => ['nullable', 'in:NEW,UNDER_REVIEW,APPROVED,REJECTED,PROCESSING,COMPLETED'], 'request_type' => ['nullable', 'in:new_card,damaged_card,system_error_card,lost_card']]);
        $today = today();
        if (($filters['period'] ?? 'custom') !== 'custom') {
            $filters['date_to'] = $today->toDateString();
            $filters['date_from'] = match ($filters['period']) {
                'daily' => $today->toDateString(), 'weekly' => $today->copy()->subDays(6)->toDateString(), 'monthly' => $today->copy()->startOfMonth()->toDateString(), 'yearly' => $today->copy()->startOfYear()->toDateString(), default => null,
            };
        }

        return $filters;
    }

    /** @param iterable<int, mixed> $rows */
    private function pdf(iterable $rows, string $locale): string
    {
        $lines = [$locale === 'en' ? 'RFID REPORT' : 'LAPORAN RFID'];
        foreach ($rows as $row) {
            $lines[] = $row->request_number.' | '.$row->request_date?->toDateString().' | '.$row->status;
        }
        $stream = 'BT /F1 10 Tf 40 780 Td '.implode(' ', array_map(fn ($line) => '('.str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line).') Tj 0 -14 Td', array_slice($lines, 0, 55))).' ET';
        $objects = ['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>', '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>', '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>', '<< /Length '.strlen($stream).' >>\nstream\n'.$stream.'\nendstream'];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1).' 0 obj\n'.$object."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= sprintf('%010d 00000 n \n', $offsets[$i]);
        }

        return $pdf."trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
    }
}
