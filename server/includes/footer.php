
            </div>
        </main>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed bottom-4 right-4 z-[200] flex flex-col gap-2"></div>

<!-- Custom JS -->
<script src="/assets/js/app.js?v=20260316"></script>
<script>
    // Lucide icons initialize
    lucide.createIcons();
    // PWA Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }
</script>
</body>
</html>
