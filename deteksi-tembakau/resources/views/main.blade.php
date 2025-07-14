<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Deteksi Tembakau</title>
  @vite('resources/css/app.css')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body class="relative overflow-x-hidden bg-green-50 min-h-screen">

  <div class="grid grid-cols-2">
    <div class="flex justify-center items-center min-h-screen">
      <div class="text-left ml-40 max-w-2xl w-full px-6">
        <h1 class="text-6xl font-bold text-gray-800 mb-6 leading-tight">
          Deteksi Kualitas Tanaman Tembakau Secara Otomatis
        </h1>
        <p class="text-base text-gray-600 mb-4">
          Melihat kualitas tanaman tembakau Anda dengan sistem deteksi berbasis pengolahan citra menggunakan CNN dengan Arsitektur MobileNetV2.
        </p>
        <p class="text-lg text-gray-600 mb-6 font-extrabold italic">
          Mulai deteksi kualitas tembakau Anda sekarang — gratis dan instan!
        </p>

        <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col gap-4 mb-6">
          <div id="previewContainer" class="mb-2">
            <img id="previewImage" src="#" alt="Preview Gambar" class="w-[150px] h-[150px] object-cover rounded shadow-lg hidden" />
          </div>
          <div class="flex gap-4 flex-wrap">
            <label for="image" class="bg-[#88B04B] flex items-center gap-2 text-white py-2 px-6 rounded-lg shadow-md hover:bg-[#7DA453] hover:shadow-xl transition transform hover:scale-105 cursor-pointer w-max">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 3a1 1 0 0 1 .78.375l4 5a1 1 0 1 1-1.56 1.25L13 6.85V14a1 1 0 1 1-2 0V6.85L8.78 9.626a1 1 0 1 1-1.56-1.25l4-5A1 1 0 0 1 12 3ZM9 14v-1H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-4v1a3 3 0 1 1-6 0Zm8 2a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd"/>
              </svg>
              Upload Gambar
            </label>
            <input type="file" name="image" id="image" accept="image/*" class="hidden" required>

            <button id="submitBtn" type="submit" class="bg-[#F3D250] flex items-center gap-2 text-gray-800 py-2 px-6 rounded-lg shadow-md hover:bg-[#E0BC44] hover:shadow-xl transition transform hover:scale-105 cursor-pointer w-max">
              <svg id="loadingSpinner" class="w-6 h-6 text-gray-800 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span id="submitText">Cek Hasil</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="h-screen overflow-hidden relative">
      <img src="/images/bg.png" alt="Background" class="w-full h-full object-cover" />
      <img src="/images/TobaccoPlantImage.png" alt="Tobacco" class="absolute top-[73px] left-0 z-10 h-[600px] w-[600px]" />
    </div>
  </div>

  <!-- Popup -->
  <div id="popup" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-lg z-50 transform translate-x-full transition-transform duration-500 ease-in-out">
    <div class="p-6 h-full flex flex-col ">
      <h2 class="text-2xl font-bold mb-4 text-gray-800">Hasil Deteksi</h2>
      <p id="popupClass" class="text-gray-600 mb-4"></p>
      <div class="flex items-center gap-4 mb-6">
        <div class="flex flex-col items-center justify-center w-full">
           <div
            id="popupConfidence"
            class="relative flex justify-center items-center w-[160px] h-[160px] rounded-full shadow-xl/20"
            style="--progress: 0; background: conic-gradient(#88B04B calc(var(--progress) * 1%), #ddd 0);"
          >
            <div class="absolute w-[100px] h-[100px] bg-white rounded-full shadow-inner z-[1]"></div>
            <div class="relative z-[3] font-bold text-2xl text-[#4A7C1E] drop-shadow-sm">0%</div>
          </div>
          <p id="popupDescription" class="text-center mt-20 text-gray-700 text-sm"></p>
        </div>   
      </div>
      <div class="mt-auto">
        <button onclick="closePopup()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
          Tutup
        </button>
      </div>
    </div>
  </div>

  <script>
    const imageInput = document.getElementById('image');
    const previewImage = document.getElementById('previewImage');
    const popupImage = document.getElementById('popupImage');
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const submitText = document.getElementById('submitText');
    const uploadForm = document.getElementById('uploadForm');

    imageInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImage.src = e.target.result;
          previewImage.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      }
    });

    uploadForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      submitBtn.disabled = true;
      loadingSpinner.classList.remove('hidden');
      submitText.textContent = 'Memproses...';

      const formData = new FormData();
      formData.append('image', imageInput.files[0]);

      try {
        const response = await fetch(`{{ route('predict.predict') }}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
          body: formData
        });

        if (!response.ok) throw new Error('Gagal mengirim data');

        const data = await response.json();
        
        if (data.class === null || data.confidence < 0.75) {
          document.getElementById('popupClass').innerHTML = `<span class="text-red-600 font-semibold">Gambar tidak dikenali</span>`;
          
          const popupConfidence = document.getElementById('popupConfidence');
          popupConfidence.querySelector('div:last-child').innerText = `0%`;
          popupConfidence.style.setProperty('--progress', 0);

          document.getElementById('popupDescription').innerText = "Keterangan tidak tersedia karena gambar tidak dikenali sebagai daun tembakau.";

          document.getElementById('popup').classList.remove('translate-x-full');
          return;
        }

        document.getElementById('popupClass').innerHTML = `Kelas: <span class="font-semibold">${data.class}</span>`;
        const confidence = Math.round(data.confidence * 100);
        const popupConfidence = document.getElementById('popupConfidence');
        popupConfidence.querySelector('div:last-child').innerText = `${confidence}%`;
        popupConfidence.style.setProperty('--progress', confidence);

        const popupDescription = document.getElementById('popupDescription');

let description = '';
switch (data.class) {
  case 'A':
    description = 'Daun tembakau dengan kualitas terbaik ditandai oleh warna kuning cerah yang merata, permukaan yang bersih dan halus, serta kondisi fisik yang utuh tanpa kerusakan seperti sobekan atau bercak. Daun pada kategori ini memiliki tekstur yang baik dan elastisitas yang optimal, mencerminkan tingkat kematangan yang ideal. Ciri-ciri ini menjadikan daun sangat cocok untuk proses pengolahan produk tembakau kelas atas yang menuntut kualitas tinggi dan konsistensi bahan baku.';
    break;
  case 'B':
    description = 'Daun tembakau kualitas sedang umumnya memiliki warna hijau yang masih cukup merata, namun disertai sedikit perubahan warna atau tekstur di beberapa bagian. Meskipun tidak sebaik kategori terbaik, daun ini tetap memiliki struktur yang layak dan belum menunjukkan kerusakan parah. Dengan proses sortir atau pengeringan tambahan, daun dalam kategori ini masih bisa digunakan untuk produk tembakau kelas menengah yang tidak terlalu menuntut kesempurnaan bahan.';
    break;
  case 'C':
    description = 'Daun tembakau dalam kategori kualitas rendah biasanya berwarna merah kecoklatan dengan permukaan yang tidak rata, menunjukkan adanya kerusakan seperti bercak, lubang, atau tekstur kasar. Kondisi ini sering kali terjadi akibat kesalahan dalam proses panen, pengeringan, atau penyimpanan yang kurang optimal. Daun jenis ini tidak memenuhi standar untuk produk tembakau berkualitas tinggi dan umumnya hanya digunakan untuk produk dengan nilai ekonomi rendah atau memerlukan pengolahan khusus.';
    break;
  default:
    description = 'Keterangan tidak tersedia.';
}

popupDescription.innerText = description;


        if (data.label_image) {
          popupImage.src = data.label_image;
          popupImage.classList.remove('hidden');
        }

        document.getElementById('popup').classList.remove('translate-x-full');
      } catch (err) {
        alert("Terjadi kesalahan saat mengirim data!");
      } finally {
        submitBtn.disabled = false;
        loadingSpinner.classList.add('hidden');
        submitText.textContent = 'Cek Hasil';
      }
    });

    function closePopup() {
      document.getElementById('popup').classList.add('translate-x-full');
    }
  </script>
</body>
</html>
