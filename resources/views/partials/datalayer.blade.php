<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'user_type':   '{{ auth()->check() ? 'logged_in' : 'guest' }}',
        'user_id':     {{ auth()->check() ? auth()->id() : 'null' }},
        'environment': '{{ app()->environment() }}',
        'page_type':   '{{ $pageType ?? 'other' }}'
    });

    @if(!empty($ga4))
    window.__ga4__ = @json($ga4);
    @endif
</script>
