<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $listing->item_name }} — eVoucher Platform</title>
  <link rel="icon" type="image/png" href="https://8000-iy05dm357wei0slf69qln-6ebb0726.us2.manus.computer/images/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; color: #0f172a; background: #f8fafc; }
    .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    .badge-green { background: #f0fdf4; color: #15803d; }
    .badge-orange { background: #fff7ed; color: #c2410c; }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all .15s; }
    .btn-secondary { background: #e2e8f0; color: #334155; }
    .btn-secondary:hover { background: #cbd5e1; }
    .btn-primary { background: #16a34a; color: white; }
    .btn-primary:hover { background: #15803d; }
  </style>
</head>
<body>
  <div style="max-width: 1200px; margin: 0 auto; padding: 24px;">
    <div style="margin-bottom: 24px;">
      <a href="{{ route('food.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Browse
      </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
      <!-- Main Details -->
      <div>
        <div class="card" style="margin-bottom: 20px;">
          @if($listing->image_url)
          <img src="{{ $listing->image_url }}" style="width:100%;height:300px;object-fit:cover;border-radius:12px 12px 0 0" alt="{{ $listing->item_name }}">
          @else
          <div style="width:100%;height:300px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);display:flex;align-items:center;justify-content:center;font-size:80px;border-radius:12px 12px 0 0">🥖</div>
          @endif
          <div style="padding: 24px;">
            <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 16px;">
              <h1 style="font-size:28px;font-weight:800;color:#0f172a">{{ $listing->item_name }}</h1>
              <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end">
                <span class="badge badge-green"><i class="fas fa-check mr-1"></i>{{ $listing->quantity }} available</span>
                @if($listing->listing_type === 'discounted' && $listing->original_price > 0)
                <span class="badge badge-orange"><i class="fas fa-tag mr-1"></i>{{ round((1 - $listing->discounted_price / $listing->original_price) * 100) }}% off</span>
                @endif
              </div>
            </div>

            @if($listing->description)
            <p style="font-size:15px;color:#64748b;line-height:1.7;margin-bottom:20px">{{ $listing->description }}</p>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
              <div style="padding:16px;background:#f8fafc;border-radius:10px">
                <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Expiry Date</div>
                <div style="font-size:15px;font-weight:700;color:#ef4444"><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($listing->expiry_date)->format('d M Y') }}</div>
              </div>
              <div style="padding:16px;background:#f8fafc;border-radius:10px">
                <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Shop</div>
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ $listing->shop->name ?? 'Local Shop' }}</div>
              </div>
              <div style="padding:16px;background:#f8fafc;border-radius:10px">
                <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Collection Address</div>
                <div style="font-size:13px;font-weight:600;color:#334155">{{ $listing->collection_address }}</div>
              </div>
              <div style="padding:16px;background:#f8fafc;border-radius:10px">
                <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Collection Time</div>
                <div style="font-size:13px;font-weight:600;color:#334155">{{ $listing->collection_time ?? 'Contact shop' }}</div>
              </div>
            </div>

            @if($listing->collection_instructions)
            <div style="padding:16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px">
              <div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:6px"><i class="fas fa-info-circle mr-1"></i> Collection Instructions</div>
              <div style="font-size:13px;color:#78350f">{{ $listing->collection_instructions }}</div>
            </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Info Panel -->
      <div>
        <div class="card" style="margin-bottom: 20px;">
          <div style="padding: 20px; border-bottom: 1px solid #e2e8f0;">
            <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 16px;">Item Summary</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
              <span style="font-size:13px;color:#64748b">Quantity</span>
              <span style="font-size:13px;font-weight:600;color:#0f172a">{{ $listing->quantity }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
              <span style="font-size:13px;color:#64748b">Voucher Cost</span>
              <span style="font-size:14px;font-weight:700;color:#16a34a">{{ $listing->voucher_value > 0 ? '£'.number_format($listing->voucher_value,2) : 'Free' }}</span>
            </div>
            @if($listing->listing_type === 'discounted')
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
              <span style="font-size:13px;color:#64748b">Original Price</span>
              <span style="font-size:13px;font-weight:600;color:#94a3b8"><del>£{{ number_format($listing->original_price,2) }}</del></span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between;">
              <span style="font-size:13px;color:#64748b">Status</span>
              <span class="badge badge-green">{{ ucfirst($listing->status) }}</span>
            </div>
          </div>
          <div style="padding: 20px;">
            <div style="padding: 12px; background: #f0fdf4; border-radius: 8px; font-size: 13px; color: #15803d; text-align: center;">
              <i class="fas fa-info-circle mr-1"></i> 
              @if($listing->voucher_value > 0)
                This item costs <strong>£{{ number_format($listing->voucher_value, 2) }}</strong> from your voucher
              @else
                This item is <strong>free</strong> — no voucher value deducted
              @endif
            </div>
          </div>
        </div>

        <div class="card">
          <div style="padding: 20px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 16px;">🎫</div>
            <div style="font-size: 15px; font-weight: 700; color: #334155; margin-bottom: 8px;">Sign In to Redeem</div>
            <div style="font-size: 13px; color: #94a3b8; margin-bottom: 16px;">You need to be logged in as a recipient to redeem this item</div>
            <a href="{{ route('login') }}" class="btn btn-primary" style="justify-content: center; width: 100%;">
              <i class="fas fa-sign-in-alt"></i> Sign In
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
