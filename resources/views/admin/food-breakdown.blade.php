@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 font-weight-bold text-gray-800">{{ __('app.food_breakdown_title') }}</h1>
            <p class="text-muted">{{ __('app.food_breakdown_subtitle') }}</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
            </a>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary font-weight-bold text-uppercase mb-1">{{ __('app.total_redemptions') }}</div>
                    <div class="h3 mb-0">{{ $overallStats['total_redemptions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success font-weight-bold text-uppercase mb-1">{{ __('app.total_value') }}</div>
                    <div class="h3 mb-0">£{{ number_format($overallStats['total_value'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-warning font-weight-bold text-uppercase mb-1">{{ __('app.total_owed') }}</div>
                    <div class="h3 mb-0">£{{ number_format($overallStats['total_owed'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-info font-weight-bold text-uppercase mb-1">{{ __('app.payment_collected') }}</div>
                    <div class="h3 mb-0">{{ $overallStats['total_collected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Food Types -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="free-tab" data-toggle="tab" href="#free" role="tab">
                        <i class="fas fa-gift text-success"></i> {{ __('app.free_food') }}
                        <span class="badge badge-success">{{ $freeStats['total_redemptions'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="discounted-tab" data-toggle="tab" href="#discounted" role="tab">
                        <i class="fas fa-tag text-warning"></i> {{ __('app.discounted_food') }}
                        <span class="badge badge-warning">{{ $discountedStats['total_redemptions'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="surplus-tab" data-toggle="tab" href="#surplus" role="tab">
                        <i class="fas fa-boxes-stacked text-purple"></i> {{ __('app.surplus_food') }}
                        <span class="badge badge-purple">{{ $surplusStats['total_redemptions'] }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <!-- Free Food Tab -->
                <div class="tab-pane fade show active" id="free" role="tabpanel">
                    @include('admin.food-breakdown-type', [
                        'redemptions' => $freeRedeemed,
                        'stats' => $freeStats,
                        'type' => 'free'
                    ])
                </div>

                <!-- Discounted Food Tab -->
                <div class="tab-pane fade" id="discounted" role="tabpanel">
                    @include('admin.food-breakdown-type', [
                        'redemptions' => $discountedRedeemed,
                        'stats' => $discountedStats,
                        'type' => 'discounted'
                    ])
                </div>

                <!-- Surplus Food Tab -->
                <div class="tab-pane fade" id="surplus" role="tabpanel">
                    @include('admin.food-breakdown-type', [
                        'redemptions' => $surplusRedeemed,
                        'stats' => $surplusStats,
                        'type' => 'surplus'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
