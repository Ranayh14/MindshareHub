<?php
declare(strict_types=1);
require '../vendor/autoload.php';
$secret = 'XVQ2UIGO75XRUKJO';
$link = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate('MindshareHub', $secret, 'MindshareHub');
$g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

$error = ''; // Variabel untuk menyimpan pesan error

if (isset($_POST['submit'])) {
    $code = $_POST['pass-code'];
    if ($g->checkCode($secret, $code)) {
        header("Location: gpTahap3.html");
    } else {
      $error = "Pin Salah. Silakan coba lagi.";
    }
}

// // Redirect jika mengisi email
// if (!isset($_SESSION['email'])) {
//   header("Location: ../gantiPassword/gpTahap1.html");
//   exit;
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masukkan Kode OTP</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
  <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
  <style>
    @tailwind base;
    @tailwind components;
    @tailwind utilities;

    @layer utilities {
      .bg-customPurple {
        background-color: rgba(43, 27, 84, 1);
      }
    }

    /* Media query untuk menyembunyikan gambar di tampilan mobile */
    @media (max-width: 768px) {
      .hide-on-mobile {
        display: none;
      }
    }
  </style>
</head>
<body class="bg-customPurple h-screen flex items-center justify-center m-0">
  <!-- Kontainer Utama -->
  <div class="flex flex-col md:flex-row bg-customPurple shadow-lg overflow-hidden w-full h-screen">
    <!-- Bagian Kiri: Ilustrasi -->
    <div class="relative flex-grow bg-cover bg-center h-full hide-on-mobile" style="background-image: url('/Asset/poster1.png');">
      <img src="/Asset/poster1.png" alt="Illustration" class="absolute inset-0 object-cover w-full h-full opacity-55" />
    </div>

    <!-- Bagian Kanan: Form Masukkan Kode OTP -->
    <div class="w-full md:w-8/12 p-8 flex flex-col justify-center h-full relative overflow-hidden">
      <!-- Animasi Kanan Atas -->
      <dotlottie-player src="https://lottie.host/2c9557a7-b65b-4d41-8e57-d6e566c92891/0uJzquhaU2.lottie" 
        background="transparent" 
        speed="1" 
        style="width: 1500px; height: 1500px;" 
        loop autoplay 
        class="absolute top-[-500px] right-[-500px] z-0 opacity-70">
      </dotlottie-player>
    
      <!-- Animasi Kiri Bawah -->
      <dotlottie-player src="https://lottie.host/2c9557a7-b65b-4d41-8e57-d6e566c92891/0uJzquhaU2.lottie" 
        background="transparent" 
        speed="1" 
        style="width: 1500px; height: 1500px;" 
        loop autoplay 
        class="absolute bottom-[-500px] left-[-500px] z-0 opacity-70">
      </dotlottie-player>
    
      <!-- Konten Form Masukkan Kode OTP -->
      <div class="relative z-10">
        <div class="text-center mb-6">
          <div class="w-32 h-32 mx-auto mb-3 flex items-center justify-center">
            <img src="/Asset/Logo MindsahreHub-07.png" alt="Logo" class="w-32 h-32">
          </div>
          
          <h2 class="text-4xl font-bold text-white">Scan QR Code dengan aplikasi Authenticator</h2>
          <div class="flex justify-center justify-items-center mt-6">
            <img src="<?=$link;?>" alt="bar code">
          </div>
        </div>
    
        <!-- Alert Error -->
        <?php if ($error): ?>
          <div id="alert-password" class="max-w-md mx-auto relative flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 rounded-lg fade-in" role="alert">
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 1 0 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div class="ms-3 text-sm font-medium">
              <?= htmlspecialchars($error); ?>
            </div>
            <button type="button" aria-label="Close" onclick="closeAlert()" style="background: none; border: none; cursor: pointer; padding: 0; position: absolute; top: 8px; right: 8px;">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="6" y1="6" x2="18" y2="18"></line>
                <line x1="6" y1="18" x2="18" y2="6"></line>
              </svg>
            </button>
          </div>
        <?php endif; ?>

        <div class="flex justify-center items-center">
          <form action="gpTahap2.php" method="POST" class="space-y-6 p-5 max-w-md w-full rounded-lg shadow-md bg-white bg-opacity-20 backdrop-blur-lg">
            <div>
              <label for="pass-code" class="block text-lg font-medium mb-1 text-white">Kode OTP</label>
              <input type="text" id="pass-code" name="pass-code" placeholder="Masukkan Kode OTP" maxlength="6" required
                class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-800 border border-purple-600 focus:ring focus:ring-purple-400 outline-none text-sm transition duration-200 ease-in-out shadow-sm hover:shadow-lg">
            </div>
            <button type="submit" name="submit"
              class="w-full py-2 bg-white hover:bg-purple-900 hover:text-white text-purple-900 text-lg font-semibold rounded-lg shadow-lg transition duration-200 ease-in-out transform hover:scale-105">
              Konfirmasi OTP
            </button>
            <p class="text-center mt-4 text-sm text-white">
              <a href="/login/login.html" class="text-white hover:underline font-semibold">Masuk Disini</a> | 
              <a href="/register/register.html" class="text-white hover:underline font-semibold">Daftar Disini</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function closeAlert() {
      const alertElement = document.getElementById('alert-password');
      if (alertElement) {
        alertElement.style.display = 'none'; // Sembunyikan alert
      }
    }
  </script>
</body>
</html>
