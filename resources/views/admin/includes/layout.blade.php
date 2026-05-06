<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from thememinister.com/health/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 02 Jul 2024 05:54:07 GMT -->

@include('admin.includes.head')

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <header class="main-header">
         
            <!-- Header Navbar -->
            @include('admin.includes.navbar')

        </header>
        <!-- =============================================== -->
        <!-- Left side column. contains the sidebar -->
        @include('admin.includes.sidebar')

        <!-- =============================================== -->
        <!-- Content Wrapper. Contains page content -->
       @yield('content')
        @include('admin.includes.footer')


    </div> <!-- ./wrapper -->
    <!-- ./wrapper -->
    <!-- Start Core Plugins
        =====================================================================-->
    <!-- jQuery -->
    @include('admin.includes.jslinks')
    <x-toast />


</body>

<!-- Mirrored from thememinister.com/health/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 02 Jul 2024 05:55:34 GMT -->

</html>
