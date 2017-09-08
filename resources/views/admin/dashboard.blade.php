@include('admin.header')
<!-- Sidebar -->
@include('admin.sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{ $page_title or "Page Title" }}
            <small>{{ $page_description or null }}</small>
        </h1>
        <!-- You can dynamically generate breadcrumbs here -->
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{$page_name or null}}</a></li>
            <li class="active">{{$page_title}}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        {{--<div class="alert  alert-dismissible" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            数据保存失败！
        </div>--}}
        <script>
            //公共alert方法
            function alert(type, msg, func) {

                var fa_type = '';
                switch (type){
                    case 'success':
                        fa_type = 'fa-check'
                        break;
                    case 'warning':
                        fa_type = 'fa-warning'
                        break;
                    case 'info':
                        fa_type = 'fa-info'
                        break;
                    case 'danger':
                        fa_type = 'fa-ban'
                        break;
                    default :
                        fa_type = 'fa-ban'
                        type = 'danger'
                }
                $('.content').prepend('<div class="alert  alert-dismissible alert-'+type+'" style="display: block"><button type="button" class="close" data-dismiss="alert" aria-hidden="false">&times;</button> <h4><i class="icon fa '+fa_type+'"></i> '+type+'!</h4>\n' +msg+'</div>');

                if( func != undefined) {
                    func()
                    /*window.setTimeout(function () {
                        func()
                    },1000)*/

                }
            }

            //公共ajax方法
            function ajax(type, url, data, func) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type:type,
                    dataType : 'json',
                    url : url,
                    data : data,
                    success : function (re) {
                        func(re)
                    },
                    error : function (re) {
                        console.log(re)
                    }
                })
            }
        </script>
        <!-- Your Page Content Here -->
        @yield('content')
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper --><!-- Footer -->
@include('admin.footer')