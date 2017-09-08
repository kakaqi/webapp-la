@extends('admin.dashboard')
@section('content')

<style>
    .example-modal .modal {
        position: fixed;
        #top: auto;
        #bottom: auto;
        /*right: auto;
        left: auto;*/
        display: block;
        z-index: 1;
        padding-top: 6%;
    }
    .hide {
        display: none;
    }
</style>

<!-- Main content -->
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                {{--<h3 class="box-title">Hover Data Table</h3>--}}
                {{--<button type="button" class="btn btn-info" id="add_lang_btn">添加</button>--}}
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>登录名</th>
                        <th>邮箱</th>
                        <th>添加时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td>{{$item['id']}}</td>
                        <td>{{$item['name']}}</td>
                        <td>{{$item['email']}}</td>
                        <td>{{$item['created_at']}}</td>
                        <td>-//-</td>
                    </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- DataTables -->
<script src="{{ asset('bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<!-- SlimScroll -->
<script src="{{ asset('bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{ asset('bower_components/AdminLTE/plugins/fastclick/fastclick.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('bower_components/AdminLTE/dist/js/demo.js')}}"></script>
<!-- page script -->
<script>
    $(function () {
        $("#example1").DataTable();
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false
        });
    });
</script>
@endsection