// Global state
let currentScreen = 'home';
let skills = [];
let profiles = [];
let filteredSkills = [];

// Sample data
const sampleSkills = [
    { id: 1, name: 'Photography', category: 'Creative', rating: 4.8, reviews: 124, description: 'Professional photography techniques' },
    { id: 2, name: 'Web Development', category: 'Tech', rating: 4.9, reviews: 89, description: 'Full-stack web development' },
    { id: 3, name: 'Cooking', category: 'Lifestyle', rating: 4.7, reviews: 156, description: 'Culinary arts and cooking techniques' },
    { id: 4, name: 'Language Learning', category: 'Education', rating: 4.6, reviews: 203, description: 'Effective language learning methods' },
    { id: 5, name: 'Music Production', category: 'Creative', rating: 4.8, reviews: 67, description: 'Digital music production and mixing' },
    { id: 6, name: 'Fitness Training', category: 'Health', rating: 4.9, reviews: 134, description: 'Personal fitness and training programs' },
    { id: 7, name: 'Digital Marketing', category: 'Business', rating: 4.7, reviews: 98, description: 'Online marketing strategies' },
    { id: 8, name: 'Graphic Design', category: 'Creative', rating: 4.8, reviews: 156, description: 'Visual design and branding' }
];

const sampleProfiles = [
    { 
        id: 1, 
        name: 'John', 
        skills: ['Photography', 'Video Editing'], 
        rating: 4.8, 
        reviews: 89,
        profession: 'Professional Photographer',
        skillsOffered: ['Photography', 'Video Editing', 'Adobe Creative Suite'],
        skillsWanted: ['Web Development', 'Marketing', 'Business Strategy']
    },
    { 
        id: 2, 
        name: 'Smith', 
        skills: ['Web Development', 'UI/UX'], 
        rating: 4.9, 
        reviews: 123,
        profession: 'Full-stack Developer',
        skillsOffered: ['Web Development', 'UI/UX Design', 'React'],
        skillsWanted: ['Photography', 'Content Writing', 'SEO']
    },
    { 
        id: 3, 
        name: 'Miller', 
        skills: ['Cooking', 'Nutrition'], 
        rating: 4.7, 
        reviews: 67,
        profession: 'Professional Chef',
        skillsOffered: ['Cooking', 'Nutrition', 'Food Photography'],
        skillsWanted: ['Web Development', 'Digital Marketing', 'Business']
    }
];

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    skills = [...sampleSkills];
    profiles = [...sampleProfiles];
    filteredSkills = [...skills];
    
    initializeNavigation();
    initializeSearch();
    initializeForms();
    renderHomeSkills();
    renderBrowseSkills();
    renderProfiles();
});

// Navigation functionality
function initializeNavigation() {
    const navButtons = document.querySelectorAll('.nav-btn');
    
    navButtons.forEach(button => {
        button.addEventListener('click', function() {
            const screenName = this.dataset.screen;
            showScreen(screenName);
            updateActiveNavButton(this);
        });
    });
}

function showScreen(screenName) {
    // Hide all screens
    document.querySelectorAll('.screen').forEach(screen => {
        screen.classList.remove('active');
    });
    
    // Show selected screen
    const targetScreen = document.getElementById(screenName + '-screen');
    if (targetScreen) {
        targetScreen.classList.add('active');
        currentScreen = screenName;
    }
}

function updateActiveNavButton(activeButton) {
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    activeButton.classList.add('active');
}

// Search functionality
function initializeSearch() {
    const homeSearch = document.getElementById('home-search');
    const browseSearch = document.getElementById('browse-search');
    const categoryFilter = document.getElementById('category-filter');
    
    if (homeSearch) {
        homeSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = skills.filter(skill => 
                skill.name.toLowerCase().includes(searchTerm) ||
                skill.category.toLowerCase().includes(searchTerm)
            );
            renderHomeSkills(filtered.slice(0, 3));
        });
    }
    
    if (browseSearch) {
        browseSearch.addEventListener('input', function() {
            filterSkills();
        });
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterSkills();
        });
    }
}

