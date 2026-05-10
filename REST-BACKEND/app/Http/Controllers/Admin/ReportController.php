<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateManualWeeklyPayoutRequest;
use App\Http\Requests\PayWeeklyPayoutRequest;
use App\Http\Requests\RejectWeeklyPayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\Admin\AdminReportService;
use App\Services\Admin\WeeklyPayoutService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
	protected $reportService;
	protected $weeklyPayoutService;

	public function __construct(AdminReportService $reportService, WeeklyPayoutService $weeklyPayoutService)
	{
		$this->reportService = $reportService;
		$this->weeklyPayoutService = $weeklyPayoutService;
	}

	public function devices_list(Request $request)
	{
		return response()->json([
			'success' => true,
			'data' => \App\Models\Device::select('id', 'device_name')->orderBy('device_name')->get()
		]);
	}

	public function list_transactions(Request $request)
	{
		return response()->json($this->reportService->listTransactions());
	}

	public function sub_pmt_details($sub_id)
	{
		return response()->json($this->reportService->getSubscriptionPaymentDetails($sub_id));
	}

	function logs()
	{
		return response()->json($this->reportService->listLogs());
	}

	public function postback_logs(Request $request)
	{
		return response()->json($this->reportService->getAffiliatePostbackLogs($request));
	}

	function all_offers()
	{
		// DB::enableQueryLog(); ... original had debug stuff mostly commented
		return response()->json($this->reportService->allOffers());
	}

	public function offer_clicks(Request $request, $offer_id, $get_conversions = "N")
	{
		return $this->reportService->getOfferClicks($offer_id, $get_conversions);
	}

	function search_offer_clicks(Request $request)
	{
		return response()->json($this->reportService->searchOfferClicks($request));
	}

	public function click_conversion_report(Request $request)
	{
		return response()->json($this->reportService->getClickConversionReportForAdmin($request));
	}

	public function all_payouts()
	{
		return response()->json($this->reportService->allPayouts());
	}

	public function search_all_payouts(Request $request)
	{
		return response()->json($this->reportService->searchAllPayouts($request));
	}

	public function offer_payouts($oid)
	{
		return response()->json($this->reportService->offerPayouts($oid));
	}

	public function search_offer_payouts(Request $request)
	{
		return response()->json($this->reportService->searchOfferPayouts($request));
	}

	public function affiliate_payouts($aid)
	{
		return response()->json($this->reportService->affiliatePayouts($aid));
	}

	public function search_affiliate_payouts(Request $request)
	{
		return response()->json($this->reportService->searchAffiliatePayouts($request));
	}

	public function get_offers_assigned_to_affiliate($aid)
	{
		return response()->json($this->reportService->getOffersAssignedToAffiliate($aid));
	}

	public function clicks_report(Request $request)
	{
		return response()->json($this->reportService->getClicksReport($request));
	}

	public function conversions_report(Request $request)
	{
		return response()->json($this->reportService->getConversionsReport($request));
	}

	protected function weeklyPayoutsFeatureEnabled(): bool
	{
		return (bool) config('reports.weekly_payouts_management_enabled', false);
	}

	protected function weeklyPayoutsDisabledResponse()
	{
		return response()->json([
			'success' => false,
			'feature' => 'weekly_payouts_management',
			'enabled' => false,
			'message' => 'Weekly payouts management is currently disabled.',
		], 403);
	}

	public function weekly_payouts_feature_status()
	{
		return response()->json([
			'success' => true,
			'feature' => 'weekly_payouts_management',
			'enabled' => $this->weeklyPayoutsFeatureEnabled(),
		]);
	}

	public function weekly_payouts(Request $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		return response()->json($this->weeklyPayoutService->listMonthData($request->all()));
	}

	public function weekly_payouts_stats(Request $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		return response()->json($this->weeklyPayoutService->monthStats($request->all()));
	}

	public function pay_weekly_payouts(PayWeeklyPayoutRequest $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		$result = $this->weeklyPayoutService->payPayouts(
			$request->validated(),
			(int) optional($request->user())->id
		);

		$status = 422;
		if (($result['code'] ?? '') === 'UNAUTHORIZED') {
			$status = 401;
		} elseif (($result['code'] ?? '') === 'NOT_FOUND') {
			$status = 404;
		}

		return response()->json($result, ($result['success'] ?? false) ? 200 : $status);
	}

	public function reject_weekly_payout(RejectWeeklyPayoutRequest $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		$result = $this->weeklyPayoutService->rejectPayout(
			$request->validated(),
			(int) optional($request->user())->id
		);

		$status = 422;
		if (($result['code'] ?? '') === 'UNAUTHORIZED') {
			$status = 401;
		} elseif (($result['code'] ?? '') === 'NOT_FOUND') {
			$status = 404;
		}

		return response()->json($result, ($result['success'] ?? false) ? 200 : $status);
	}

	public function create_manual_weekly_payout(CreateManualWeeklyPayoutRequest $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		$result = $this->weeklyPayoutService->createManualPayout(
			$request->validated(),
			(int) optional($request->user())->id
		);

		$status = (($result['code'] ?? '') === 'UNAUTHORIZED') ? 401 : 422;

		return response()->json($result, ($result['success'] ?? false) ? 200 : $status);
	}

	public function update_conversion_status(Request $request)
	{
		Log::info('update_conversion_status request payload', [
			'request' => $request->all(),
		]);

		$request->validate([
			'id' => 'required|integer|min:1',
			'status' => 'required|in:confirmed,declined',
		]);

		$result = $this->reportService->updateConversionStatus($request->id, $request->status);

		return response()->json($result, $result['success'] ? 200 : 404);
	}

	public function bulk_update_conversion_status(Request $request)
	{
		$request->validate([
			'file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
			'status' => 'nullable|string|in:confirmed,declined,approve,approved,reject,rejected',
		]);

		try {
			$rows = $this->extractConversionRowsFromUploadedFile($request->file('file'));
		} catch (\Throwable $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage(),
				'summary' => [
					'total_rows' => 0,
					'processed' => 0,
					'updated' => 0,
					'failed' => 0,
				],
				'rows' => [],
			], 422);
		}

		foreach($rows as $row){
			Log::info('Row parsed for bulk conversion status update.', ['row' => $row]);
		}

		$forcedStatus = $request->input('status');
		$result = $this->reportService->bulkUpdateConversionStatuses($rows, $forcedStatus);

		$statusCode = ($result['summary']['processed'] ?? 0) > 0 ? 200 : 422;

		return response()->json($result, $statusCode);
	}

	public function download_bulk_conversion_template(Request $request): BinaryFileResponse
	{
		$format = strtolower((string) $request->query('format', 'csv'));

		if (!in_array($format, ['csv', 'xlsx'], true)) {
			$format = 'csv';
		}

		if ($format === 'xlsx') {
			$filePath = $this->buildBulkConversionTemplateXlsx();
			$fileName = 'bulk-conversion-status-template.xlsx';
			$contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		} else {
			$filePath = $this->buildBulkConversionTemplateCsv();
			$fileName = 'bulk-conversion-status-template.csv';
			$contentType = 'text/csv';
		}

		return response()->download($filePath, $fileName, [
			'Content-Type' => $contentType,
		])->deleteFileAfterSend(true);
	}

	private function extractConversionRowsFromUploadedFile($file): array
	{
		$extension = strtolower($file->getClientOriginalExtension());

		if ($extension === 'csv' || $extension === 'txt') {
			return $this->extractRowsFromCsv($file->getRealPath());
		}

		if ($extension === 'xlsx') {
			return $this->extractRowsFromXlsx($file->getRealPath());
		}

		throw new \RuntimeException('Unsupported file type. Only CSV and XLSX are supported.');
	}

	private function extractRowsFromCsv(string $path): array
	{
		$handle = fopen($path, 'r');
		if ($handle === false) {
			throw new \RuntimeException('Unable to read uploaded CSV file.');
		}

		$header = fgetcsv($handle);
		if (!$header || count($header) === 0) {
			fclose($handle);
			throw new \RuntimeException('CSV is empty or missing a header row.');
		}

		$rows = [];
		$headerMap = $this->buildHeaderIndexMap($header);
		[$idIndex, $statusIndex] = $this->resolveRequiredColumnIndexes($headerMap);

		$rowNumber = 1;
		while (($data = fgetcsv($handle)) !== false) {
			$rowNumber++;

			$conversionId = isset($data[$idIndex]) ? trim((string) $data[$idIndex]) : '';
			$status = ($statusIndex !== null && isset($data[$statusIndex])) ? trim((string) $data[$statusIndex]) : null;

			if ($conversionId === '' && ($status === null || $status === '')) {
				continue;
			}

			$rows[] = [
				'row_number' => $rowNumber,
				'conversion_id' => $conversionId,
				'status' => $status,
			];
		}

		fclose($handle);

		return $rows;
	}

	private function extractRowsFromXlsx(string $path): array
	{
		$zip = new \ZipArchive();
		if ($zip->open($path) !== true) {
			throw new \RuntimeException('Unable to open uploaded XLSX file.');
		}

		$worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
		if ($worksheetXml === false) {
			$zip->close();
			throw new \RuntimeException('XLSX file is missing the first worksheet.');
		}

		$sharedStrings = [];
		$sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
		if ($sharedStringsXml !== false) {
			$sharedStringsDoc = simplexml_load_string($sharedStringsXml);
			if ($sharedStringsDoc) {
				foreach ($sharedStringsDoc->si as $item) {
					$value = '';
					if (isset($item->t)) {
						$value = (string) $item->t;
					} elseif (isset($item->r)) {
						foreach ($item->r as $run) {
							$value .= (string) $run->t;
						}
					}
					$sharedStrings[] = $value;
				}
			}
		}

		$sheet = simplexml_load_string($worksheetXml);
		$zip->close();

		if (!$sheet || !isset($sheet->sheetData)) {
			throw new \RuntimeException('XLSX worksheet could not be parsed.');
		}

		$rawRows = [];
		foreach ($sheet->sheetData->row as $row) {
			$rowValues = [];
			foreach ($row->c as $cell) {
				$cellRef = (string) $cell['r'];
				preg_match('/^[A-Z]+/', $cellRef, $matches);
				$columnLetters = $matches[0] ?? 'A';
				$columnIndex = $this->columnLettersToIndex($columnLetters);

				$type = (string) $cell['t'];
				$value = '';
				if ($type === 's') {
					$sharedStringIndex = (int) $cell->v;
					$value = $sharedStrings[$sharedStringIndex] ?? '';
				} elseif ($type === 'inlineStr' && isset($cell->is->t)) {
					$value = (string) $cell->is->t;
				} else {
					$value = isset($cell->v) ? (string) $cell->v : '';
				}

				$rowValues[$columnIndex] = trim($value);
			}

			ksort($rowValues);
			$rawRows[] = $rowValues;
		}

		if (count($rawRows) === 0) {
			throw new \RuntimeException('XLSX is empty or missing a header row.');
		}

		$header = $rawRows[0];
		$headerMap = $this->buildHeaderIndexMap($header);
		[$idIndex, $statusIndex] = $this->resolveRequiredColumnIndexes($headerMap);

		$rows = [];
		for ($i = 1; $i < count($rawRows); $i++) {
			$rowNumber = $i + 1;
			$currentRow = $rawRows[$i];

			$conversionId = isset($currentRow[$idIndex]) ? trim((string) $currentRow[$idIndex]) : '';
			$status = ($statusIndex !== null && isset($currentRow[$statusIndex])) ? trim((string) $currentRow[$statusIndex]) : null;

			if ($conversionId === '' && ($status === null || $status === '')) {
				continue;
			}

			$rows[] = [
				'row_number' => $rowNumber,
				'conversion_id' => $conversionId,
				'status' => $status,
			];
		}

		return $rows;
	}

	private function buildHeaderIndexMap(array $header): array
	{
		$headerMap = [];
		foreach ($header as $index => $value) {
			$key = Str::of((string) $value)->lower()->trim()->replace([' ', '-'], '_')->value();
			if ($key !== '') {
				$headerMap[$key] = (int) $index;
			}
		}

		return $headerMap;
	}

	private function resolveRequiredColumnIndexes(array $headerMap): array
	{
		$idAliases = ['conversion_id', 'id', 'conv_id'];
		$statusAliases = ['status', 'conversion_status', 'action'];

		$idIndex = null;
		foreach ($idAliases as $alias) {
			if (array_key_exists($alias, $headerMap)) {
				$idIndex = $headerMap[$alias];
				break;
			}
		}

		if ($idIndex === null) {
			throw new \RuntimeException('Missing required column. Please include conversion_id or id.');
		}

		$statusIndex = null;
		foreach ($statusAliases as $alias) {
			if (array_key_exists($alias, $headerMap)) {
				$statusIndex = $headerMap[$alias];
				break;
			}
		}

		return [$idIndex, $statusIndex];
	}

	private function columnLettersToIndex(string $letters): int
	{
		$letters = strtoupper($letters);
		$index = 0;

		for ($i = 0; $i < strlen($letters); $i++) {
			$index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
		}

		return $index - 1;
	}

	private function buildBulkConversionTemplateCsv(): string
	{
		$path = tempnam(sys_get_temp_dir(), 'bulk_conv_tpl_');
		if ($path === false) {
			throw new \RuntimeException('Unable to generate CSV template file.');
		}

		$handle = fopen($path, 'w');
		if ($handle === false) {
			@unlink($path);
			throw new \RuntimeException('Unable to write CSV template file.');
		}

		fputcsv($handle, ['conversion_id', 'status']);
		fputcsv($handle, ['101', 'confirmed']);
		fputcsv($handle, ['102', 'declined']);
		fputcsv($handle, ['103', 'approved']);
		fputcsv($handle, ['104', 'rejected']);

		fclose($handle);

		return $path;
	}

	private function buildBulkConversionTemplateXlsx(): string
	{
		$path = tempnam(sys_get_temp_dir(), 'bulk_conv_tpl_');
		if ($path === false) {
			throw new \RuntimeException('Unable to generate XLSX template file.');
		}

		$zip = new \ZipArchive();
		if ($zip->open($path, \ZipArchive::OVERWRITE) !== true) {
			@unlink($path);
			throw new \RuntimeException('Unable to create XLSX archive.');
		}

		$sharedStrings = [
			'conversion_id',
			'status',
			'confirmed',
			'declined',
			'approved',
			'rejected',
		];

		$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
			. '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
			. '<Default Extension="xml" ContentType="application/xml"/>'
			. '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
			. '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
			. '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
			. '</Types>';

		$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
			. '</Relationships>';

		$workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
			. '<sheets><sheet name="Template" sheetId="1" r:id="rId1"/></sheets>'
			. '</workbook>';

		$workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
			. '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
			. '</Relationships>';

		$sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			. '<sheetData>'
			. '<row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c></row>'
			. '<row r="2"><c r="A2"><v>101</v></c><c r="B2" t="s"><v>2</v></c></row>'
			. '<row r="3"><c r="A3"><v>102</v></c><c r="B3" t="s"><v>3</v></c></row>'
			. '<row r="4"><c r="A4"><v>103</v></c><c r="B4" t="s"><v>4</v></c></row>'
			. '<row r="5"><c r="A5"><v>104</v></c><c r="B5" t="s"><v>5</v></c></row>'
			. '</sheetData>'
			. '</worksheet>';

		$sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
		foreach ($sharedStrings as $text) {
			$escaped = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
			$sharedStringsXml .= '<si><t>' . $escaped . '</t></si>';
		}
		$sharedStringsXml .= '</sst>';

		$zip->addFromString('[Content_Types].xml', $contentTypes);
		$zip->addFromString('_rels/.rels', $rels);
		$zip->addFromString('xl/workbook.xml', $workbook);
		$zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
		$zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
		$zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);

		$zip->close();

		return $path;
	}
}
