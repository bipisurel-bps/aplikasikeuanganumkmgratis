import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Format angka ke rupiah: 12500 -> "Rp 12.500"
window.rupiah = (n) => 'Rp ' + Math.round(Number(n) || 0).toLocaleString('id-ID');

Alpine.start();
