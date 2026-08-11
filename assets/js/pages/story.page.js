/**
 * Logic trang Câu Chuyện Thương Hiệu (story.html).
 * Lấy dữ liệu động từ CMS Backend API `/api/story`.
 * Phụ thuộc: jQuery, jsRender, API (api.js).
 */
$(document).ready(function () {
  loadStoryData();
});

/**
 * Hàm lấy dữ liệu câu chuyện thương hiệu từ API `/api/story` và render ra DOM.
 */
async function loadStoryData() {
  const $container = $('#story-container');
  if (!$container.length) return;

  try {
    // 1. Gọi API lấy dữ liệu câu chuyện từ CMS
    const storyData = await API.getStory();

    if (storyData && storyData.title) {
      // Render bằng jsRender Template (#storyTemplate)
      const html = $.templates('#storyTemplate').render(storyData);
      $container.html(html);
    } else {
      showStoryFallback();
    }
  } catch (error) {
    console.error('[story.page.js] Không thể kết nối API CMS /api/story. Hiển thị dữ liệu fallback.', error);
    showStoryFallback();
  }
}

/**
 * Render dữ liệu fallback tĩnh cho trang Câu Chuyện khi chưa có CMS Backend kết nối
 */
function showStoryFallback() {
  const fallbackStory = {
    subtitle: "Hành Trình Thương Hiệu",
    title: "Câu Chuyện Về \"Anh Bếu\" & Ly Cà Phê Muối Sài Gòn",
    image: "https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=900&auto=format&fit=crop&q=80",
    paragraphs: [
      "Bắt đầu từ một xe cà phê nhỏ ven đường Sài Gòn, <strong>Cà Phê Muối Anh Bếu</strong> ra đời với niềm đam mê cháy bỏng: biến ly cà phê truyền thống trở nên thú vị, mộc mạc mà tràn đầy cảm xúc.",
      "Không sử dụng chất tạo béo công nghiệp, lớp bọt kem muối của Anh Bếu được đánh thủ công mỗi ngày từ sữa tươi nguyên chất kết hợp với muối biển tinh khiết. Vị mặn nhẹ thanh tao tôn lên độ đậm đà của từng giọt cà phê Robusta Tây Nguyên.",
      "Đến nay, hệ thống <strong>Cà Phê Muối Anh Bếu</strong> tự hào đón hàng nghìn lượt khách mỗi ngày và mở rộng dịch vụ cung cấp sỉ đóng chai cho các đại lý & sự kiện trên toàn quốc."
    ]
  };

  const $container = $('#story-container');
  if ($container.length && $('#storyTemplate').length) {
    const html = $.templates('#storyTemplate').render(fallbackStory);
    $container.html(html);
  }
}
