<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\Affiliate\AffiliateService;
use App\Models\Conversion;

class AffiliateController extends Controller
{
	public function profile(Request $request, AffiliateService $service)
	{
		return response()->json($service->getProfileData($request->user()));
	}

	public function update_profile(Request $request, AffiliateService $service)
	{
		try {
			return $service->updateProfile($request);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function a_postback_cols(Request $request, AffiliateService $service)
	{
		return response()->json($service->getPostbackColumns(Auth::id(), $request->user()->user_type));
	}

	public function update_a_postback_cols(Request $request, AffiliateService $service)
	{
		try {
			$result = $service->updatePostbackColumns($request->all(), Auth::id());
			return response()->json($result);
		} catch (\Exception $e) {
			Log::error("Postback Update Error: " . $e->getMessage());
			Log::error($e->getTraceAsString());
			return response()->json(['error' => 'Postback columns could not be updated', 'details' => $e->getMessage()], 500);
		}
	}

	/**
	 * Change affiliate account password.
	 */
	public function change_password(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'current_password' => 'required|string',
			'password' => 'required|string|min:6|confirmed',
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		$user = Auth::user();

		if (!Hash::check($request->current_password, $user->password)) {
			return response()->json(['error' => 'Current password does not match.'], 422);
		}

		try {
			$user->password = Hash::make($request->password);
			$user->save();

			return response()->json(['message' => 'Password updated successfully.']);
		} catch (\Exception $e) {
			Log::error('Password change failed: ' . $e->getMessage());
			return response()->json(['error' => 'Failed to update password.'], 500);
		}
	}

	/**
	 * Get payout requests history with server-side pagination.
	 */
	public function payout_history(Request $request)
	{
		$payouts = DB::table('affiliate_weekly_payouts')
			->where('affiliate_id', Auth::id())
			->orderBy('created_at', 'desc')
			->paginate($request->input('limit', 10));

		$aud = DB::table('affiliate_user_details')->where('user_id', Auth::id())->first();
		$threshold = $aud->payout_threshold ?? 50.00;

		// Calculate available balance
		$totalApproved = Conversion::where('affiliate_id', Auth::id())
			->where('status', 'approved')
			->sum('payout_usd');

		$totalRequestedOrPaid = DB::table('affiliate_weekly_payouts')
			->where('affiliate_id', Auth::id())
			->whereIn('status', ['PENDING', 'PROCESSING', 'PAID'])
			->sum('total_amount');

		$totalPending = Conversion::where('affiliate_id', Auth::id())
			->where('status', 'pending')
			->sum('payout_usd');

		$balance = max(0, $totalApproved - $totalRequestedOrPaid);

		return response()->json([
			'payouts' => $payouts,
			'available_balance' => number_format($balance, 2, '.', ''),
			'pending_earnings' => number_format($totalPending, 2, '.', ''),
			'payout_threshold' => number_format($threshold, 2, '.', '')
		]);
	}

	/**
	 * Request a new payout.
	 */
	public function request_payout(Request $request)
	{
		$aud = DB::table('affiliate_user_details')->where('user_id', Auth::id())->first();
		$threshold = $aud->payout_threshold ?? 50.00;

		$totalApproved = Conversion::where('affiliate_id', Auth::id())
			->where('status', 'approved')
			->sum('payout_usd');

		$totalRequestedOrPaid = DB::table('affiliate_weekly_payouts')
			->where('affiliate_id', Auth::id())
			->whereIn('status', ['PENDING', 'PROCESSING', 'PAID'])
			->sum('total_amount');

		$balance = max(0, $totalApproved - $totalRequestedOrPaid);

		if ($balance < $threshold) {
			return response()->json([
				'error' => 'Your available balance ($' . number_format($balance, 2) . ') is below the minimum payout threshold ($' . number_format($threshold, 2) . ').'
			], 422);
		}

		if (empty($aud->payment_method) || empty($aud->payment_details)) {
			return response()->json([
				'error' => 'Please set up your payment method and details in Settings before requesting a payout.'
			], 422);
		}

		try {
			DB::table('affiliate_weekly_payouts')->insert([
				'affiliate_id' => Auth::id(),
				'week_start' => now()->startOfWeek()->toDateString(),
				'week_end' => now()->endOfWeek()->toDateString(),
				'month_key' => now()->format('Y-m'),
				'year' => now()->year,
				'week_number' => now()->weekOfYear,
				'entry_type' => 'Manual',
				'total_amount' => $balance,
				'currency' => 'USD',
				'status' => 'PENDING',
				'notes' => 'Manual payout request from affiliate portal.',
				'created_at' => now(),
				'updated_at' => now()
			]);

			return response()->json(['message' => 'Payout request submitted successfully.']);
		} catch (\Exception $e) {
			Log::error('Payout request failed: ' . $e->getMessage());
			return response()->json(['error' => 'Failed to submit payout request.'], 500);
		}
	}

	/**
	 * Update payment settings.
	 */
	public function update_payout_method(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'payment_method' => 'required|string|max:100',
			'payment_details' => 'required|string',
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		try {
			DB::table('affiliate_user_details')
				->updateOrInsert(
					['user_id' => Auth::id()],
					[
						'payment_method' => $request->payment_method,
						'payment_details' => $request->payment_details,
						'updated_at' => now()
					]
				);

			return response()->json(['message' => 'Payment method updated successfully.']);
		} catch (\Exception $e) {
			Log::error('Payment method update failed: ' . $e->getMessage());
			return response()->json(['error' => 'Failed to update payment method.'], 500);
		}
	}

	/**
	 * Update Telegram Bot notifications settings.
	 */
	public function update_telegram_settings(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'telegram_bot_token' => 'nullable|string|max:255',
			'telegram_chat_id' => 'nullable|string|max:255',
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		try {
			DB::table('affiliate_user_details')
				->updateOrInsert(
					['user_id' => Auth::id()],
					[
						'telegram_bot_token' => $request->telegram_bot_token,
						'telegram_chat_id' => $request->telegram_chat_id,
						'updated_at' => now()
					]
				);

			return response()->json(['message' => 'Telegram settings updated successfully.']);
		} catch (\Exception $e) {
			Log::error('Telegram settings update failed: ' . $e->getMessage());
			return response()->json(['error' => 'Failed to update telegram settings.'], 500);
		}
	}
}


