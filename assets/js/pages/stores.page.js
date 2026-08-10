/**
 * Logic trang Tìm cửa hàng (stores.html) tích hợp bản đồ Mapbox GL JS.
 * Phụ thuộc: jQuery, jsRender, Mapbox GL JS SDK, API (api.js), CONFIG (config.js).
 */
let map = null;

$(document).ready(function () {
  // 1. Tải danh sách cửa hàng
  loadStoresData();
});

/**
 * Tải danh sách cửa hàng từ API và khởi tạo bản đồ Mapbox
 */
async function loadStoresData() {
  const $container = $('#stores-list-container');

  try {
    // Gọi API lấy danh sách chi nhánh
    let stores = await API.getStores();

    if (!stores || stores.length === 0) {
      stores = getFallbackStores();
    }

    // Render danh sách cửa hàng bên sidebar bằng jsRender
    const html = $.templates('#storeItemTemplate').render(stores);
    $container.html(html);

    // Khởi tạo bản đồ Mapbox GL JS với mảng chi nhánh
    initMapboxMap(stores);

    // Lắng nghe sự kiện click nút "Xem bản đồ"
    $(document).on('click', '.btn-focus-map, .store-item-card', function () {
      const lat = parseFloat($(this).data('lat'));
      const lng = parseFloat($(this).data('lng'));

      if (map && !isNaN(lat) && !isNaN(lng)) {
        // Fly animation tới vị trí chi nhánh
        map.flyTo({
          center: [lng, lat],
          zoom: 16,
          essential: true
        });
      }
    });

  } catch (error) {
    console.error('[stores.page.js] Lỗi khi tải chi nhánh API. Dùng fallback.', error);
    const fallbackStores = getFallbackStores();
    const html = $.templates('#storeItemTemplate').render(fallbackStores);
    $container.html(html);
    initMapboxMap(fallbackStores);
  }
}

/**
 * Khởi tạo bản đồ Mapbox GL JS
 * @param {Array} stores - Danh sách cửa hàng kèm tọa độ lat/lng
 */
function initMapboxMap(stores) {
  // Kiểm tra SDK Mapbox GL JS đã sẵn sàng chưa
  if (typeof mapboxgl === 'undefined') {
    console.warn('[stores.page.js] Mapbox GL JS SDK chưa được nhúng hoặc bị chặn.');
    $('#map-container').html('<div class="error-fallback"><p>Không thể hiển thị bản đồ trực tuyến. Vui lòng xem danh sách chi nhánh bên cạnh.</p></div>');
    return;
  }

  // Gán token từ CONFIG
  mapboxgl.accessToken = CONFIG.MAPBOX_TOKEN;

  // Tọa độ trung tâm mặc định (TP.HCM)
  const defaultCenter = [106.7009, 10.7769];

  try {
    // Khởi tạo đối tượng Map
    map = new mapboxgl.Map({
      container: 'map-container',
      style: 'mapbox://styles/mapbox/streets-v12',
      center: defaultCenter,
      zoom: 12
    });

    // Thêm Navigation Controls (zoom in/out)
    map.addControl(new mapboxgl.NavigationControl());

    // Thêm các marker chi nhánh lên bản đồ
    stores.forEach(store => {
      if (store.lat && store.lng) {
        // Tạo Popup hiển thị tên cửa hàng
        const popup = new mapboxgl.Popup({ offset: 25 })
          .setHTML(`<strong>${store.name}</strong><br><small>${store.address}</small>`);

        // Tạo Marker và đính kèm vào bản đồ
        new mapboxgl.Marker({ color: '#3d2314' })
          .setLngLat([store.lng, store.lat])
          .setPopup(popup)
          .addTo(map);
      }
    });

  } catch (err) {
    console.error('[stores.page.js] Lỗi khởi tạo Mapbox Map:', err);
    $('#map-container').html('<div class="error-fallback"><p>Bản đồ Mapbox đang ở chế độ xem trước (Preview Mode).</p></div>');
  }
}

/**
 * Dữ liệu cửa hàng fallback
 */
function getFallbackStores() {
  return [
    {
      id: 1,
      name: "Chi nhánh 1 — Quận 1 (Trụ sở chính)",
      address: "123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh",
      phone: "0901 234 567",
      hours: "06:00 - 22:30",
      lat: 10.7769,
      lng: 106.7009
    },
    {
      id: 2,
      name: "Chi nhánh 2 — Bình Thạnh",
      address: "456 Đường Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh",
      phone: "0901 234 568",
      hours: "06:30 - 22:00",
      lat: 10.8016,
      lng: 106.7118
    },
    {
      id: 3,
      name: "Chi nhánh 3 — Thủ Đức",
      address: "789 Đường Võ Văn Ngân, Phường Bình Thọ, TP. Thủ Đức, TP. Hồ Chí Minh",
      phone: "0901 234 569",
      hours: "06:30 - 22:30",
      lat: 10.8505,
      lng: 106.7719
    }
  ];
}
