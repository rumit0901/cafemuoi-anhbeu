/**
 * File cấu hình tập trung cho toàn bộ ứng dụng Frontend.
 * Đây là nguồn duy nhất quản lý các hằng số môi trường.
 */
const isLocal = typeof window !== 'undefined' && 
  (window.location.hostname === 'localhost' || 
   window.location.hostname === '127.0.0.1' || 
   window.location.protocol === 'file:');

const CONFIG = {
  // Domain Backend REST API (PHP Slim Framework)
  API_BASE_URL: isLocal ? 'http://127.0.0.1:8000' : 'https://cafemuoi-anhbeu.great-site.net',

  // Mapbox GL JS Public Access Token (dành cho trang Tìm cửa hàng)
  MAPBOX_TOKEN: 'pk.eyJ1IjoiY2FmZW11b2ktYW5oYmV1IiwiYSI6ImNseDhpbXo1cTAxb28ya3F4ZXN0bm9qZDMifQ.demo_token_place_holder'
};
