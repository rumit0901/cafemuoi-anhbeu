/**
 * API Wrapper module đóng gói các thao tác gửi HTTP fetch tới Backend PHP Slim.
 * Tự động xử lý bắt lỗi và trả về dữ liệu chuẩn JSON.
 */
const API = {
  /**
   * Helper gửi HTTP Request tới API backend với try-catch và timeout
   * @param {string} endpoint - Đường dẫn API (ví dụ: '/api/menu')
   * @param {object} options - Các tùy chọn fetch (method, body, headers, ...)
   * @returns {Promise<any>} JSON response dữ liệu từ server
   */
  async request(endpoint, options = {}) {
    // Ghép URL hoàn chỉnh từ CONFIG
    const url = `${CONFIG.API_BASE_URL}${endpoint}`;
    
    // Cấu hình headers mặc định
    const defaultHeaders = {
      'Accept': 'application/json'
    };

    if (options.body) {
      defaultHeaders['Content-Type'] = 'application/json';
    }

    try {
      // Thực hiện fetch dữ liệu từ server
      const response = await fetch(url, {
        ...options,
        headers: { ...defaultHeaders, ...options.headers }
      });

      // Kiểm tra HTTP Status
      if (!response.ok) {
        throw new Error(`HTTP Error! Status: ${response.status}`);
      }

      // Trả về dữ liệu JSON
      return await response.json();
    } catch (error) {
      // Log lỗi chuẩn định dạng console.error khi kết nối thất bại
      console.error(`[API.request] Lỗi khi gọi endpoint: ${endpoint}`, error);
      throw error;
    }
  },

  /**
   * Lấy danh sách sản phẩm thực đơn từ `/api/menu`
   * @returns {Promise<Array>} Danh sách sản phẩm menu
   */
  async getMenu() {
    // Gọi endpoint /api/menu
    return await this.request('/api/menu');
  },

  /**
   * Lấy danh sách kênh bán hàng bán lẻ từ `/api/channels` (ShopeeFood, GrabFood...)
   * @returns {Promise<Array>} Danh sách các kênh bán hàng đối tác
   */
  async getChannels() {
    // Gọi endpoint /api/channels
    return await this.request('/api/channels');
  },

  /**
   * Lấy danh sách sản phẩm/gói sỉ cà phê muối đóng chai từ `/api/wholesale`
   * @returns {Promise<Array>} Danh sách sản phẩm bán sỉ
   */
  async getWholesale() {
    // Gọi endpoint /api/wholesale
    return await this.request('/api/wholesale');
  },

  /**
   * Lấy nội dung câu chuyện thương hiệu từ CMS `/api/story`
   * @returns {Promise<object>} Object chứa nội dung title, subtitle, content, image...
   */
  async getStory() {
    // Gọi endpoint /api/story
    return await this.request('/api/story');
  },

  /**
   * Lấy danh sách chi nhánh cửa hàng từ `/api/stores`
   * @returns {Promise<Array>} Danh sách chi nhánh
   */
  async getStores() {
    // Gọi endpoint /api/stores
    return await this.request('/api/stores');
  },

  /**
   * Lấy danh sách đánh giá từ `/api/reviews`
   * @returns {Promise<Array>} Danh sách phản hồi khách hàng & đại lý
   */
  async getReviews() {
    // Gọi endpoint /api/reviews
    return await this.request('/api/reviews');
  },

  /**
   * Lấy danh sách bài viết/tin tức từ `/api/blog`
   * @returns {Promise<Array>} Danh sách bài viết blog & chính sách sỉ
   */
  async getBlog() {
    // Gọi endpoint /api/blog
    return await this.request('/api/blog');
  },

  /**
   * Gửi thông tin form liên hệ / yêu cầu báo giá sỉ tới `/api/contact`
   * @param {object} contactData - Object chứa { name, phone, email, inquiry_type, store_name, quantity, message }
   * @returns {Promise<object>} Kết quả phản hồi từ server
   */
  async sendContact(contactData) {
    // Gửi POST request kèm body JSON
    return await this.request('/api/contact', {
      method: 'POST',
      body: JSON.stringify(contactData)
    });
  }
};
