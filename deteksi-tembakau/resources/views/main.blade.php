<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deteksi Tembakau</title>
    @vite('resources/css/app.css')
</head>
<body>
    
<div class="grid grid-cols-2 bg-green-50 min-h-screen">
    <div class=" max-h-full ">
        <div class="flex justify-center items-center h-screen">
            <div class="text-left ml-52 max-w-2xl w-full px-6">   
                <h1 class="text-6xl font-bold  text-gray-800 mb-6 leading-tight">Deteksi Kualitas Tanaman Tembakau dengan Teknologi Citra</h1>
                <p class="text-base  text-gray-600 mb-4 ">Melihat kualitas tanaman tembakau Anda dengan sistem deteksi berbasis pengolahan citra menggunakan CNN (Convolutional Neural Network) untuk mengetahui seberapa baik tembakau Anda.</p>
                <p class="text-lg  text-gray-600 mb-6">Mulai deteksi kualitas tembakau Anda sekarang — gratis dan instan!</p>
                <div class="flex gap-4">
                    <button
                        class="bg-[#88B04B] text-white py-2 px-6 rounded-lg shadow-md hover:bg-[#7DA453] hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-105 cursor-pointer"
                    >
                        📤 Upload Gambar
                    </button>
                    <button
                        class="bg-[#F3D250] text-gray-800 py-2 px-6 rounded-lg shadow-md hover:bg-[#E0BC44] hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-105 cursor-pointer"
                    >
                        🔍 Cek Hasil
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="h-screen overflow-hidden">
        <img src="/images/bg.png" alt="Background" class="w-full h-full">
    </div>

</div>

</body>
</html>