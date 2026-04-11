/**
 * MarkPaidPlugin — Reusable Payment Receipt Modal
 *
 * Self-injects a Bootstrap 4 modal with a payment receipt form.
 * Can be opened from any page with a single call.
 *
 * Usage:
 *   MarkPaidPlugin.open({
 *       endpoint:    'admin/invoices/5/pay',   // API endpoint for POST submit
 *       amount:      '1250.00',                // pre-fill amount
 *       currency:    'USD',                    // display only (default: USD)
 *       paymentFrom: 'Advertiser',             // default selection
 *       paymentTo:   'Agency',                 // default selection
 *       paymentMode: 'Manual',                 // default selection
 *       onSuccess:   function(response) {},    // called on successful save
 *       onSubmit:    function(data, done, err) {} // override full submit logic
 *   });
 *
 * Dependencies (must be loaded before this file):
 *   jQuery, Bootstrap 4, API (api.js), Toast (toast.js)
 */
const MarkPaidPlugin = (function ($) {
    'use strict';

    const MODAL_ID  = 'markPaidPluginModal';
    const FORM_ID   = 'mpForm';
    const SUBMIT_ID = 'mpSubmitBtn';
    const MSG_ID    = 'mpValidationMsg';

    let _opts = {};

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------

    function _today() {
        return new Date().toISOString().split('T')[0];
    }

    function _el(id) {
        return document.getElementById(id);
    }

    // ---------------------------------------------------------------
    // Modal HTML injection (runs once)
    // ---------------------------------------------------------------

    function _inject() {
        if (_el(MODAL_ID)) return;

        const html = `
<div class="modal fade" id="${MODAL_ID}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-left-success">

      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-success">
          <i class="fas fa-receipt mr-2"></i> Record Payment Receipt
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="${FORM_ID}" novalidate>

          <!-- Direction row -->
          <div class="row">
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold">
                Payment From <span class="text-danger">*</span>
              </label>
              <select class="form-control" id="mpPaymentFrom">
                <option value="">-- Select --</option>
                <option value="Advertiser">Advertiser</option>
                <option value="Affiliate">Affiliate</option>
                <option value="Agency">Agency</option>
                <option value="Custom">Custom</option>
              </select>
            </div>
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold">
                Payment To <span class="text-danger">*</span>
              </label>
              <select class="form-control" id="mpPaymentTo">
                <option value="">-- Select --</option>
                <option value="Advertiser">Advertiser</option>
                <option value="Affiliate">Affiliate</option>
                <option value="Agency">Agency</option>
                <option value="Custom">Custom</option>
              </select>
            </div>
          </div>

          <!-- Amount + Mode row -->
          <div class="row">
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold">
                Amount <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="mpCurrencyLabel">$</span>
                </div>
                <input type="number" class="form-control" id="mpAmount"
                       min="0.01" step="0.01" placeholder="0.00">
              </div>
            </div>
            <div class="col-md-6 form-group mb-3">
              <label class="font-weight-bold">
                Payment Mode <span class="text-danger">*</span>
              </label>
              <select class="form-control" id="mpPaymentMode">
                <option value="">-- Select --</option>
                <option value="Manual">Manual</option>
                <option value="Online">Online</option>
              </select>
            </div>
          </div>

          <!-- Payment Date -->
          <div class="form-group mb-3">
            <label class="font-weight-bold">Payment Date</label>
            <input type="date" class="form-control" id="mpActivityDate">
            <small class="form-text text-muted">Defaults to today if left blank.</small>
          </div>

          <!-- Reference numbers row -->
          <div class="row">
            <div class="col-md-6 form-group mb-3">
              <label>Transaction Code</label>
              <input type="text" class="form-control" id="mpTransactionCode"
                     placeholder="e.g. TXN-2026-001" maxlength="100">
            </div>

          </div>

          <!-- Notes -->
          <div class="form-group mb-0">
            <label>Notes / Details</label>
            <textarea class="form-control" id="mpTransactionDetails" rows="2"
                      placeholder="Optional remarks about this payment..."></textarea>
          </div>

          <!-- Validation message -->
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

    // ---------------------------------------------------------------
    // Populate form with context values on each open()
    // ---------------------------------------------------------------

    function _populate(opts) {
        _el('mpPaymentFrom').value        = opts.paymentFrom  || 'Advertiser';
        _el('mpPaymentTo').value          = opts.paymentTo    || 'Agency';
        _el('mpPaymentMode').value        = opts.paymentMode  || 'Manual';
        _el('mpAmount').value             = opts.amount       || '';
        _el('mpActivityDate').value       = _today();
        _el('mpTransactionCode').value    = '';
        _el('mpTransactionDetails').value = '';
        _el(MSG_ID).classList.add('d-none');
        _resetBtn();
    }

    function _resetBtn() {
        var btn     = _el(SUBMIT_ID);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Confirm Payment';
    }

    // ---------------------------------------------------------------
    // Client-side validation
    // ---------------------------------------------------------------

    function _validate() {
        var errors = [];
        if (!_el('mpPaymentFrom').value)                          errors.push('Payment From is required.');
        if (!_el('mpPaymentTo').value)                            errors.push('Payment To is required.');
        if (!_el('mpPaymentMode').value)                          errors.push('Payment Mode is required.');
        var amt = parseFloat(_el('mpAmount').value);
        if (isNaN(amt) || amt <= 0)                               errors.push('Amount must be greater than 0.');
        return errors;
    }

    // ---------------------------------------------------------------
    // Collect form values
    // ---------------------------------------------------------------

    function _collectFormData() {
        return {
            payment_from:        _el('mpPaymentFrom').value,
            payment_to:          _el('mpPaymentTo').value,
            payment_mode:        _el('mpPaymentMode').value,
            transaction_amount:  parseFloat(_el('mpAmount').value),
            activity_date:       _el('mpActivityDate').value  || null,
            transaction_code:    _el('mpTransactionCode').value.trim()    || null,
            transaction_details: _el('mpTransactionDetails').value.trim() || null,
        };
    }

    // ---------------------------------------------------------------
    // Submit handler
    // ---------------------------------------------------------------

    async function _onSubmit() {
        var errors = _validate();
        var msgEl  = _el(MSG_ID);

        if (errors.length) {
            msgEl.textContent = errors.join(' ');
            msgEl.classList.remove('d-none');
            return;
        }
        msgEl.classList.add('d-none');

        var btn      = _el(SUBMIT_ID);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

        var formData = _collectFormData();

        // If caller provides a custom submit hook, delegate entirely to it.
        // Signature: onSubmit(formData, doneFn, errorFn)
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

        // Default: POST form data to the configured endpoint via API.fetchWithAuth
        if (!_opts.endpoint) {
            _resetBtn();
            msgEl.textContent = 'Submit endpoint is not configured.';
            msgEl.classList.remove('d-none');
            return;
        }

        try {
            var response = await API.fetchWithAuth(_opts.endpoint, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(formData),
            });

            if (response && response.success) {
                $('#' + MODAL_ID).modal('hide');
                Toast.success('Payment Recorded', 'Invoice has been marked as paid.');
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

    // ---------------------------------------------------------------
    // Public API
    // ---------------------------------------------------------------

    return {
        /**
         * Open the payment receipt modal.
         *
         * @param {Object}   opts
         * @param {string}   opts.endpoint      API path for POST (e.g. 'admin/invoices/5/pay')
         * @param {string}   [opts.amount]       Pre-fill amount field
         * @param {string}   [opts.currency]     Currency label shown in input group (default USD)
         * @param {string}   [opts.paymentFrom]  Default for Payment From dropdown
         * @param {string}   [opts.paymentTo]    Default for Payment To dropdown
         * @param {string}   [opts.paymentMode]  Default for Payment Mode dropdown
         * @param {function} [opts.onSuccess]    Called with API response on success
         * @param {function} [opts.onSubmit]     Override full submit (formData, doneFn, errFn)
         */
        open: function (opts) {
            _opts = opts || {};
            _inject();
            _populate(_opts);
            $('#' + MODAL_ID).modal('show');
        }
    };

}(jQuery));
