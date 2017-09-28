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

            <div class="example-modal hide">
                <div class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close close_btn" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">添加语言</h4>
                            </div>
                            <div class="modal-body">
                                <!-- form start -->
                                <form class="form-horizontal">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="lang_name" class="col-sm-2 control-label">语言名称</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="lang_name" placeholder="语言名称（英文字母，_）">
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lang_title" class="col-sm-2 control-label">语言标题</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="lang_title" placeholder="语言标题（中文）">
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">状态</label>
                                            <div class="col-sm-10">
                                                <select class="form-control" id="lang_status">
                                                    <option value="1">可用</option>
                                                    <option  value="0">不可用</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" id="id" value="0">
                                        <input type="hidden" id="method" value="post">
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        {{--<button  class="btn btn-default close_btn">取消</button>--}}
                                        <button  class="btn btn-info pull-right save_btn">保存</button>
                                    </div>
                                    <!-- /.box-footer -->
                                </form>
                            </div>
                           {{-- <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>--}}
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->
            </div>
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
                        <th>日期</th>
                        <th>主图片</th>
                        <th>图片2</th>
                        <th>分享图片</th>
                        <th width="8%">内容（英文）</th>
                        <th width="8%">内容（中文）</th>
                        <th width="8%">点评</th>
                        <th>喜欢</th>
                        <th>分享</th>
                        <th>查看</th>
                        <th>添加时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td>{{$item['id']}}</td>
                        <td>{{$item['dateline']}}</td>
                        <td><img style="height: 64px;width: auto" src="{{$item['picture']}}"/></td>
                        <td><img style="height: 64px;width: auto" src="{{$item['picture2']}}"/></td>
                        <td><img style="height: 64px;width: auto" src="{{$item['fenxiang_img']}}"/></td>
                        <td>{{$item['content']}}</td>
                        <td>{{$item['note']}}</td>
                        <td>{{$item['translation']}}</td>
                        <td>{{$item['love']}}</td>
                        <td>{{$item['shares']}}</td>
                        <td>{{$item['views']}}</td>
                        <td>{{$item['created_at']}}</td>
                        <td>{{$item['updated_at']}}</td>

                        <td>
                            {{--<button type="button" class="btn btn-success edit-btn" data-id="{{$item['id']}}">编辑</button>--}}
                            {{--<button type="button" class="btn btn-danger del-btn" data-id="{{$item['id']}}">删除</button>--}}
                        </td>
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


        //添加
        $("#add_lang_btn").on('click', function () {
            $(".modal-title").text('添加语言');
            $("#lang_name").val('');
            $("#lang_title").val('');
            $("#lang_status option").each(function () {
                if($(this).attr('value') == 1){
                    $(this).attr('selected',true)
                }
            });
            $(".example-modal").removeClass('hide');
        });
        //关闭弹窗
        $(".close_btn").on('click', function () {
            $(".example-modal").addClass('hide');return
        });
        //删除数据
        $(".del-btn").on('click', function () {
            var id =  $(this).data('id')
            $(this).attr('disabled',true);

            ajax('delete', '/admin/languages/'+id, {}, function (re) {
                if( re.code != 0) {
                    alert('danger', re.text);
                    (this).attr('disabled',false);
                    return false;
                }
                alert('success', re.text, function () {
                    window.setTimeout(function () {
                        window.location.href = '/admin/languages';
                    },1000)

                });
            });
        });
        
        //编辑获取数据
        $(".edit-btn").on('click',function () {
            var that = $(this);
            var id =  that.data('id')
            that.attr('disabled',true);
            ajax('get', '/admin/languages/'+id, {}, function (re) {
                if( re.code != 0) {
                    alert('danger', re.text);
                    (this).attr('disabled',false);
                    return false;
                }
                that.attr('disabled',false);
                //打开弹窗
                $(".modal-title").text('编辑语言');
                $("#id").val(re.result.id)
                $("#method").val('put')
                $("#lang_name").val(re.result.name);
                $("#lang_title").val(re.result.title);
                $("#lang_status option").each(function () {
                    if($(this).attr('value') == re.result.status){
                        $(this).attr('selected',true)
                    }
                })
                $(".example-modal").removeClass('hide');

            });
        });
        //保存数据
        $(".save_btn").on('click', function () {

            var name = $("#lang_name").val();
            var title = $("#lang_title").val();
            var status = $("#lang_status").val();

            if( name == '') {
                $("#lang_name").closest('.form-group').addClass('has-error');
                $("#lang_name").next('span').text('名称 不能为空。');

            } else {
                $("#lang_name").closest('.form-group').removeClass('has-error');
                $("#lang_name").next('span').text('');
            }
            if( title == '') {
                $("#lang_title").closest('.form-group').addClass('has-error');
                $("#lang_title").next('span').text('标题 不能为空。');

            } else {
                $("#lang_title").closest('.form-group').removeClass('has-error');
                $("#lang_title").next('span').text('');
            }

            if( name == '' || title == '') return false;


            var data = {
                name : name,
                title : title,
                status : status,
                id : $("#id").val()
            };
            ajax($("#method").val(), '/admin/languages', data, function (re) {

                if(re.code != 0){
                    $.each(re.text, function(key, val) {
                        $("#lang_"+key).closest('.form-group').addClass('has-error');
                        $("#lang_"+key).next('span').text(val[0]);
                    });
                    return false
                }
                $(".example-modal").addClass('hide');
                alert('success', re.text, function () {
                    window.location.href = '/admin/languages';
                })
            })
            return false;
        })
    });
</script>
@endsection