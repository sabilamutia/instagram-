// Konfirmasi visual sederhana saat menambahkan komentar
document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function () {
        console.log("Komentar sedang dikirim...");
    });
});