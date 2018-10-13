@extends('layouts.top-nav')

@section('title', $title)
@section('body-class', 'top-navigation')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title }}</h5>

                            @if(Entrust::can(['role']))
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('role') }}">
                                    {{ trans('app.列表', ['value' => trans('app.权限')]) }}
                                </a>
                            </div>
                            @endif

                        </div>
                        <div class="ibox-content">
                            <div class="well">
                                <h2>{{ $role->name }} | {{ $role->display_name }}</h2>
                                <small>{{ $role->description }}</small>
                            </div>
                            <div class="row">

                                {!! Form::open(['class' => 'form-horizontal']) !!}

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <button class="btn btn-primary btn-sm" id="didClick" type="button">提交</button>
                                            <button class="btn btn-white btn-sm" type="reset">取消</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <i class="fa fa-info-circle"></i> 权限列表

                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="col-sm-12 disabled-item">
                                                    <div id="tree" class="ztree"> </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@include('widget.ztree')

@section('scripts-last')
    <script>
        var chkNodeArr;
        var chkNodeStr = "";

        $(function() {
            createTree('#tree');
            $('#didClick').click(function () {
                 $.ajax({
                       url: '{{ route('role.appointUpdate', ['id' => $id])}}',
                    type: 'GET',
                    async: true,
                        data: {
                            PostMethod : "checkedBox",
                            nodesJson: chkNodeStr
                         },
                   dataType: 'json',
                    success: function (data) {
                        if(data.status >= 1) {
                            window.location.href = data.url;
                        } else {
                            window.location.href = data.url;
                        }

                         },
                    error: function (err) {
                        alert('错误的修改数据')
                    }
                 });
              });
            });

        function createTree(treeId) {
            var zTree; //用于保存创建的树节点
            var setting = { //设置
                check: {
                    enable: true
                },
                view: {
                    showLine: true, //显示辅助线
                    dblClickExpand: true
                },
                data: {
                    simpleData: {
                        enable: true,
                        idKey: "id",
                        pIdKey: "pid",
                        rootPId: 0
                    }
                },
                callback: {
                    onCheck: onCheckNode  //回调函数,获取选节点
                }
            };
            $.ajax({ //请求数据,创建树
                type: 'GET',
                url: '{{ route('role.getAppoint', ['id' => $id])}}',
                dataType: "json", //返回的结果为json
                success: function(data) {
                    zTree = $.fn.zTree.init($(treeId), setting, data); //创建树
                },
                error: function(data) {
                    alert("创建树失败!");
                }
            });
        }

        function onCheckNode()
        {
            var treenode = $.fn.zTree.getZTreeObj("tree");
            chkNodeArr = treenode.getCheckedNodes(true);
            //true获取选中节点,false未选中节点,默认为true
            nodeJson = [];
            for (var i = 0; i < chkNodeArr.length; i++) {
                    nodeJson[i] = { "name": chkNodeArr[i].name, "id": chkNodeArr[i].id };
                }
            chkNodeStr = JSON.stringify(nodeJson);
         }

    </script>
@endsection