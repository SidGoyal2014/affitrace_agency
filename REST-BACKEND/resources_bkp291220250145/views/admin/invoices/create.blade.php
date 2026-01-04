@extends('admin.theme.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create Invoice</h6>
                </div>
                <div class="card-body">
                    <form id="createInvoiceForm">
                        <div class="form-group">
                            <label for="advertiser_id">Select Advertiser</label>
                            <select class="form-control" id="advertiser_id" name="advertiser_id" required>
                                <option value="">-- Select Advertiser --</option>
                                @foreach($advertisers as $adv)
                                    <option value="{{ $adv->id }}">{{ $adv->company }} ({{ $adv->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="transactionsSection" style="display:none;">
                            <h5 class="mt-4">Billable Transactions</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="transactionsTable">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Total Selected:</th>
                                            <th id="totalSelectedAmount">$0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3" id="submitBtn" disabled>Create Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let transactions = [];

            $('#advertiser_id').change(function () {
                let advId = $(this).val();
                if (!advId) {
                    $('#transactionsSection').hide();
                    $('#submitBtn').prop('disabled', true);
                    return;
                }

                // Fetch billable transactions
                $.get(`/api/admin/invoices/${advId}/billable-transactions`, function (data) {
                    transactions = data;
                    renderTransactions();
                    $('#transactionsSection').show();
                });
            });

            function renderTransactions() {
                let tbody = $('#transactionsTable tbody');
                tbody.empty();

                if (transactions.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center">No billable transactions found.</td></tr>');
                    $('#submitBtn').prop('disabled', true);
                    return;
                }

                transactions.forEach(tx => {
                    let row = `<tr>
                    <td><input type="checkbox" name="transactions[]" value="${tx.id}" class="tx-checkbox" data-amount="${tx.billing_charge}"></td>
                    <td>${tx.id}</td>
                    <td>${new Date(tx.created_at).toLocaleDateString()}</td>
                    <td>${tx.billing_reason || 'N/A'}</td>
                    <td>$${parseFloat(tx.billing_charge).toFixed(2)}</td>
                </tr>`;
                    tbody.append(row);
                });

                updateTotal();
            }

            $(document).on('change', '.tx-checkbox, #selectAll', function () {
                if (this.id === 'selectAll') {
                    $('.tx-checkbox').prop('checked', $(this).prop('checked'));
                }
                updateTotal();
            });

            function updateTotal() {
                let total = 0;
                let checkedBoxes = $('.tx-checkbox:checked');

                checkedBoxes.each(function () {
                    total += parseFloat($(this).data('amount'));
                });

                $('#totalSelectedAmount').text('$' + total.toFixed(2));
                $('#submitBtn').prop('disabled', checkedBoxes.length === 0);

                // Update Select All state checking logic
                let allChecked = $('.tx-checkbox').length > 0 && $('.tx-checkbox:checked').length === $('.tx-checkbox').length;
                $('#selectAll').prop('checked', allChecked);
            }

            $('#createInvoiceForm').submit(function (e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.post("{{ route('admin.invoices.store') }}", formData)
                    .done(function (response) {
                        if (response.success) {
                            window.location.href = "{{ route('admin.invoices.index') }}"; // Ideally go to show page
                        }
                    })
                    .fail(function (xhr) {
                        alert('Error: ' + (xhr.responseJSON.message || 'Unknown error'));
                    });
            });
        });
    </script>
@endsection