function filterSkills() {
    const searchTerm = document.getElementById('browse-search').value.toLowerCase();
    const category = document.getElementById('category-filter').value;
    
    filteredSkills = skills.filter(skill => {
        const matchesSearch = skill.name.toLowerCase().includes(searchTerm) ||
                             skill.category.toLowerCase().includes(searchTerm);
        const matchesCategory = !category || skill.category === category;
        return matchesSearch && matchesCategory;
    });
    
    renderBrowseSkills();
}

// Form handling
function initializeForms() {
    const loginForm = document.getElementById('login-form');
    const contactForm = document.getElementById('contact-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    if (contactForm) {
        contactForm.addEventListener('submit', handleContact);
    }
}

function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        hideLoading();
        if (email && password) {
            showMessage('Login successful!', 'success');
            showScreen('home');
        } else {
            showMessage('Please fill in all fields.', 'error');
        }
    }, 1000);
}

function handleContact(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    showLoading();
    
    // Send to PHP backend
    fetch('contact.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        hideLoading();
        if (result.success) {
            showMessage('Request sent successfully!', 'success');
            e.target.reset();
        } else {
            showMessage('Error sending request. Please try again.', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showMessage('Error sending request. Please try again.', 'error');
    });
}

// Rendering functions
function renderHomeSkills(skillsToRender = skills.slice(0, 3)) {
    const container = document.getElementById('home-skills');
    if (!container) return;
    
    container.innerHTML = skillsToRender.map(skill => createSkillCard(skill)).join('');
}

function renderBrowseSkills() {
    const container = document.getElementById('browse-skills');
    if (!container) return;
    
    container.innerHTML = filteredSkills.map(skill => createSkillCard(skill)).join('');
}

function renderProfiles() {
    const container = document.getElementById('featured-profiles');
    if (!container) return;
    
    container.innerHTML = profiles.map(profile => createProfileCard(profile)).join('');
}

function createSkillCard(skill) {
    return `
        <div class="skill-card">
            <div class="skill-header">
                <div class="skill-info">
                    <div class="skill-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="skill-details">
                        <h3>${skill.name}</h3>
                        <p>${skill.category}</p>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span>${skill.rating} (${skill.reviews} reviews)</span>
                        </div>
                    </div>
                </div>
                <button class="message-btn" onclick="showScreen('contact')">
                Request
                </button>
            </div>
        </div>
    `;
}

function createProfileCard(profile) {
    return `
        <div class="skill-card">
            <div class="skill-header">
                <div class="skill-info">
                    <div class="skill-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="skill-details">
                        <h3>${profile.name}</h3>
                        <p>${profile.skills.join(', ')}</p>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span>${profile.rating} (${profile.reviews} reviews)</span>
                        </div>
                    </div>
                </div>
                <button class="message-btn" onclick="showScreen('contact')">
                    Request
                </button>
            </div>
        </div>
    `;
}

// Utility functions
function showLoading() {
    document.getElementById('loading').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loading').classList.add('hidden');
}

function showMessage(text, type) {
    const messageElement = document.getElementById(type + '-message');
    const span = messageElement.querySelector('span');
    
    span.textContent = text;
    messageElement.classList.remove('hidden');
    
    setTimeout(() => {
        messageElement.classList.add('hidden');
    }, 3000);
}

// API functions for PHP backend
async function fetchSkills() {
    try {
        const response = await fetch('api.php?action=get_skills');
        const data = await response.json();
        if (data.success) {
            skills = data.skills;
            filteredSkills = [...skills];
            renderHomeSkills();
            renderBrowseSkills();
        }
    } catch (error) {
        console.error('Error fetching skills:', error);
    }
}

async function fetchProfiles() {
    try {
        const response = await fetch('api.php?action=get_profiles');
        const data = await response.json();
        if (data.success) {
            profiles = data.profiles;
            renderProfiles();
        }
    } catch (error) {
        console.error('Error fetching profiles:', error);
    }
}

async function searchSkills(query) {
    try {
        const response = await fetch(`api.php?action=search_skills&q=${encodeURIComponent(query)}`);
        const data = await response.json();
        if (data.success) {
            filteredSkills = data.skills;
            renderBrowseSkills();
        }
    } catch (error) {
        console.error('Error searching skills:', error);
    }
}