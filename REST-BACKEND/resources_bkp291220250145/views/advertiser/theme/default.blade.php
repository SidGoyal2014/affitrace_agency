@include('page_header')

    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('advertiser.theme.sidebar_nav')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('advertiser.theme.header')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">{{$module_name}}</h1>

                        {{---

							<?php
								/*
						<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
								*/
							?>

						---}}

                    </div>

                    <!-- Content Row -->

					<div class="row">
						<div class="col-lg-12 mb-8">
							<div class="card shadow mb-4">
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
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

@include('page_footer')