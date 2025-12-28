@extends('admin.theme.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <!-- Header -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Invoice: {{ $invoice->invoice_number }}</h1>
                <div>
                    <span
                        class="badge badge-{{ $invoice->status == 'PAID' ? 'success' : ($invoice->status == 'SENT' ? 'info' : 'secondary') }} p-2"
                        style="font-size: 1rem;">
                        {{ $invoice->status }}
                    </span>
                </div>
            </div>

            <div class="row">
                <!-- Details Card -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Invoice Details</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Advertiser:</strong> {{ $invoice->advertiser->company ?? 'N/A' }}</p>
                            <p><strong>Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</p>
                            <p><strong>Due Date:</strong>
                                {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A' }}</p>
                            <hr>
                            <p><strong>Subtotal:</strong> ${{ number_format($invoice->subtotal_amount, 2) }}</p>
                            <p><strong>Tax:</strong> ${{ number_format($invoice->tax_amount, 2) }}</p>
                            <h4 class="font-weight-bold">Total: ${{ number_format($invoice->total_amount, 2) }}</h4>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                        </div>
                        <div class="card-body">
                            @if($invoice->is_draft)
                                <button class="btn btn-primary btn-block mb-2" id="sendBtn">Send Invoice</button>
                                <button class="btn btn-secondary btn-block mb-2" disabled title="Coming soon">Edit
                                    Invoice</button>
                                <button class="btn btn-danger btn-block" id="deleteBtn">Delete Invoice</button>
                            @elseif($invoice->is_sent)
                                <a href="{{ asset('storage/' . $invoice->pdf_path) }}" target="_blank"
                                    class="btn btn-info btn-block mb-2">Download PDF</a>
                                <button class="btn btn-success btn-block" id="payBtn">Mark as Paid</button>
                            @elseif($invoice->is_paid)
                                <a href="{{ asset('storage/' . $invoice->pdf_path) }}" target="_blank"
                                    class="btn btn-info btn-block mb-2">Download PDF</a>
                                <div class="alert alert-success text-center">Paid on {{ $invoice->paid_at->format('Y-m-d') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Transactions List -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Line Items</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->transactions as $tx)
                                            <tr>
                                                <td>Transaction</td>
                                                <td>{{ $tx->billing_reason ?? 'Transaction #' . $tx->id }}</td>
                                                <td class="text-right">${{ number_format($tx->billing_charge, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let invoiceId = "{{ $invoice->id }}";

            $('#sendBtn').click(function () {
                if (!confirm('Are you sure you want to finalize and send this invoice? It cannot be edited afterwards.')) return;

                $.post(`/api/admin/invoices/${invoiceId}/send`)
                    .done(function (res) {
                        if (res.success) location.reload();
                    })
                    .fail(handleError);
            });

            $('#payBtn').click(function () {
                if (!confirm('Mark this invoice as PAID?')) return;

                $.post(`/api/admin/invoices/${invoiceId}/pay`)
                    .done(function (res) {
                        if (res.success) location.reload();
                    })
                    .fail(handleError);
            });

            $('#deleteBtn').click(function () {
                if (!confirm('Delete this draft invoice? Transactions will be unlinked.')) return;

                $.ajax({
                    url: `/api/admin/invoices/${invoiceId}`,
                    type: 'DELETE',
                    success: function (res) {
                        if (res.success) window.location.href = "{{ route('admin.invoices.index') }}";
                    },
                    error: handleError
                });
            });

            function handleError(xhr) {
                alert('Error: ' + (xhr.responseJSON.message || 'Unknown error'));
            }
        });
    </script>
@endsection