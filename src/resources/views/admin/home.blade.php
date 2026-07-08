@extends('admin.layouts.app')
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0-rc.1/Chart.js"></script>
    <!--<div class="container">
@include('flash::message')

            -->

    <div class="margin row">
        <div class="col-lg-4 col-xs-4">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <?php
                    $users1 = App\Models\User::query()->whereHas('roles', function($q){
                        return $q->whereNotIn('id', [App\Models\Role::ROLE_SUPER_ADMIN, App\Models\Role::ROLE_ADMIN]);
                    });
                    ?>

                    <h3>{{ $users1->count() }}</h3>

                    <p>App Users</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">More info
                    <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-4">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">

                    <?php
                    $media1 = App\Models\Media::where('is_mixer', 0)->get();
                    ?>

                    <h3>{{ $media1->count() }}</h3>

                    <p>Total Sounds</p>
                </div>
                <div class="icon">
                    <i class="fa fa-music"></i>

                </div>
                <a href="{{ route('admin.medias.index') }}" class="small-box-footer">More info
                    <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-xs-4">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <?php
                    $category1 = App\Models\Category::where('is_mixer', 0)->get();
                    ?>
                    <h3>{{ $category1->count() }}</h3>
                    <p>Total Categories</p>
                </div>
                <div class="icon">
                    <i class="fa fa-cubes"></i>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="small-box-footer">More info
                    <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <div class="margin row">        
        <div class="col-lg-4 col-xs-4">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <?php
                    $reports = App\Models\Report::all();
                    ?>
                    <h3>{{ $reports->count() }}</h3>
                    <p>Total Reports</p>
                </div>
                <div class="icon">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="small-box-footer">More info
                    <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-4">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <?php
                    $reports = App\Models\Transaction::all();
                    ?>
                    <h3>{{ $reports->count() }}</h3>
                    <p>No of Transactions</p>
                </div>
                <div class="icon">
                    <i class="fa fa-credit-card"></i>
                </div>
                <a href="{{ route('admin.transactions.index') }}" class="small-box-footer">More info
                    <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-4">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <?php
                        $subscribedUsers = App\Models\User::whereHas('details', function($builder) {
                            return $builder->where('is_subscribed', 1);
                        });
                    ?>
                    <h3>{{ $subscribedUsers->count() }}</h3>
                    <p>Subscribed Users</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-circle-o"></i>
                </div>
                <a href="{{ route('admin.users.index') }}?subscription=1" class="small-box-footer">More info
                    <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

    <!-- <div class="row" style="margin-left:30px;">
        <div class="col-lg-9">
            <div class="box box-success">
                <div class="box-header with-border">`
                    <h3 class="box-title">Audios </h3>
                </div>
                <div class="box-body chart-responsive">
                    <canvas id="myChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>

        var ctx = document.getElementById("myChart").getContext('2d');
        var labels =
                {!! json_encode($months) !!}
        var data =
                {!! json_encode($counts_orders) !!}

        var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Number of Audios per month',
                        data,
                        backgroundColor: "#1A1427"
                    }]
                }
            });
    </script> -->

@endsection