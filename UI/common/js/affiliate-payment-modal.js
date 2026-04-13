/**
 * AffiliatePaymentPlugin - Reusable Affiliate Payment Modal
 *
 * Self-injects a Bootstrap 4 modal with an affiliate payment form.
 * Can be opened from any page with a single call.
 *
 * Usage:
 *   AffiliatePaymentPlugin.open({
 *       endpoint:    'admin/invoices/5/pay',   // API endpoint for POST submit
 *       amount:      '1250.00',                // pre-fill amount
 *       currency:    'USD',                    // display only (default: USD)
 *       paymentMode: 'Manual',                 // default selection
 *       onSuccess:   function(response) {},    // called on successful save
 *       onSubmit:    function(data, done, err) {} // override full submit logic
 *   });
 *
 * Fixed values:
 *   payment_from: Agency
 *   payment_to:   Affiliate
 *
 * Dependencies (must be loaded before this file):
 *   jQuery, Bootstrap 4, API (api.js), Toast (toast.js)
 */
const AffiliatePaymentPlugin = (function ($) {
    'use strict';

    const MODAL_ID = 'affiliatePaymentModal';
    const FORM_ID = 'apForm';
    const SUBMIT_ID = 'apSubmitBtn';
    const MSG_ID = 'apValidationMsg';

    let _opts = {};

    function _today() {
        return new Date().toISOString().split('T')[0];
    }

    function _el(id) {
        return document.getElementById(id);
    }

    function _inject() {
        if (_el(MODAL_ID)) return;

        const html = `
<div class="modal fade" id="${MODAL_ID}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-left-success">

      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-success">
          <i class="fas fa-receipt mr-2"></i> Record Affiliate Payment
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="${FORM_ID}" novalidate>

          <input type="hidden" id="apPaymentFrom" value="Agency">
          <input type="hidden" id="apPaymentTo" value="Affiliate">

          <div class="row">
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold">
                Amount <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="apCurrencyLabel">$</span>
                </div>
                <input type="number" class="form-control" id="apAmount"
                       min="0.01" step="0.01" placeholder="0.00">
              </div>
            </div>
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold">
                Payment Mode <span class="text-danger">*</span>
              </label>
              <select class="form-control" id="apPaymentMode">
                <option value="">-- Select --</option>
                <option value="Manual">Manual</option>
                <option value="Online">Online</option>
              </select>
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold">
              Manual Invoice Number
            </label>
            <input type="text" class="form-control" id="apManualInvoiceNumber"
                   placeholder="e.g. INV-2026-001" maxlength="100">
            <small class="form-text text-muted">Optional. Use if affiliate provided invoice code.</small>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold">Payment Date</label>
            <input type="date" class="form-control" id="apActivityDate">
            <small class="form-text text-muted">Defaults to today if left blank.</small>
          </div>

          <div class="row">
            <div class="col-md-6 form-group mb-3">
              <label>Transaction Code</label>
              <input type="text" class="form-control" id="apTransactionCode"
                     placeholder="e.g. TXN-2026-001" maxlength="100">
            </div>
          </div>

          <div class="form-group mb-0">
            <label>Notes / Details</label>
            <textarea class="form-control" id="apTransactionDetails" rows="2"
                      placeholder="Optional remarks about this payment..."></textarea>
          </div>

          <div id="${MSG_ID}" class="alert alert-danger mt-3 mb-0 d-none" role="alert"></div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i> Cancel
        </button>
        <button type="button" class="btn btn-success" id="${SUBMIT_ID}">
          <i class="fas fa-check-circle mr-2"></i> Confirm Payment
        </button>
      </div>

    </div>
  </div>
</div>`;

        document.body.insertAdjacentHTML('beforeend', html);

        _el(SUBMIT_ID).addEventListener('click', _onSubmit);

        $('#' + MODAL_ID).on('hidden.bs.modal', function () {
            _el(MSG_ID).classList.add('d-none');
            _resetBtn();
        });
    }

    function _populate(opts) {
        _el('apPaymentFrom').value = 'Agency';
        _el('apPaymentTo').value = 'Affiliate';
        _el('apPaymentMode').value = opts.paymentMode || 'Manual';
        _el('apAmount').value = opts.amount || '';
        _el('apManualInvoiceNumber').value = '';
        _el('apActivityDate').value = _today();
        _el('apTransactionCode').value = '';
        _el('apTransactionDetails').value = '';
        _el(MSG_ID).classList.add('d-none');
        _resetBtn();
    }

    function _resetBtn() {
        var btn = _el(SUBMIT_ID);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Confirm Payment';
    }

    function _validate() {
        var errors = [];
        if (!_el('apPaymentMode').value) errors.push('Payment Mode is required.');

        var amt = parseFloat(_el('apAmount').value);
        if (isNaN(amt) || amt <= 0) errors.push('Amount must be greater than 0.');

        return errors;
    }

    function _collectFormData() {
        var manualInvoiceNumber = _el('apManualInvoiceNumber').value.trim();

        return {
            payment_from: _el('apPaymentFrom').value,
            payment_to: _el('apPaymentTo').value,
            payment_mode: _el('apPaymentMode').value,
            transaction_amount: parseFloat(_el('apAmount').value),
          manual_invoice_no: manualInvoiceNumber || null,
          manual_invoice_number: manualInvoiceNumber || null,
            activity_date: _el('apActivityDate').value || null,
            transaction_code: _el('apTransactionCode').value.trim() || null,
            transaction_details: _el('apTransactionDetails').value.trim() || null,
        };
    }

    async function _onSubmit() {
        var errors = _validate();
        var msgEl = _el(MSG_ID);

        if (errors.length) {
            msgEl.textContent = errors.join(' ');
            msgEl.classList.remove('d-none');
            return;
        }
        msgEl.classList.add('d-none');

        var btn = _el(SUBMIT_ID);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

        var formData = _collectFormData();

        if (typeof _opts.onSubmit === 'function') {
            _opts.onSubmit(
                formData,
                function onDone() {
                    $('#' + MODAL_ID).modal('hide');
                },
                function onError(msg) {
                    _resetBtn();
                    msgEl.textContent = msg || 'An unexpected error occurred.';
                    msgEl.classList.remove('d-none');
                }
            );
            return;
        }

        if (!_opts.endpoint) {
            _resetBtn();
            msgEl.textContent = 'Submit endpoint is not configured.';
            msgEl.classList.remove('d-none');
            return;
        }

        try {
            var response = await API.fetchWithAuth(_opts.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
            });

            if (response && response.success) {
                $('#' + MODAL_ID).modal('hide');
                Toast.success('Affiliate Payment Recorded', 'Payment has been saved successfully.');
                if (typeof _opts.onSuccess === 'function') {
                    _opts.onSuccess(response);
                }
            } else {
                throw new Error((response && response.message) || 'Failed to record payment.');
            }
        } catch (err) {
            _resetBtn();
            msgEl.textContent = err.message;
            msgEl.classList.remove('d-none');
        }
    }

    return {
        /**
         * Open the affiliate payment modal.
         *
         * @param {Object}   opts
         * @param {string}   opts.endpoint      API path for POST
         * @param {string}   [opts.amount]      Pre-fill amount field
         * @param {string}   [opts.currency]    Currency label shown in input group (default USD)
         * @param {string}   [opts.paymentMode] Default for Payment Mode dropdown
         * @param {function} [opts.onSuccess]   Called with API response on success
         * @param {function} [opts.onSubmit]    Override full submit (formData, doneFn, errFn)
         */
        open: function (opts) {
            _opts = opts || {};
            _inject();
            _populate(_opts);
            $('#' + MODAL_ID).modal('show');
        }
    };

}(jQuery));
