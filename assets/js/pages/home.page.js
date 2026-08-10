/**
 * Logic Trang Chủ (index.html).
 * Phụ thuộc: jQuery, jsRender, API (api.js), CONFIG (config.js).
 */
$(document).ready(function () {
  // Tải danh sách kênh bán hàng bán lẻ (ShopeeFood, GrabFood...)
  loadChannelsData();

  // Tải danh sách gói cà phê muối đóng chai sỉ nổi bật
  loadWholesaleHighlight();
});

/**
 * Hàm lấy danh sách kênh bán hàng từ API backend `/api/channels` và render ra DOM.
 */
async function loadChannelsData() {
  const $container = $('#channels-container');
  if (!$container.length) return;

  try {
    // 1. Gọi API lấy danh sách kênh bán hàng
    const channels = await API.getChannels();

    if (channels && channels.length > 0) {
      // Render danh sách bằng jsRender Template (#channelTemplate)
      const renderedHtml = $.templates('#channelTemplate').render(channels);
      $container.html(renderedHtml);
    } else {
      showChannelsFallback();
    }
  } catch (error) {
    console.error('[home.page.js] Không thể tải danh sách kênh bán hàng. Hiển thị fallback tĩnh.', error);
    showChannelsFallback();
  }
}

/**
 * Render dữ liệu fallback tĩnh cho các kênh bán hàng bán lẻ đối tác
 */
function showChannelsFallback() {
  const fallbackChannels = [
    {
      id: 1,
      name: "ShopeeFood",
      logo: "https://images.unsplash.com/photo-1526367790999-0150786686a2?w=100&auto=format&fit=crop&q=80",
      url: "https://shopeefood.vn/",
      badge: "Giao Siêu Tốc 15p",
      active: 1
    },
    {
      id: 2,
      name: "GrabFood",
      logo: "https://images.unsplash.com/photo-1526367790999-0150786686a2?w=100&auto=format&fit=crop&q=80",
      url: "https://food.grab.com/vn/",
      badge: "Freeship Đơn Đầu",
      active: 1
    },
    {
      id: 3,
      name: "BeFood",
      logo: "https://images.unsplash.com/photo-1526367790999-0150786686a2?w=100&auto=format&fit=crop&q=80",
      url: "https://be.com.vn/",
      badge: "Ưu Đãi Hàng Ngày",
      active: 1
    },
    {
      id: 4,
      name: "GoFood (Gojek)",
      logo: "https://images.unsplash.com/photo-1526367790999-0150786686a2?w=100&auto=format&fit=crop&q=80",
      url: "https://www.gojek.com/vn/",
      badge: "Giảm Đến 50K",
      active: 1
    }
  ];

  const $container = $('#channels-container');
  if ($container.length && $('#channelTemplate').length) {
    const html = $.templates('#channelTemplate').render(fallbackChannels);
    $container.html(html);
  }
}

/**
 * Hàm lấy các sản phẩm/gói sỉ đóng chai tiêu biểu từ API `/api/wholesale`
 */
async function loadWholesaleHighlight() {
  const $container = $('#wholesale-container');
  if (!$container.length) return;

  try {
    const items = await API.getWholesale();

    if (items && items.length > 0) {
      const html = $.templates('#wholesaleItemTemplate').render(items);
      $container.html(html);
    } else {
      showWholesaleFallback();
    }
  } catch (error) {
    console.error('[home.page.js] Không thể tải dữ liệu bán sỉ. Hiển thị fallback.', error);
    showWholesaleFallback();
  }
}

/**
 * Hiển thị dữ liệu fallback mẫu cho phần Cung cấp sỉ cà phê muối đóng chai
 */
function showWholesaleFallback() {
  const fallbackWholesale = [
    {
      id: 1,
      name: "Cà Phê Muối Đóng Chai 330ml (Nguyên Chai)",
      bottle_size: "Chai 330ml",
      min_quantity: "Tối thiểu 5 chai / đơn",
      price_tier: "Báo giá sỉ ưu đãi",
      description: "Pha chế sẵn theo công thức chuẩn Anh Bếu, bảo quản lạnh 3-5 ngày. Thích hợp bán kèm quán cafe, buffet, sự kiện.",
      image: "https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=500&auto=format&fit=crop&q=80"
    },
    {
      id: 2,
      name: "Cà Phê Muối Đóng Chai 500ml (Đậm Đặc)",
      bottle_size: "Chai 500ml",
      min_quantity: "Tối thiểu 5 chai / đơn",
      price_tier: "Báo giá sỉ ưu đãi",
      description: "Cốt cà phê Robusta đậm đặc kèm hũ kem muối riêng. Phù hợp cho đại lý phân phối lại hoặc dùng cho văn phòng đông người.",
      image: "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80"
    }
  ];

  const $container = $('#wholesale-container');
  if ($container.length && $('#wholesaleItemTemplate').length) {
    const html = $.templates('#wholesaleItemTemplate').render(fallbackWholesale);
    $container.html(html);
  }
}
