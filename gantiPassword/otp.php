<?php
declare(strict_types=1);
require '../vendor/autoload.php';
$secret = 'XVQ2UIGO75XRUKJO';
$link = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate('MindshareHub', $secret, 'MindshareHub');
$g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

if (isset($_POST['submit'])) {
    $code = $_POST['pass-code'];
    if ($g->checkCode($secret, $code)) {
        header("Location: resetPassword.php");
    } else {
        echo "<script>alert('Pin Salah'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ganti Kata Sandi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
  <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
  <style>
    @tailwind base;
    @tailwind components;
    @tailwind utilities;

    @layer utilities {
      .bg-customPurple {
        --tw-bg-opacity: 1;
        background-color: rgba(43, 27, 84, var(--tw-bg-opacity));
      }
    }
  </style>
</head>
<body class="bg-customPurple h-screen flex items-center justify-center m-0">
  <!-- Kontainer Utama -->
  <div class="flex flex-col md:flex-row bg-customPurple shadow-lg overflow-hidden w-full h-screen">
    <!-- Bagian Kiri: Ilustrasi -->
    <div class="relative flex-grow bg-cover bg-center h-full z-100" style="background-image: url('/Asset/poster1.png');">
      <div class="absolute inset-0 bg-customPurple"></div>
      <img src="/Asset/poster1.png" alt="Illustration" class="absolute inset-0 object-cover w-full h-full opacity-55" />
    </div>

    <!-- Bagian Kanan: Form Login -->
    <!-- https://lottie.host/2c9557a7-b65b-4d41-8e57-d6e566c92891/0uJzquhaU2.lottie -->
    <div class="w-full md:w-8/12 p-8 bg-customPurple text-white flex flex-col justify-center h-full relative overflow-hidden">
      <!-- Animasi Kanan Atas -->
      <dotlottie-player src="https://lottie.host/2c9557a7-b65b-4d41-8e57-d6e566c92891/0uJzquhaU2.lottie" 
        background="transparent" 
        speed="1" 
        style="width: 1500px; height: 1500px" 
        loop autoplay 
        class="absolute top-[-500px] right-[-500px] z-0 opacity-70">
      </dotlottie-player>
    
      <!-- Animasi Kiri Bawah -->
      <dotlottie-player src="https://lottie.host/2c9557a7-b65b-4d41-8e57-d6e566c92891/0uJzquhaU2.lottie" 
        background="transparent" 
        speed="1" 
        style="width: 1500px; height: 1500px" 
        loop autoplay 
        class="absolute bottom-[-500px] left-[-500px] z-0 opacity-70">
      </dotlottie-player>
    
      <!-- Konten Form Login -->
      <div class="relative z-10">
        <div class="text-center mb-6">
          <div class="w-32 h-32 mx-auto mb-3 flex items-center justify-center">
            <img src="/Asset/Logo MindsahreHub-07.png" alt="Logo" class="w-32 h-32">
          </div>
          
          <h2 class="text-4xl font-bold">Scan QR Code dengan aplikasi Authenticator</h2>
          <div class="flex justify-center justify-items-center mt-6">
            <img src="<?=$link;?>" alt="bar code">
          </div>
        </div>
    
        <div class="flex justify-center items-center">
          <form action="resetPassword.html" method="POST" class="space-y-6 p-5 max-w-md w-full rounded-lg shadow-md bg-customPurple opacity-90">
            <div>
              <label for="pass-code" class="block text-lg font-medium mb-1">Kode OTP</label>
              <input type="text" id="pass-code" name="pass-code" placeholder="Masukkan Kode OTP" maxlength="6" required
                class="w-full px-3 py-1.5 rounded-lg bg-gray-50 text-black border border-purple-600 focus:ring focus:ring-purple-400 outline-none text-sm">
            </div>
            <button type="submit" name="submit"
              class="w-full py-1.5 bg-white hover:bg-purple-900 hover:text-white text-purple-900 text-lg font-semibold rounded-lg shadow-lg">
              Konfimasi OTP
            </button>
            <p class="text-center mt-4 text-sm">
              <a href="/login/login.html" class="text-white hover:underline font-semibold">Masuk Disini</a> | <a href="/register/register.html" class="text-white hover:underline font-semibold">Daftar Disini</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
