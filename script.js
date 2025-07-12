 // Sample data
        const profiles = [
            {
                name: "Marc Demo",
                photo: "Profile Photo",
                skillsOffered: ["Web Design", "HTML"],
                skillsWanted: ["JavaScript", "Node.js"],
                rating: 3.5,
                maxRating: 5
            },
            {
                name: "Michell",
                photo: "Profile Photo",
                skillsOffered: ["Web Design", "HTML"],
                skillsWanted: ["JavaScript", "Node.js"],
                rating: 2.5,
                maxRating: 5
            },
            {
                name: "Joe Mills",
                photo: "Profile Photo",
                skillsOffered: ["Web Design", "HTML"],
                skillsWanted: ["JavaScript", "Node.js"],
                rating: 4.0,
                maxRating: 5
            },
            {
                name: "Joe Mills",
                photo: "Profile Photo",
                skillsOffered: ["Web Design", "HTML"],
                skillsWanted: ["JavaScript", "Node.js"],
                rating: 4.0,
                maxRating: 5
            }
            
            
        ];

        let currentPage = 3;
        let filteredProfiles = [...profiles];

        function renderProfiles() {
            const container = document.getElementById('profilesContainer');
            container.innerHTML = '';

            filteredProfiles.forEach(profile => {
                const profileCard = document.createElement('div');
                profileCard.className = 'profile-card';
                
                const stars = '★'.repeat(Math.floor(profile.rating)) + '☆'.repeat(profile.maxRating - Math.floor(profile.rating));
                
                profileCard.innerHTML = `
                    <div class="profile-photo">${profile.photo}</div>
                    <div class="profile-info">
                        <div class="profile-name">${profile.name}</div>
                        <div class="skill-label">Skills Offered:</div>
                        <div class="skills">
                            ${profile.skillsOffered.map(skill => `<span class="skill-tag skill-offered">${skill}</span>`).join('')}
                        </div>
                        <div class="skill-label">Skills Wanted:</div>
                        <div class="skills">
                            ${profile.skillsWanted.map(skill => `<span class="skill-tag skill-wanted">${skill}</span>`).join('')}
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="request-btn" onclick="sendRequest('${profile.name}')">Request</button>
                        <div class="rating">
                            <span class="stars">${stars}</span>
                            Rating: ${profile.rating}/${profile.maxRating}
                        </div>
                    </div>
                `;
                
                container.appendChild(profileCard);
            });
        }

        function searchProfiles() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const availabilityFilter = document.getElementById('availabilityFilter').value;
            
            filteredProfiles = profiles.filter(profile => {
                const matchesSearch = searchTerm === '' || 
                    profile.name.toLowerCase().includes(searchTerm) ||
                    profile.skillsOffered.some(skill => skill.toLowerCase().includes(searchTerm)) ||
                    profile.skillsWanted.some(skill => skill.toLowerCase().includes(searchTerm));
                
                // For demo purposes, we'll assume all profiles match availability filter
                const matchesAvailability = availabilityFilter === 'all' || true;
                
                return matchesSearch && matchesAvailability;
            });
            
            renderProfiles();
        }

        function sendRequest(profileName) {
            alert(`Request sent to ${profileName}!`);
        }

        function showLogin() {
            alert('Login functionality would be implemented here');
        }

        function changePage(page) {
            const buttons = document.querySelectorAll('.pagination button');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if (typeof page === 'number') {
                currentPage = page;
                buttons[page].classList.add('active');
            } else if (page === 'prev' && currentPage > 1) {
                currentPage--;
                buttons[currentPage].classList.add('active');
            } else if (page === 'next' && currentPage < 4) {
                currentPage++;
                buttons[currentPage].classList.add('active');
            }
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            renderProfiles();
            
            // Add event listener for search input
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchProfiles();
                }
            });
            
            // Add event listener for availability filter
            document.getElementById('availabilityFilter').addEventListener('change', searchProfiles);
        });