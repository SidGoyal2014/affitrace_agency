@extends('admin.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
	                                            <th width="70%">Offer Name</th>
	                                            <th width="20%">Revenue</th>
	                                            <th width="10%">Status</th>
						</tr>
					</thead>

					<tbody>
						@foreach($data as $obj)
                        <tr>
                            <td><a href="{{url('admin/offer/edit')}}/{{ $obj->offer_id }}">{{ $obj->offer_name }}</a> </td>
                            <td>{{ sprintf("%01.2f", $obj->revenue) }}</td>
                            @if( $obj->offer_status == 'approve')
                           		<td class="btn_small btn-border-success">Approved</td>

                            @elseif( $obj->offer_status == 'pending')
                            	<td class="btn_small btn-border-warning">Pending</td>

                            @elseif( $obj->offer_status == 'ended')
                            	<td class="btn_small btn-border-danger">Completed</td>

                            @endif

                        </tr>
						@endforeach

					</tbody>
				</table>
			</div>
		</div>

	@endsection
