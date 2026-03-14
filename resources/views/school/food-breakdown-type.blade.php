<!-- Type-Specific Statistics Widgets -->
<div class="type-stats-section mb-4">
    <div class="row mb-4 g-3 type-stats-grid">
        <!-- Heading spanning all columns -->
        <div class="col-12 mb-3">
            <h5 class="mb-0 text-muted">
                <i class="fas fa-chart-line"></i> Type-Specific Breakdown
            </h5>
        </div>
        <!-- Total Redemptions Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-primary type-widget">
                <div class="widget-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.total_redemptions') }}</div>
                    <div class="widget-value">{{ $stats['total_redemptions'] }}</div>
                    <div class="widget-subtext">This Type</div>
                </div>
            </div>
        </div>

        <!-- Total Value Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-success type-widget">
                <div class="widget-icon">
                    <i class="fas fa-pound-sign"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.total_value') }}</div>
                    <div class="widget-value">£{{ number_format($stats['total_value'], 2) }}</div>
                    <div class="widget-subtext">This Type</div>
                </div>
            </div>
        </div>

        <!-- Average Value Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-warning type-widget">
                <div class="widget-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.avg_value') }}</div>
                    <div class="widget-value">£{{ number_format($stats['average_value'], 2) }}</div>
                    <div class="widget-subtext">This Type</div>
                </div>
            </div>
        </div>

        <!-- Payment Collected Widget -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="modern-stat-widget widget-info type-widget">
                <div class="widget-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="widget-content">
                    <div class="widget-label">{{ __('app.payment_collected') }}</div>
                    <div class="widget-value">{{ $stats['total_collected'] }}<span style="font-size: 0.6em; margin-left: 0.25rem;">/{{ $stats['total_redemptions'] }}</span></div>
                    <div class="widget-subtext">This Type</div>
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
        font-size: 1.2rem;
        margin-right: 0.75rem;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--widget-color-start), var(--widget-color-end));
        color: white;
        flex-shrink: 0;
    }

    .widget-content {
        flex: 1;
    }

    .widget-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .widget-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2e3338;
        margin-bottom: 0.25rem;
    }

    .widget-subtext {
        font-size: 0.75rem;
        color: #adb5bd;
    }

    /* Type-specific widget styling */
    .type-widget {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.95));
        border-left: 3px solid;
    }

    .type-widget.widget-primary {
        border-left-color: #4e73df;
    }

    .type-widget.widget-success {
        border-left-color: #1cc88a;
    }

    .type-widget.widget-warning {
        border-left-color: #f6c23e;
    }

    .type-widget.widget-info {
        border-left-color: #36b9cc;
    }

    /* Grid layout for type stats */
    .type-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    .type-stats-grid > [class*="col-"] {
        grid-column: span 1;
    }

    @media (max-width: 1200px) {
        .type-stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .type-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .modern-stat-widget {
            flex-direction: column;
            text-align: center;
        }

        .widget-icon {
            margin-right: 0;
            margin-bottom: 0.75rem;
        }

        .widget-value {
            font-size: 1.5rem;
        }

        .widget-label {
            font-size: 0.75rem;
        }

        .type-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .type-stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .type-stats-section h5 {
        border-bottom: 2px solid #e3e6f0;
        padding-bottom: 0.75rem;
    }
</style>

<!-- Redemptions Table -->
<div class="table-responsive mt-4">
    @if($redemptions->count() > 0)
        <table class="table table-hover table-sm">
            <thead class="bg-light">
                <tr>
                    <th>{{ __('app.food_item') }}</th>
                    <th>{{ __('app.recipient') }}</th>
                    <th>{{ __('app.shop') }}</th>
                    <th>{{ __('app.amount_used') }}</th>
                    <th>{{ __('app.amount_owed') }}</th>
                    <th>{{ __('app.payment_status') }}</th>
                    <th>{{ __('app.redeemed_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($redemptions as $redemption)
                    <tr>
                        <td>
                            <strong>{{ $redemption->foodListing->item_name ?? 'N/A' }}</strong>
                            @if($redemption->foodListing)
                                <br>
                                <small class="text-muted">{{ Str::limit($redemption->foodListing->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $redemption->recipient->name ?? 'N/A' }}
                            @if($redemption->recipient)
                                <br>
                                <small class="text-muted">{{ $redemption->recipient->email }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $redemption->shop->name ?? 'N/A' }}
                            @if($redemption->shop && $redemption->shop->shopProfile)
                                <br>
                                <small class="text-muted">{{ $redemption->shop->shopProfile->shop_name ?? '' }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>£{{ number_format($redemption->amount_used, 2) }}</strong>
                        </td>
                        <td>
                            £{{ number_format($redemption->amount_owed_at_shop, 2) }}
                        </td>
                        <td>
                            @if($redemption->payment_collected)
                                <span class="badge badge-success">{{ __('app.collected') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('app.pending') }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $redemption->redeemed_at ? $redemption->redeemed_at->format('d M Y H:i') : 'N/A' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('app.no_redemptions_found') }}
        </div>
    @endif
</div>
