<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        {{--<div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('bower_components/AdminLTE/dist/img/admin.png')}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>--}}

        <!-- search form (Optional) -->
       {{-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>--}}
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            @foreach($menus as $item)
                @if( isset($item['_child']))

                    <li class="treeview  " id="p_menu_{{$item['id']}}">
                        <a href="{{$item['url']}}"><i class="fa {{$item['icon']}}"></i> <span>{{$item['title']}}</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @foreach($item['_child'] as $child)
                                @if( $child['url'] == $current_uri )
                                    <script>
                                        $("#p_menu_{{$child['pid']}}").addClass('active');
                                    </script>
                                @endif
                                <li><a href="{{$child['url']}}">{{$child['title']}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="@if( $item['url'] == $current_uri ) active @endif"><a href="{{$item['url']}}"><i class="fa {{$item['icon']}}"></i> <span>{{$item['title']}}</span></a></li>
                @endif

            @endforeach

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
