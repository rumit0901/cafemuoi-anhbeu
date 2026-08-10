/**
 * Logic trang Thực đơn (menu.html).
 * Phụ thuộc: jQuery, jsRender, API (api.js), CONFIG (config.js).
 */
$(document).ready(function () {
  // Tải sản phẩm thực đơn bán lẻ
  loadMenuData();

  // Tải danh sách các gói cà phê muối đóng chai bán sỉ
  loadWholesaleData();
});

/**
 * Hàm lấy dữ liệu menu từ API backend và render ra DOM.
 * Sử dụng Skeleton state khi tải và Fallback tĩnh khi gặp lỗi.
 */
async function loadMenuData() {
  const $container = $('#menu-container');

  try {
    // 1. Gọi API lấy danh sách sản phẩm
    const items = await API.getMenu();

    // 2. Kiểm tra nếu có dữ liệu hợp lệ
    if (items && items.length > 0) {
      // Render danh sách bằng jsRender Template (#menuItemTemplate)
      const renderedHtml = $.templates('#menuItemTemplate').render(items);
      $container.html(renderedHtml);
    } else {
      // Trường hợp mảng rỗng từ API
      showMenuFallback();
    }
  } catch (error) {
    // 3. Xử lý lỗi fetch: Hiển thị console log & giữ lại fallback tĩnh
    console.error('[menu.page.js] Không thể kết nối tới Backend API. Hiển thị dữ liệu fallback.', error);
    showMenuFallback();
  }
}

/**
 * Tải dữ liệu danh sách gói cà phê muối đóng chai sỉ từ API `/api/wholesale`
 */
async function loadWholesaleData() {
  const $container = $('#wholesale-menu-container');
  if (!$container.length) return;

  try {
    const wholesaleItems = await API.getWholesale();
    if (wholesaleItems && wholesaleItems.length > 0) {
      const html = $.templates('#wholesaleCardTemplate').render(wholesaleItems);
      $container.html(html);
    } else {
      showWholesaleMenuFallback();
    }
  } catch (error) {
    console.error('[menu.page.js] Không thể kết nối API Wholesale. Hiển thị dữ liệu fallback.', error);
    showWholesaleMenuFallback();
  }
}

/**
 * Hiển thị dữ liệu mẫu fallback tĩnh khi API backend chưa bật hoặc bị lỗi kết nối
 */
function showMenuFallback() {
  const fallbackItems = [
    {
      id: 1,
      name: "Cà phê muối anh Bếu Signature",
      price: 35000,
      image: "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80",
      tag: "best-seller",
      description: "Hương vị cà phê Robusta đậm đà kết hợp cùng lớp kem muối béo mặn sánh mịn công thức bí truyền."
    },
    {
      id: 2,
      name: "Cà phê muối dừa nướng",
      price: 39000,
      image: "https://images.unsplash.com/photo-1572442388796-116686028430?w=500&auto=format&fit=crop&q=80",
      tag: "best-seller",
      description: "Sự hòa quyện tuyệt vời giữa vị kem muối đặc trưng và vụn dừa nướng thơm giòn sần sật."
    },
    {
      id: 3,
      name: "Cà phê sữa đá Sài Gòn",
      price: 29000,
      image: "https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=500&auto=format&fit=crop&q=80",
      tag: "",
      description: "Cà phê Robusta phin chuẩn phong cách Sài Gòn đậm vị, thơm lừng béo ngậy."
    },
    {
      id: 4,
      name: "Bạc xỉu mặn anh Bếu",
      price: 35000,
      image: "https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=500&auto=format&fit=crop&q=80",
      tag: "new",
      description: "Nhiều sữa ít cà phê, kết hợp vệt kem muối nhẹ nhàng thích hợp cho người thích ngọt dịu."
    }
  ];

  // Render bằng jsRender với dữ liệu fallback
  const html = $.templates('#menuItemTemplate').render(fallbackItems);
  $('#menu-container').html(html);
}

/**
 * Render dữ liệu fallback tĩnh cho Menu Bán Sỉ Đóng Chai
 */
function showWholesaleMenuFallback() {
  const fallbackWholesale = [
    {
      id: 1,
      name: "Cà Phê Muối Đóng Chai 330ml (Pha Sẵn)",
      bottle_size: "Chai 330ml",
      min_quantity: "Tối thiểu 5 chai",
      price_tier: "Giá sỉ chiết khấu cao",
      description: "Cà phê muối pha sẵn ướp lạnh, giữ trọn bọt kem sánh mịn 3-5 ngày trong tủ mát.",
      image: "https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=500&auto=format&fit=crop&q=80"
    },
    {
      id: 2,
      name: "Cà Phê Muối Đóng Chai 500ml (Cốt Đậm Đặc)",
      bottle_size: "Chai 500ml",
      min_quantity: "Tối thiểu 5 chai",
      price_tier: "Giá sỉ chiết khấu cao",
      description: "Chai cốt cà phê 500ml kèm 1 hũ kem muối riêng biệt, phù hợp cho đại lý & tiệc văn phòng.",
      image: "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80"
    }
  ];

  const $container = $('#wholesale-menu-container');
  if ($container.length && $('#wholesaleCardTemplate').length) {
    const html = $.templates('#wholesaleCardTemplate').render(fallbackWholesale);
    $container.html(html);
  }
}
