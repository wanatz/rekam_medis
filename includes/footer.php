        <footer class="footer">
            Aplikasi Rekam Medis Sederhana &mdash; data yang digunakan adalah data fiktif untuk pembelajaran.
        </footer>
    </main>
</div>
<script>
// Konfirmasi sebelum menghapus data
document.querySelectorAll('[data-konfirmasi]').forEach(function (el) {
    el.addEventListener('click', function (ev) {
        if (!confirm(el.getAttribute('data-konfirmasi'))) {
            ev.preventDefault();
        }
    });
});
</script>
</body>
</html>
