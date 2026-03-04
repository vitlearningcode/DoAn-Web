// ==================== MOCK DATA ====================

// Featured books data
const featuredBooks = [
  { id: '1', name: 'Đắc Nhân Tâm', author: 'Dale Carnegie', category: 'Kỹ năng sống', price: 86000, originalPrice: 120000, rating: 4.8, reviews: 1240, image: 'https://picsum.photos/seed/book1/300/400', badge: 'Bán chạy' },
  { id: '2', name: 'Nhà Giả Kim', author: 'Paulo Coelho', category: 'Văn học', price: 79000, originalPrice: 95000, rating: 4.9, reviews: 850, image: 'https://picsum.photos/seed/book2/300/400' },
  { id: '3', name: 'Nghĩ Giàu Làm Giàu', author: 'Napoleon Hill', category: 'Kinh tế', price: 110000, rating: 4.7, reviews: 620, image: 'https://picsum.photos/seed/book3/300/400', badge: 'Mới' },
  { id: '4', name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', author: 'Rosie Nguyễn', category: 'Kỹ năng sống', price: 80000, rating: 4.6, reviews: 2100, image: 'https://picsum.photos/seed/book4/300/400' },
  { id: '5', name: 'Sapiens: Lược Sử Loài Người', author: 'Yuval Noah Harari', category: 'Khoa học', price: 250000, originalPrice: 300000, rating: 4.9, reviews: 3200, image: 'https://picsum.photos/seed/book5/300/400', badge: '-15%' },
  { id: '6', name: 'Cây Cam Ngọt Của Tôi', author: 'José Mauro de Vasconcelos', category: 'Văn học', price: 95000, rating: 4.8, reviews: 1500, image: 'https://picsum.photos/seed/book6/300/400' },
  { id: '7', name: 'Tâm Lý Học Tội Phạm', author: 'Tôn Thất', category: 'Tâm lý học', price: 135000, originalPrice: 150000, rating: 4.5, reviews: 430, image: 'https://picsum.photos/seed/book7/300/400' },
  { id: '8', name: 'Muôn Kiếp Nhân Sinh', author: 'Nguyên Phong', category: 'Tôn giáo - Tâm linh', price: 168000, rating: 4.7, reviews: 980, image: 'https://picsum.photos/seed/book8/300/400' },
];

// New releases data
const newReleases = [
  { id: '9', name: 'Dune - Xứ Cát', author: 'Frank Herbert', category: 'Khoa học viễn tưởng', price: 210000, originalPrice: 250000, rating: 4.9, reviews: 150, image: 'https://picsum.photos/seed/book9/300/400', badge: 'Mới' },
  { id: '10', name: 'Atomic Habits', author: 'James Clear', category: 'Kỹ năng sống', price: 145000, rating: 4.8, reviews: 5200, image: 'https://picsum.photos/seed/book10/300/400' },
  { id: '11', name: 'Kẻ Trộm Sách', author: 'Markus Zusak', category: 'Văn học', price: 125000, originalPrice: 140000, rating: 4.7, reviews: 890, image: 'https://picsum.photos/seed/book11/300/400' },
  { id: '12', name: 'Sức Mạnh Của Thói Quen', author: 'Charles Duhigg', category: 'Tâm lý học', price: 115000, rating: 4.6, reviews: 1200, image: 'https://picsum.photos/seed/book12/300/400' },
  { id: '13', name: 'Tư Duy Nhanh Và Chậm', author: 'Daniel Kahneman', category: 'Kinh tế', price: 185000, originalPrice: 210000, rating: 4.8, reviews: 3400, image: 'https://picsum.photos/seed/book13/300/400', badge: 'Bán chạy' },
];

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { featuredBooks, newReleases };
}

