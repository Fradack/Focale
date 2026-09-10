@if (\App\Support\Plugins::enabled('tracking'))
<script>
(function () {
  if (document.cookie.indexOf('focale_consent=accepted') === -1) return;

  const beaconUrl = @json(route('public.tracking.beacon'));
  const startedAt = Date.now();
  let sent = false;

  function send() {
    if (sent) return;
    sent = true;
    const duration = Math.round((Date.now() - startedAt) / 1000);
    const payload = new Blob([JSON.stringify({ path: location.pathname, duration: duration })], { type: 'application/json' });
    navigator.sendBeacon(beaconUrl, payload);
  }

  window.addEventListener('pagehide', send);
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') send();
  });
})();
</script>
@endif
