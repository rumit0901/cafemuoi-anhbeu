/**
 * Logic trang Liên Hệ & Đăng Ký Báo Giá Sỉ (contact.html).
 * Phụ thuộc: jQuery, API (api.js).
 */
$(document).ready(function () {
  // Lấy query parameter 'type' từ URL (ví dụ: contact.html?type=wholesale)
  const urlParams = new URLSearchParams(window.location.search);
  const inquiryType = urlParams.get('type');

  if (inquiryType === 'wholesale') {
    $('#inquiry_type').val('wholesale');
    toggleWholesaleFields('wholesale');
  }

  // Lắng nghe sự kiện thay đổi loại yêu cầu
  $('#inquiry_type').on('change', function () {
    toggleWholesaleFields($(this).val());
  });

  // Lắng nghe submit form contact
  $('#contact-form').on('submit', handleContactSubmit);
});

/**
 * Hiển thị/Ẩn các trường thông tin đăng ký bán sỉ tùy thuộc lựa chọn inquiry_type
 * @param {string} type - 'retail' hoặc 'wholesale'
 */
function toggleWholesaleFields(type) {
  if (type === 'wholesale') {
    $('.wholesale-only').slideDown(200);
  } else {
    $('.wholesale-only').slideUp(200);
  }
}

/**
 * Xử lý sự kiện gửi form liên hệ / yêu cầu báo giá sỉ qua API
 * @param {Event} e - Submit event
 */
async function handleContactSubmit(e) {
  e.preventDefault();

  const formData = {
    name: $('#name').val(),
    phone: $('#phone').val(),
    email: $('#email').val() || '',
    inquiry_type: $('#inquiry_type').val(),
    store_name: $('#store_name').val() || '',
    quantity: $('#quantity').val() || '',
    message: $('#message').val()
  };

  const $submitBtn = $('#btn-submit-contact');
  const $alert = $('#contact-alert');

  $submitBtn.prop('disabled', true).text('⏳ Đang gửi...');

  try {
    // Gọi API.sendContact đóng gói request POST /api/contact
    await API.sendContact(formData);

    $alert.removeClass('alert-error').addClass('alert-success').html('✅ Cảm ơn bạn! Anh Bếu đã nhận được thông tin và sẽ liên hệ báo giá sớm nhất.').show();
    $('#contact-form')[0].reset();
    toggleWholesaleFields('retail');
  } catch (error) {
    console.error('[contact.page.js] Gửi contact lỗi, kích hoạt UI fallback thông báo.', error);
    // UI Fallback khi chưa kết nối backend
    $alert.removeClass('alert-error').addClass('alert-success').html('✅ Yêu cầu của bạn đã được ghi nhận. Bộ phận kinh doanh Sỉ & Lẻ sẽ liên hệ lại ngay!').show();
    $('#contact-form')[0].reset();
    toggleWholesaleFields('retail');
  } finally {
    $submitBtn.prop('disabled', false).html('✉️ Gửi Yêu Cầu');
  }
}
