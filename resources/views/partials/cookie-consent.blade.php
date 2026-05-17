@php
    $consentCookie = request()->cookie('laladia_consent');
    $hasDecided    = in_array($consentCookie, ['granted', 'denied']);
@endphp

@unless ($hasDecided)
<div id="cookieConsentBanner"
     style="position:fixed;bottom:0;left:0;right:0;z-index:9999;
            background:#1a1a1a;color:#fff;padding:16px 24px;
            display:flex;align-items:center;justify-content:space-between;
            flex-wrap:wrap;gap:12px;font-size:13px;line-height:1.5;">
    <p style="margin:0;max-width:680px;">
        We use cookies for analytics and marketing to improve your experience.
        <a href="{{ route('privacy') }}" style="color:#d4a853;text-decoration:underline;">Privacy Policy</a>
    </p>
    <div style="display:flex;gap:10px;flex-shrink:0;">
        <button onclick="consentDecline()"
                style="padding:8px 18px;border-radius:6px;border:1px solid #555;
                       background:transparent;color:#ccc;cursor:pointer;font-size:13px;">
            Decline
        </button>
        <button onclick="consentAccept()"
                style="padding:8px 18px;border-radius:6px;border:none;
                       background:#d4a853;color:#1a1a1a;cursor:pointer;
                       font-weight:600;font-size:13px;">
            Accept
        </button>
    </div>
</div>
<script>
function _setConsentCookie(value) {
    var d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    document.cookie = 'laladia_consent=' + value + '; expires=' + d.toUTCString()
        + '; path=/; SameSite=Lax';
}
function consentAccept() {
    _setConsentCookie('granted');
    if (typeof gtag === 'function') {
        gtag('consent', 'update', {
            ad_storage:        'granted',
            analytics_storage: 'granted',
            ad_user_data:      'granted',
            ad_personalization:'granted',
        });
    }
    document.getElementById('cookieConsentBanner').remove();
}
function consentDecline() {
    _setConsentCookie('denied');
    document.getElementById('cookieConsentBanner').remove();
}
</script>
@endunless
