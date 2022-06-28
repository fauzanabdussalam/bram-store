@extends('admin.layouts.layout')

@section('content')
<div class="content-page">
  <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="md md-dashboard"></i> Dashboard</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{ Route('dashboard') }}">Dashboard</a></li>
                    </ol>
                </div>
            </div>

            <!-- Start Widget -->
            <!-- <div class="row">
                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-info"><i class="md md-view-list"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"> </span>
                            Total Category
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-purple"><i class="fa fa-newspaper-o"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"></span>
                            Total News
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-success"><i class="fa fa-user"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"></span>
                            Total Customer
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-primary"><i class="fa fa-users"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"></span>
                            Total Users
                        </div>
                    </div>
                </div>
            </div>  -->
            <!-- End row-->

            <div class="panel panel-default">    
                <div class="panel-heading">
                    <h3 class="panel-title">Today's News</h3>
                </div>      
                <div class="panel-body">
                    <table class="table table-bordered table-striped" id="datatable">
                        <thead>
                            <tr>
                                <th>Created</th>
                                <th>Creator</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th></th>
                            </tr>
                        </thead>                  
                        <!-- <tbody>
                          
                        </tbody> -->
                    </table>
                    <hr>
                    <div style="float: right">
                        <a href="{{ Route('news') }}" class="btn btn-icon btn-sm btn-primary">SEE ALL NEWS <i class="fa fa-arrow-right"></i></i></a>
                    </div>
                </div>
                <!-- end: page -->
            </div> <!-- end Panel -->
        </div>
    </div>
</div>
@endsection