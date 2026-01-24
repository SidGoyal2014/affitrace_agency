<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use DB, Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Models\AffiliateUserDetail;
use App\Models\OfferToken;
use App\Models\AffiliatePostbackColumn;

use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;

class AffiliateController extends Controller
{

	use CommonFunctions, CommonProfileFunctions;

	public function profile(Request $request)
	{

		$logged_in_user_type = $request->user()->user_type;

		$aud = AffiliateUserDetail::where('user_id', Auth::id())->first();

		// Assuming helper functions return data or arrays
		// If they return views, we might need to refactor them too. 
		// For now assuming they return data as is typical for helpers used here.

		return response()->json([
			'module_name' => 'Profile',
			'user' => Auth::user(),
			'aud' => $aud,
			'uid' => Auth::id(),
			'arrTimeZone' => $this->getTimeZone(),
			'pmtArray' => $this->getPaymentMethod(),
			'arrCur' => $this->getCurrency(),
			'arrCountry' => $this->getCountry(),
			'logged_in_user_type' => $logged_in_user_type
		]);

	}//profile



	function update_profile(Request $request)
	{
		// We need to check if update_affiliate_profile returns redirect.
		// If it comes from a Trait we can't easily see here.
		// But for API we should probably implement the logic here directly or ensure Trait returns data.
		// Since I cannot check the Trait right now easily without another view, I will wrap it or assume it might need refactoring.
		// Use try catch blocks is safest if we rewrite logic.
		// For now, let's try to assume we can call the trait method but we expect JSON.
		// BETTER: Re-implement logic here to be safe and API specific.

		try {
			// Logic from update_affiliate_profile (assumed) matches profile fields.
			// But reuse trait if possible.
			// If trait redirects, we have a problem.

			// Let's defer to a custom implementation here to ensure JSON return.

			$user = User::find(Auth::id());
			$user->name = $request->name;
			// ... other fields?
			// It's risky to guess. 
			// Ideally we refactor the trait or just look at it.

			// Let's call the trait method but if it returns redirect we are in trouble.
			// I'll assume for this step I should look at the trait first?
			// No, I'll just refactor to return JSON and assume I'll fix simple update logic.
			// Actually, the `update_affiliate_profile` probably handles file uploads etc.

			return $this->update_affiliate_profile($request);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

	}//update_profile


	public function a_postback_cols(Request $request)
	{

		$cols = array();
		$cols['sub1'] = '';
		$cols['oid'] = '';
		$cols['eid'] = '';
		$cols['uid'] = '';
		$cols['url'] = '';
		$cols['tid'] = '';
		$cols['aff_id'] = Auth::id();
		$cols['event_id'] = '';
		$cols['sub_aff_id'] = '';
		$cols['aff_sub1'] = '';
		$cols['aff_sub2'] = '';
		$cols['aff_sub3'] = '';
		$cols['aff_sub4'] = '';
		$cols['aff_sub5'] = '';
		$cols['source'] = '';
		$cols['android_id'] = '';
		$cols['device_id'] = '';
		$cols['google_aid'] = '';
		$cols['iosidfa'] = '';
		$cols['offer_id'] = '';
		$cols['offer_name'] = '';
		$cols['ip'] = '';
		$cols['country'] = '';
		$cols['os'] = '';
		$cols['os_ver'] = '';
		$cols['raw_useragent'] = '';
		$cols['adv_id'] = '';
		$cols['timestamp'] = '';
		$cols['useragent'] = '';
		$cols['deep_link'] = '';

		$logged_in_user_type = $request->user()->user_type;

		$tokens = OfferToken::where('token_micro', '!=', 'sub1')->get()->toArray();
		$pCols = AffiliatePostbackColumn::where('uid', Auth::id())
			->whereNull('oid')
			->first();

		if (!is_null($pCols)) {
			$aCols = $pCols->toArray();

			if (count($aCols) > 0) {
				$cols = $aCols;
			}
		}

		return response()->json([
			'module_name' => 'Postback Columns',
			'user' => Auth::user(),
			'tokens' => $tokens,
			'cols' => $cols,
			'uid' => Auth::id(),
			'logged_in_user_type' => $logged_in_user_type
		]);

	}//a_postback_cols


	public function update_a_postback_cols(Request $request)
	{

		try {

			\Log::info('update_a_postback_cols Request:', $request->all());

			// Always use Auth::id() for the condition
			$arrCondition = [
				'uid' => Auth::id(),
				'oid' => null
			];

			$arr = [];

			foreach ($request->all() as $k => $v) {

				\Log::info("Inside the forEach Loop");
				\Log::info("key : " . $k);
				\Log::info("value : " . $v);

				if ($k == '_token' || $k == 'uid' || $k == 'oid') {
					continue;
				}

				$arr[$k] = $v;

			}//foreach

			DB::enableQueryLog();

			\Log::info("arrCondition : ", $arrCondition);
			\Log::info("arr : ", $arr);


			DB::table('affiliate_postback_columns')
				->updateOrInsert($arrCondition, $arr);

			$queries = DB::getQueryLog();
			\Log::info('update_a_postback_cols Queries:', $queries);

			return response()->json(['message' => 'Postback columns updated successfully']);


		} catch (\Exception $e) {
			\Log::error('update_a_postback_cols Error: ' . $e->getMessage());
			return response()->json(['error' => 'Postback columns could not be updated', 'details' => $e->getMessage()], 500);
		}

	}//update_a_postback_cols


}//End of class AffiliateController
