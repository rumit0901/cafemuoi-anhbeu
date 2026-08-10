/**
 * Logic trang Đánh giá khách hàng (reviews.html).
 * Phụ thuộc: jQuery, jsRender, API (api.js).
 */
$(document).ready(function () {
  // Đăng ký helper jsRender tạo mảng số sao rating
  if ($.views && $.views.helpers) {
    $.views.helpers({
      range: function (start, end) {
        const result = [];
        for (let i = start; i <= end; i++) {
          result.push(i);
        }
        return result;
      }
    });
  }

  // Tải dữ liệu đánh giá
  loadReviewsData();
});

/**
 * Tải danh sách đánh giá từ API backend và render ra DOM.
 */
async function loadReviewsData() {
  const $container = $('#reviews-container');

  try {
    // Gọi API lấy danh sách đánh giá
    const reviews = await API.getReviews();

    if (reviews && reviews.length > 0) {
      // Render bằng template jsRender
      const html = $.templates('#reviewTemplate').render(reviews);
      $container.html(html);
    } else {
      showReviewsFallback();
    }
  } catch (error) {
    console.error('[reviews.page.js] Lỗi kết nối API. Hiển thị fallback.', error);
    showReviewsFallback();
  }
}

/**
 * Hiển thị đánh giá mẫu fallback tĩnh khi chưa có backend
 */
function showReviewsFallback() {
  const fallbackReviews = [
    {
      id: 1,
      customer: "Anh Minh Hoàng (Q.1, TP.HCM)",
      avatar: "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&auto=format&fit=crop&q=80",
      rating: 5,
      comment: "Cà phê muối ở đây ngon dã dũng! Lớp kem mặn thanh thanh không bị ngấy, hòa quyện xuất sắc với cà phê đậm đà.",
      date: "2 ngày trước"
    },
    {
      id: 2,
      customer: "Chị Thanh Thảo (Bình Thạnh)",
      avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80",
      rating: 5,
      comment: "Quán ruột của mình mỗi buổi sáng. Anh Bếu chủ quán siêu vui tính, phục vụ nhanh lẹ!",
      date: "1 tuần trước"
    },
    {
      id: 3,
      customer: "Nguyễn Quốc Bảo (TP. Thủ Đức)",
      avatar: "https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=150&auto=format&fit=crop&q=80",
      rating: 5,
      comment: "Món dừa nướng mặn đỉnh thực sự. Rất đáng thử cho tín đồ nghiện cà phê muối sài thành.",
      date: "2 tuần trước"
    }
  ];

  const html = $.templates('#reviewTemplate').render(fallbackReviews);
  $('#reviews-container').html(html);
}
