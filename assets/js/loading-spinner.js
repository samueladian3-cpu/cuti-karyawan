 document.addEventListener('DOMContentLoaded', function() {
        const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.add('hidden');
    }
});

    window.addEventListener('beforeunload', function() {
        const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.remove('hidden');
    }
});

function showSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.classList.remove('hidden');
}

function hideSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.classList.add('hidden');
}

// Tampilkan spinner saat submit form
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    if (forms.length === 0) {
        console.warn('⚠️ Tidak ada form ditemukan di halaman ini');
        return;
    }
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Jangan tampilkan spinner jika form submit ke halaman lain
            // (biarkan transition spinner menghilang otomatis via beforeunload)
            if (!this.hasAttribute('data-no-spinner')) {
                showSpinner();
            }
        });
    });
    
    console.log(`✓ ${forms.length} form event listener berhasil ditambahkan`);
});

// Contoh AJAX request ke API
function fetchApiData(type = 'dashboard') {
    showSpinner();
    
    // Gunakan /api/data.php (dengan .php extension)
    const apiUrl = `/api/data.php?type=${encodeURIComponent(type)}`;
    
    console.log('📤 Mengirim request ke:', apiUrl);
    
    fetch(apiUrl)
        .then(res => {
            // Cek content-type sebelum parse JSON
            const contentType = res.headers.get('content-type');
            
            if (!res.ok) {
                hideSpinner();
                
                const statusMessages = {
                    400: 'Request tidak valid',
                    401: 'Anda harus login terlebih dahulu',
                    404: 'Endpoint tidak ditemukan: ' + apiUrl,
                    500: 'Server error - hubungi administrator'
                };
                
                const message = statusMessages[res.status] || `HTTP Error ${res.status}`;
                console.error('❌ HTTP Error:', res.status, message);
                console.error('Content-Type:', contentType);
                throw new Error(message);
            }
            
            // Validasi content-type
            if (!contentType || !contentType.includes('application/json')) {
                hideSpinner();
                console.error('❌ Response bukan JSON. Content-Type:', contentType);
                console.error('Response HTML:', res);
                throw new Error('Response tidak valid - bukan JSON');
            }
            
            return res.json();
        })
        .then(response => {
            hideSpinner();
            
            if (!response || typeof response !== 'object') {
                throw new Error('Response format tidak valid');
            }
            
            if (response.success) {
                console.log('✓ Data berhasil diambil:', response.data);
                return response.data;
            } else {
                console.error('API Error:', response.message);
                alert('⚠️ ' + (response.message || 'Error tidak diketahui'));
            }
        })
        .catch(error => {
            hideSpinner();
            console.error('❌ Full Error Stack:', error);
            alert('❌ Gagal: ' + (error.message || 'Error tidak diketahui'));
        });
}
