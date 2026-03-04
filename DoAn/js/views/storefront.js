// ==================== STOREFRONT VIEW ====================

let currentHeroSlide = 0;

// Initialize role toggle
function initRoleToggle() {
  const btnCustomer = document.getElementById('btn-customer');
  const btnAdmin = document.getElementById('btn-admin');
  const storefrontView = document.getElementById('storefront-view');
  const adminView = document.getElementById('admin-view');

  btnCustomer.addEventListener('click', () => {
    btnCustomer.classList.add('active');
    btnAdmin.classList.remove('active');
    storefrontView.style.display = 'block';
    adminView.style.display = 'none';
  });

  btnAdmin.addEventListener('click', () => {
    btnAdmin.classList.add('active');
    btnCustomer.classList.remove('active');
    storefrontView.style.display = 'none';
    adminView.style.display = 'flex';
    loadAdminView('overview');
  });
}

// Initialize hero carousel
function initHeroCarousel() {
  const slides = document.querySelectorAll('.hero-slide');
  const dotsContainer = document.getElementById('hero-dots');
  
  // Create dots
  slides.forEach((_, index) => {
    const dot = document.createElement('div');
    dot.className = `hero-dot ${index === 0 ? 'active' : ''}`;
    dot.addEventListener('click', () => goToSlide(index));
    dotsContainer.appendChild(dot);
  });

  // Auto slide
  setInterval(() => {
    currentHeroSlide = (currentHeroSlide + 1) % slides.length;
    updateHeroSlide();
  }, 5000);
}

function goToSlide(index) {
  currentHeroSlide = index;
  updateHeroSlide();
}

function updateHeroSlide() {
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-dot');
  
  slides.forEach((slide, i) => {
    slide.classList.toggle('active', i === currentHeroSlide);
  });
  
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === currentHeroSlide);
  });
}

// Initialize flash sale timer
function initFlashSaleTimer() {
  let hours = 12;
  let minutes = 45;
  let seconds = 30;

  setInterval(() => {
    seconds--;
    if (seconds < 0) {
      seconds = 59;
      minutes--;
      if (minutes < 0) {
        minutes = 59;
        hours--;
        if (hours < 0) {
          hours = 24;
        }
      }
    }
    
    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
  }, 1000);
}

// Render all books
function renderBooks() {
  const flashSaleContainer = document.getElementById('flash-sale-books');
  const featuredContainer = document.getElementById('featured-books');
  const newReleasesContainer = document.getElementById('new-releases');

  // Flash Sale - first 5 books
  flashSaleContainer.innerHTML = featuredBooks.slice(0, 5).map(book => createBookCard(book, true)).join('');
  
  // Featured Books
  featuredContainer.innerHTML = featuredBooks.map(book => createBookCard(book)).join('');
  
  // New Releases
  newReleasesContainer.innerHTML = newReleases.map(book => createBookCard(book)).join('');

  // Add click handlers
  document.querySelectorAll('.book-card').forEach(card => {
    card.addEventListener('click', () => {
      const bookId = card.dataset.bookId;
      const book = [...featuredBooks, ...newReleases].find(b => b.id === bookId);
      if (book) {
        openBookModal(book);
      }
    });
  });
}

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { initRoleToggle, initHeroCarousel, initFlashSaleTimer, renderBooks };
}

