<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'user_type': '{{ auth()->check() ? 'logged_in' : 'guest' }}',
        'environment': '{{ app()->environment() }}',
        'page_type': '{{ $pageType ?? 'other' }}'
    });
</script>
