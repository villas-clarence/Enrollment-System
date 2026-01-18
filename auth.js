// Authentication and Role-Based Access Control
class AuthManager {
    constructor() {
        this.roles = {
            student: {
                name: 'Student',
                permissions: ['my-profile', 'my-enrollment', 'my-classes', 'course-catalog', 'academic-calendar'],
                modules: ['student', 'enrollment', 'section_enrollment', 'course', 'term', 'course_prerequisite'],
                dashboard: 'student-dashboard.html',
                dataAccess: 'personal' // Only own data
            },
            faculty: {
                name: 'Faculty',
                permissions: ['my-profile', 'my-classes', 'student-records', 'course-info', 'class-enrollment', 'room-schedule'],
                modules: ['instructor', 'section_enrollment', 'student', 'course', 'enrollment', 'term', 'room', 'course_prerequisite'],
                dashboard: 'faculty-dashboard.html',
                dataAccess: 'teaching' // Own classes and students
            },
            admin: {
                name: 'Administrator',
                permissions: ['all'],
                modules: ['enrollment', 'student', 'instructor', 'course', 'department', 'term', 'program', 'room', 'course_prerequisite', 'section_enrollment', 'admin-enrollment'],
                dashboard: 'admin-dashboard.html',
                dataAccess: 'full' // All data
            }
        };
    }

    // Check if user is logged in
    isLoggedIn() {
        return sessionStorage.getItem('isLoggedIn') === 'true';
    }

    // Get current user role
    getCurrentRole() {
        return sessionStorage.getItem('userRole');
    }

    // Get current username
    getCurrentUser() {
        return sessionStorage.getItem('username');
    }

    // Check if current user has permission to access a module
    hasPermission(module) {
        const role = this.getCurrentRole();
        if (!role || !this.roles[role]) return false;
        return this.roles[role].permissions.includes(module);
    }

    // Redirect to login if not authenticated
    requireAuth() {
        if (!this.isLoggedIn()) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    }

    // Require specific role
    requireRole(requiredRole) {
        if (!this.requireAuth()) return false;
        
        const currentRole = this.getCurrentRole();
        if (currentRole !== requiredRole) {
            this.redirectToDashboard();
            return false;
        }
        return true;
    }

    // Check permission for current page
    checkPagePermission() {
        if (!this.requireAuth()) return false;

        const currentPage = window.location.pathname.split('/').pop().replace('.html', '');
        const role = this.getCurrentRole();
        
        // Allow access to dashboard pages
        if (currentPage.includes('dashboard')) return true;
        
        // Check if user has permission for this module using the modules array
        if (!this.roles[role] || !this.roles[role].modules.includes(currentPage)) {
            this.showAccessDenied();
            return false;
        }
        
        return true;
    }

    // Redirect to appropriate dashboard
    redirectToDashboard() {
        const role = this.getCurrentRole();
        if (role && this.roles[role]) {
            window.location.href = this.roles[role].dashboard;
        } else {
            window.location.href = 'login.html';
        }
    }

    // Show access denied message
    showAccessDenied() {
        document.body.innerHTML = `
            <div style="
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                font-family: 'Inter', sans-serif;
                padding: 20px;
            ">
                <div style="
                    background: white;
                    padding: 50px;
                    border-radius: 20px;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
                    text-align: center;
                    max-width: 500px;
                ">
                    <div style="
                        width: 80px;
                        height: 80px;
                        background: linear-gradient(135deg, #dc3545, #c82333);
                        border-radius: 50%;
                        margin: 0 auto 30px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 2rem;
                    ">
                        <i class="fas fa-ban"></i>
                    </div>
                    <h1 style="color: #1e3c72; margin-bottom: 15px; font-size: 2rem;">Access Denied</h1>
                    <p style="color: #666; margin-bottom: 30px; font-size: 1.1rem;">
                        You don't have permission to access this page.
                    </p>
                    <button onclick="auth.redirectToDashboard()" style="
                        background: linear-gradient(135deg, #1e3c72, #2a5298);
                        color: white;
                        border: none;
                        padding: 15px 30px;
                        border-radius: 10px;
                        font-size: 1rem;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    " onmouseover="this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.transform='translateY(0)'">
                        <i class="fas fa-home"></i>
                        Go to Dashboard
                    </button>
                </div>
            </div>
        `;
    }

    // Get appropriate dashboard URL based on user role
    getDashboardUrl() {
        const role = this.getCurrentRole();
        if (role && this.roles[role]) {
            return this.roles[role].dashboard;
        }
        return 'login.html';
    }

    // Logout user
    logout() {
        sessionStorage.clear();
        window.location.href = 'login.html';
    }

    // Get role display name
    getRoleDisplayName(role) {
        return this.roles[role] ? this.roles[role].name : role;
    }

    // Add role-based styling to page
    addRoleStyling() {
        const role = this.getCurrentRole();
        if (!role) return;

        const colors = {
            student: { primary: '#28a745', secondary: '#20c997' },
            faculty: { primary: '#17a2b8', secondary: '#138496' },
            admin: { primary: '#dc3545', secondary: '#c82333' }
        };

        if (colors[role]) {
            const style = document.createElement('style');
            style.textContent = `
                :root {
                    --role-primary: ${colors[role].primary};
                    --role-secondary: ${colors[role].secondary};
                }
            `;
            document.head.appendChild(style);
        }
    }
}

// Create global auth instance
const auth = new AuthManager();

// Auto-check authentication on page load
document.addEventListener('DOMContentLoaded', function() {
    // Skip auth check for login page
    if (window.location.pathname.includes('login.html')) return;
    
    // Check page permission
    auth.checkPagePermission();
    
    // Add role-based styling
    auth.addRoleStyling();
});