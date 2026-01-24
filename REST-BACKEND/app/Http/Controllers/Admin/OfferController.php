<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminOfferService;
use App\Models\Offer;

class OfferController extends Controller
{
	protected $offerService;

	public function __construct(AdminOfferService $offerService)
	{
		$this->offerService = $offerService;
	}

	public function index()
	{
		return response()->json(['module_name' => 'Offers Index', 'success' => true]);
	}

	public function create(Request $request)
	{
		$result = $this->offerService->getCreateOfferData($request->user());
		if (isset($result['code']) && $result['code'] != 200) {
			return response()->json($result, $result['code']);
		}
		return response()->json($result);
	}

	public function save(Request $request)
	{
		$result = $this->offerService->saveOffer($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function delete_offer(string $id)
	{
		return response()->json($this->offerService->deleteOffer($id));
	}

	public function restore_offer(string $id)
	{
		return response()->json($this->offerService->restoreOffer($id));
	}

	public function edit(Request $request, $id)
	{
		$result = $this->offerService->getEditOfferData($request, $id);
		if (isset($result['code']) && $result['code'] != 200) {
			return response()->json($result, $result['code']);
		}
		return response()->json($result);
	}

	public function getEventTurlForAffiliate($aff_id)
	{
		return null;
	}

	public function update_offer(Request $request)
	{
		$result = $this->offerService->updateOffer($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function update_targetting(Request $request)
	{
		$result = $this->offerService->updateTargeting($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function update_mmp_details(Request $request)
	{
		$result = $this->offerService->saveMmpDetails($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function offers_approved()
	{
		return response()->json($this->offerService->getOffersByStatus('approve'));
	}

	public function offers_pending()
	{
		return response()->json($this->offerService->getOffersByStatus('pending'));
	}

	public function offers_ended()
	{
		return response()->json($this->offerService->getOffersByStatus('ended'));
	}

	public function offers_deleted()
	{
		return response()->json($this->offerService->getOffersByStatus('deleted'));
	}

	public function save_payout_rules(Request $request)
	{
		$result = $this->offerService->savePayoutRules($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function updateTrackingURL(Request $request)
	{
		// Empty in original
	}

	public function stu(Request $request)
	{
		$result = $this->offerService->saveTrackingUrl($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function show_events($id)
	{
		$arrEvents = \App\Models\OfferEvent::where('offer_id', '=', $id)->get()->toArray();
		return response()->json(['events' => $arrEvents]);
	}

	public function assign($oid, $aid)
	{
		$result = $this->offerService->assignOffer($oid, $aid);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function reject($oid, $aid)
	{
		$result = $this->offerService->rejectOffer($oid, $aid);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function unassign($oid, $aid)
	{
		$result = $this->offerService->unassignOffer($oid, $aid);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function save_event(Request $request)
	{
		$result = $this->offerService->saveEvent($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	function get_event($eid)
	{
		$event = \App\Models\OfferEvent::find($eid)->toArray();
		return response()->json($event);
	}

	function get_rule($oid, $eid)
	{
		$rule = \App\Models\PayoutRule::where('event_id', $eid)->where('offer_id', $oid)->get()->toArray();
		if (count($rule) > 0) {
			return response()->json($rule[0]);
		} else {
			return response()->json(array());
		}
	}

	function update_event(Request $request)
	{
		$result = $this->offerService->updateEvent($request);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function update_event_postback(Request $request)
	{
		try {
			\App\Models\PayoutRule::where('id', $request->pr_id)->update(['postback_url' => $request->postback_url]);
			return response()->json(['status' => 1, 'msg' => 'Postback updated successfully']);
		} catch (\Exception $e) {
			return response()->json(['status' => 0, 'error' => $e->getMessage()]);
		}
	}

	public function remove_event($oid, $eid)
	{

		try {

			\App\Models\OfferEvent::where('id', $eid)->where('offer_id', $oid)->delete();
			return response()->json(['status' => 1, 'msg' => 'Event removed successfully']);

		} catch (\Exception $e) {
			return response()->json(['status' => 0, 'msg' => $e->getMessage()]);

		}

	}//remove_event

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{

		Offer::find($id)->delete();
		return response()->json(['status' => 1, 'msg' => 'Offer deleted successfully']);

	}//destroy


}//OfferController
