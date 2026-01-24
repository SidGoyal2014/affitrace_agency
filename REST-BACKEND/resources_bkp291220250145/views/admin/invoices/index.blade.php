@extends('admin.theme.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Invoices</h6>
                    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-sm">Create New Invoice</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="invoiceTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice #</th>
                                    <th>Advertiser</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.invoices.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'invoice_number', name: 'invoice_number' },
                    { data: 'advertiser.company', name: 'advertiser.company', defaultContent: 'N/A' },
                    { data: 'total_amount', name: 'total_amount', render: $.fn.dataTable.render.number(',', '.', 2, '$') },
                    { data: 'invoice_date', name: 'invoice_date' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });
        });
    </script>
@endsection