@include('page_header')

    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('affiliate.theme.sidebar_nav')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('affiliate.theme.header')

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Content Row -->
					<div class="row">
						<div class="col-lg-12 mb-8">
							<div class="card">{{-- shadow mb-4 --}}
								@include('flash-message')
								@yield('content')
							</div>
						</div>
					</div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->



@include('page_footer')