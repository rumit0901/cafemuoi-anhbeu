/**
 * Logic trang Blog & Tin tức (blog.html).
 * Phụ thuộc: jQuery, jsRender, API (api.js).
 */
$(document).ready(function () {
  loadBlogData();
});

/**
 * Tải danh sách bài viết từ API backend `/api/blog` và render ra DOM.
 */
async function loadBlogData() {
  const $container = $('#blog-container');
  if (!$container.length) return;

  try {
    const posts = await API.getBlog();

    if (posts && posts.length > 0) {
      const html = $.templates('#blogCardTemplate').render(posts);
      $container.html(html);
    } else {
      showBlogFallback();
    }
  } catch (error) {
    console.error('[blog.page.js] Không thể kết nối API Blog. Hiển thị dữ liệu fallback.', error);
    showBlogFallback();
  }
}

/**
 * Hiển thị tin tức & bài viết fallback tĩnh cho SEO và người dùng
 */
function showBlogFallback() {
  const fallbackPosts = [
    {
      id: 1,
      title: "🔥 Chính Sách Đại Lý & Phân Phối Sỉ Cà Phê Muối Đóng Chai 2026",
      excerpt: "Tìm hiểu chính sách chiết khấu, hỗ trợ vận chuyển và quy trình trở thành đại lý phân phối cà phê muối Anh Bếu.",
      image: "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&auto=format&fit=crop&q=80",
      slug: "chinh-sach-dai-ly-ban-si"
    },
    {
      id: 2,
      title: "🍾 Hướng Dẫn Bảo Quản Cà Phê Muối Đóng Chai Chuẩn Vị 3-5 Ngày",
      excerpt: "Bí quyết giữ lớp kem muối sánh mịn béo mặn không bị tách lớp khi bảo quản tủ lạnh dành cho quán cafe & tiệc.",
      image: "https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=600&auto=format&fit=crop&q=80",
      slug: "huong-dan-bao-quan-ca-phe-muoi-dong-chai"
    },
    {
      id: 3,
      title: "☕ 3 Bước Thưởng Thức Cà Phê Muối Chuẩn Vị Anh Bếu",
      excerpt: "Nên nếm lớp bọt kem trước hay khuấy đều lên? Cùng khám phá bí kíp nếm trọn hương vị tuyệt hảo nhé!",
      image: "https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&auto=format&fit=crop&q=80",
      slug: "3-buoc-thuong-thuc-ca-phe-muoi"
    }
  ];

  const $container = $('#blog-container');
  if ($container.length && $('#blogCardTemplate').length) {
    const html = $.templates('#blogCardTemplate').render(fallbackPosts);
    $container.html(html);
  }
}
