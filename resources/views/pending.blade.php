<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval — eVoucher</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>* { margin:0; padding:0; box-sizing:border-box; } body { font-family:'Inter',sans-serif; background:#f8fafc; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }</style>
</head>
<body>
    <div style="max-width:480px;width:100%;text-align:center;">
        <div style="width:72px;height:72px;background:linear-gradient(135deg,#fef9c3,#fde68a);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <i class="fas fa-clock" style="font-size:32px;color:#ca8a04;"></i>
        </div>
        <h1 style="font-size:24px;font-weight:900;color:#0f172a;margin-bottom:10px;">Account Pending Approval</h1>
        <p style="color:#64748b;font-size:14.5px;line-height:1.6;margin-bottom:28px;">
            Thank you for registering, <strong>{{ Auth::user()->name }}</strong>. Your account is currently under review by our team. You will receive an email notification once your account is approved.
        </p>
        <div style="background:#fff;border-radius:14px;border:1px solid #f1f5f9;padding:20px;margin-bottom:24px;text-align:left;">
            <div style="font-size:12.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Your Account Details</div>
            <div style="display:grid;gap:8px;">
                <div style="display:flex;justify-content:space-between;font-size:13.5px;">
                    <span style="color:#64748b;">Name</span><strong style="color:#0f172a;">{{ Auth::user()->name }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13.5px;">
                    <span style="color:#64748b;">Email</span><strong style="color:#0f172a;">{{ Auth::user()->email }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13.5px;">
                    <span style="color:#64748b;">Role</span>
                    <strong style="color:#0f172a;">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</strong>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:#f1f5f9;color:#374151;font-weight:700;padding:12px 28px;border-radius:10px;border:none;cursor:pointer;font-size:14px;font-family:'Inter',sans-serif;">
                <i class="fas fa-arrow-right-from-bracket" style="margin-right:6px;"></i> Sign Out
            </button>
        </form>
    </div>
</body>
</html>
