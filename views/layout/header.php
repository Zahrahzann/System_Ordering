<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Ordering</title>
    <link rel="stylesheet" href="/system_ordering/public/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
setInterval(() => {
    fetch('/system_ordering/public/notifications.php')
        .then(res => res.json())
        .then(data => {
            if (data.new) {
                Swal.fire({
                    icon: data.type,
                    title: 'Notifikasi SPV',
                    html: data.message,
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(err => console.error('Notif error:', err));
}, 10000); // cek tiap 10 detik
</script>
