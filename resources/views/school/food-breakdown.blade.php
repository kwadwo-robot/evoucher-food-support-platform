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
            <a href="{{ route('school.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
            </a>
        </div>
    </div>

    <!-- Modern Statistics Widgets -->
    <div class="row mb-4 g-3">
        <!-- Total Redemptions Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-primary">
                <div class="widget-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.total_redemptions') }}</div>
                    <div class="widget-value counter" data-target="{{ $overallStats['total_redemptions'] }}">0</div>
                    <div class="widget-subtext">{{ __('app.total_redemptions') }}</div>
                </div>
            </div>
        </div>

        <!-- Total Value Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-success">
                <div class="widget-icon">
                    <i class="fas fa-pound-sign"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.total_value') }}</div>
                    <div class="widget-value currency-counter" data-target="{{ $overallStats['total_value'] }}">£0.00</div>
                    <div class="widget-subtext">{{ __('app.total_value') }}</div>
                </div>
            </div>
        </div>

        <!-- Total Owed Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-warning">
                <div class="widget-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.total_owed') }}</div>
                    <div class="widget-value currency-counter" data-target="{{ $overallStats['total_owed'] }}">£0.00</div>
                    <div class="widget-subtext">{{ __('app.total_owed') }}</div>
                </div>
            </div>
        </div>

        <!-- Payment Collected Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-info">
                <div class="widget-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.payment_collected') }}</div>
                    <div class="widget-value counter" data-target="{{ $overallStats['total_collected'] }}">0</div>
                    <div class="widget-subtext">{{ __('app.payment_collected') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Food Types -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs modern-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="free-tab" data-toggle="tab" href="#free" role="tab">
                        <span class="tab-icon">
                            <i class="fas fa-gift text-success"></i>
                        </span>
                        <span class="tab-label">{{ __('app.free_food') }}</span>
                        <span class="badge badge-success ml-2">{{ $freeStats['total_redemptions'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="discounted-tab" data-toggle="tab" href="#discounted" role="tab">
                        <span class="tab-icon">
                            <i class="fas fa-tag text-warning"></i>
                        </span>
                        <span class="tab-label">{{ __('app.discounted_food') }}</span>
                        <span class="badge badge-warning ml-2">{{ $discountedStats['total_redemptions'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="surplus-tab" data-toggle="tab" href="#surplus" role="tab">
                        <span class="tab-icon">
                            <i class="fas fa-boxes text-purple"></i>
                        </span>
                        <span class="tab-label">{{ __('app.surplus_food') }}</span>
                        <span class="badge badge-purple ml-2">{{ $surplusStats['total_redemptions'] }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <!-- Free Food Tab -->
                <div class="tab-pane fade show active" id="free" role="tabpanel">
                    @include('school.food-breakdown-type', [
                        'redemptions' => $freeRedeemed,
                        'stats' => $freeStats,
                        'type' => 'free'
                    ])
                </div>

                <!-- Discounted Food Tab -->
                <div class="tab-pane fade" id="discounted" role="tabpanel">
                    @include('school.food-breakdown-type', [
                        'redemptions' => $discountedRedeemed,
                        'stats' => $discountedStats,
                        'type' => 'discounted'
                    ])
                </div>

                <!-- Surplus Food Tab -->
                <div class="tab-pane fade" id="surplus" role="tabpanel">
                    @include('school.food-breakdown-type', [
                        'redemptions' => $surplusRedeemed,
                        'stats' => $surplusStats,
                        'type' => 'surplus'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modern Stat Widget Styles */
    .modern-stat-widget {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        border-radius: 12px;
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: relative;
    }

    .modern-stat-widget::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--widget-color-start), var(--widget-color-end));
    }

    .modern-stat-widget:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .widget-primary {
        --widget-color-start: #4e73df;
        --widget-color-end: #224abe;
    }

    .widget-success {
        --widget-color-start: #1cc88a;
        --widget-color-end: #13855c;
    }

    .widget-warning {
        --widget-color-start: #f6c23e;
        --widget-color-end: #dda15e;
    }

    .widget-info {
        --widget-color-start: #36b9cc;
        --widget-color-end: #224abe;
    }

    .widget-icon {
        font-size: 2.5rem;
        margin-right: 1.5rem;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--widget-color-start), var(--widget-color-end));
        color: white;
        flex-shrink: 0;
    }

    .widget-content {
        flex: 1;
    }

    .widget-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .widget-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2e3338;
        margin-bottom: 0.25rem;
    }

    .widget-subtext {
        font-size: 0.8rem;
        color: #adb5bd;
    }

    /* Modern Tabs */
    .modern-tabs {
        border-bottom: 2px solid #e3e6f0;
        gap: 0.5rem;
    }

    .modern-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .modern-tabs .nav-link:hover {
        color: #495057;
        background-color: rgba(0, 0, 0, 0.02);
    }

    .modern-tabs .nav-link.active {
        color: #4e73df;
        border-bottom-color: #4e73df;
        background-color: transparent;
    }

    .tab-icon {
        font-size: 1.1rem;
    }

    .tab-label {
        font-weight: 500;
    }

    /* Counter Animation */
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .widget-value {
        animation: slideUp 0.6s ease-out;
    }

    /* Responsive */
    /* Force 4-column layout for widgets */
    .row.mb-4.g-3 > [class*="col-"] {
        flex: 0 0 calc(25% - 0.75rem);
        max-width: calc(25% - 0.75rem);
    }

    @media (max-width: 1200px) {
        .row.mb-4.g-3 > [class*="col-"] {
            flex: 0 0 calc(25% - 0.75rem);
            max-width: calc(25% - 0.75rem);
        }
    }

    @media (max-width: 992px) {
        .row.mb-4.g-3 > [class*="col-"] {
            flex: 0 0 calc(50% - 0.75rem);
            max-width: calc(50% - 0.75rem);
        }
    }

    @media (max-width: 768px) {
        .modern-stat-widget {
            flex-direction: column;
            text-align: center;
        }

        .widget-icon {
            margin-right: 0;
            margin-bottom: 1rem;
        }

        .widget-value {
            font-size: 1.75rem;
        }

        .row.mb-4.g-3 > [class*="col-"] {
            flex: 0 0 calc(50% - 0.75rem);
            max-width: calc(50% - 0.75rem);
        }
    }

    @media (max-width: 576px) {
        .row.mb-4.g-3 > [class*="col-"] {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<script>
    // Animated Counter Function
    function animateCounter(element, target, duration = 1500) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = Math.floor(target);
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Animated Currency Counter Function
    function animateCurrencyCounter(element, target, duration = 1500) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = '£' + target.toFixed(2);
                clearInterval(timer);
            } else {
                element.textContent = '£' + current.toFixed(2);
            }
        }, 16);
    }

    // Initialize counters on page load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.counter').forEach(element => {
            const target = parseInt(element.dataset.target);
            animateCounter(element, target);
        });

        document.querySelectorAll('.currency-counter').forEach(element => {
            const target = parseFloat(element.dataset.target);
            animateCurrencyCounter(element, target);
        });
    });
</script>
@endsection
