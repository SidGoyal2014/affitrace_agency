@include('page_header')


    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('admin.theme.sidebar_nav')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('admin.theme.header')

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

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Affitrace 2023</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

@include('page_footer')