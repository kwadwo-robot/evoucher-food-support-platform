<!-- Detailed Redemptions Table -->
<div class="table-responsive">
    @if($redemptions->count() > 0)
        <table class="table table-hover table-sm" style="table-layout: fixed; width: 100%;">
            <thead class="bg-light">
                <tr>
                    <th style="width: 20%; word-wrap: break-word; overflow-wrap: break-word;">{{ __('app.food_item') }}</th>
                    <th style="width: 15%; word-wrap: break-word; overflow-wrap: break-word;">{{ __('app.recipient') }}</th>
                    <th style="width: 15%; word-wrap: break-word; overflow-wrap: break-word;">{{ __('app.shop') }}</th>
                    <th style="width: 12%; word-wrap: break-word; overflow-wrap: break-word; text-align: center;">{{ __('app.amount_used') }}</th>
                    <th style="width: 12%; word-wrap: break-word; overflow-wrap: break-word; text-align: center;">{{ __('app.amount_owed') }}</th>
                    <th style="width: 12%; word-wrap: break-word; overflow-wrap: break-word; text-align: center;">{{ __('app.payment_status') }}</th>
                    <th style="width: 14%; word-wrap: break-word; overflow-wrap: break-word;">{{ __('app.redeemed_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($redemptions as $redemption)
                    <tr>
                        <td style="word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle;">
                            <strong>{{ $redemption->foodListing->item_name ?? 'N/A' }}</strong>
                            @if($redemption->foodListing)
                                <br>
                                <small class="text-muted">{{ Str::limit($redemption->foodListing->description, 50) }}</small>
                            @endif
                        </td>
                        <td style="word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle;">
                            {{ $redemption->recipient->name ?? 'N/A' }}
                            @if($redemption->recipient)
                                <br>
                                <small class="text-muted">{{ $redemption->recipient->email }}</small>
                            @endif
                        </td>
                        <td style="word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle;">
                            {{ $redemption->shop->name ?? 'N/A' }}
                            @if($redemption->shop && $redemption->shop->shopProfile)
                                <br>
                                <small class="text-muted">{{ $redemption->shop->shopProfile->shop_name ?? '' }}</small>
                            @endif
                        </td>
                        <td style="word-wrap: break-word; overflow-wrap: break-word; text-align: center; vertical-align: middle;">
                            <strong>£{{ number_format($redemption->amount_used, 2) }}</strong>
                        </td>
                        <td style="word-wrap: break-word; overflow-wrap: break-word; text-align: center; vertical-align: middle;">
                            £{{ number_format($redemption->amount_owed_at_shop, 2) }}
                        </td>
                        <td style="word-wrap: break-word; overflow-wrap: break-word; text-align: center; vertical-align: middle;">
                            @if($redemption->payment_collected)
                                <span class="badge badge-success">{{ __('app.collected') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('app.pending') }}</span>
                            @endif
                        </td>
                        <td style="word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle;">
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
