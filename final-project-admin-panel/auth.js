let authUsers = [];
try {
    const storedUsers = localStorage.getItem("users");

    authUsers = storedUsers ? JSON.parse(storedUsers) : [];

    if(!Array.isArray(authUsers)) {
        authUsers = [];
    }
} catch (error) {
    console.error("Error reading users: ",error);
    authUsers = [];
}

// creating initial admin account
const existingAdmin = authUsers.find(user => user.role === "Admin");

if (!existingAdmin) {
    const adminUser = {
        id: Date.now(),
        name: "Administrator",
        email: "admin@gmail.com",
        password: "Admin@123",
        role : "Admin",
        status: "Active"
    };

    authUsers.push(adminUser);

    localStorage.setItem("users", JSON.stringify(authUsers));
} else if (!existingAdmin.password) {
    // giving the existing admin account a demo password
    existingAdmin.password = "Admin@123";

    localStorage.setItem("users", JSON.stringify(authUsers));
}

function checkPageAccess(requiredRole) {
    const currentUserData = localStorage.getItem("currentUser");

    // check whether someone is logged in

    if (!currentUserData) {
        window.location.href = "login.html";

        return false;
    }

    let currentUser;

    try {
        currentUser = JSON.parse(currentUserData);
    } catch(error) {
        localStorage.removeItem("currentUser");

        window.location.href = "login.html";

        return false;
    }

    // check account role

    if (currentUser.role !== requiredRole) {
        if (currentUser.role === "Admin") {
            window.location.href = "dashboard.html";
        } else if (currentUser.role === "User") {
            window.location.href = "user-dashboard.html";
        } else {
            localStorage.removeItem("currentUser");

            window.location.href = "login.html";
        }
        return false;
    }
    return true;
}

// Registration Form

const registerForm = document.getElementById("registerForm");

if (registerForm) {

    registerForm.addEventListener("submit", function(event) {

        event.preventDefault();

        const name = document.getElementById("registerName").value.trim();
        const email = document.getElementById("registerEmail").value.trim().toLowerCase();
        const password = document.getElementById("registerPassword").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const registerMessage = document.getElementById("registerMessage");
        // const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i;
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        registerMessage.classList.add("hidden");
        registerMessage.textContent = "";

        if (!name || !email || !password || !confirmPassword) {

            registerMessage.textContent = "Please fill in all the fields.";
            registerMessage.className = "text-sm text-red-600";
            return;
        }
        if (!emailPattern.test(email)) {

            registerMessage.textContent = "Please enter a valid mail address.";
            registerMessage.className = "text-sm text-red-600";
            return;
        }

        // Validate password length
        if (password.length < 6 || password.length > 12) {

            registerMessage.textContent = "Password must contain 6 to 12 characters.";
            registerMessage.className = "text-sm text-red-600";
            return;
        }

        if (password !== confirmPassword) {

            registerMessage.textContent = "Passwords do not match.";
            registerMessage.className = "text-sm text-red-600";
            return;
        }

        const existingUser = authUsers.find(user =>
            user.email.toLowerCase() === email
        );

        if (existingUser) {

            registerMessage.textContent = "This email is already registered.";
            registerMessage.className = "text-sm text-red-600";
            return;
        }
        const newUser = {
            id: Date.now(),
            name: name,
            email: email,
            password: password,
            role: "User",
            status: "Active"
        };

        authUsers.push(newUser);

        localStorage.setItem("users", JSON.stringify(authUsers));

        alert("Account created successfully! Please login.");

        window.location.href = "login.html";

    });
}

// Login form

const loginForm = document.getElementById("loginForm");

if (loginForm) {
    
    loginForm.addEventListener("submit", function(event) {
        event.preventDefault();
        

        // get login values

        const email = document.getElementById("loginEmail").value.trim().toLowerCase();

        const password = document.getElementById("loginPassword").value;

        const loginError = document.getElementById("loginError");

        // clear the previous error

        loginError.classList.add("hidden");
        loginError.textContent = "";

        // Find user by email
        const loggedInUser = authUsers.find(user => 
                    user.email.toLowerCase() === email
        );
        // Check whether account exists
        if (!loggedInUser) {
            loginError.textContent = "Invalid email or password.";
            loginError.classList.remove("hidden");

            return;
        }

        // check password
        if (loggedInUser.password !== password) {
            loginError.textContent = "Invalid email or password.";
            loginError.classList.remove("hidden");

            return;
        }

        // check account status

        if (loggedInUser.status.toLowerCase() !== "active") {
            loginError.textContent = "Your account is inactive. Please contact the administrator.";
            loginError.classList.remove("hidden");

            return;
        }

        // Save lloggedin user without password
        const currentUser = {
            id: loggedInUser.id,
            name: loggedInUser.name,
            email: loggedInUser.email,
            role: loggedInUser.role,
            status: loggedInUser.status
        };
        localStorage.setItem("currentUser", JSON.stringify(loggedInUser));

        // Redirect based on role
        if (loggedInUser.role === "Admin") {
            window.location.href = "dashboard.html";
        } else if (loggedInUser.role === "User") {
            window.location.href = "user-dashboard.html";
        } else {
            loginError.textContent = "Invalid account role.";
            loginError.classList.remove("hidden");

            localStorage.removeItem("currentUser");
        }
    });
}

// User Dashboard

const userDashboard = document.getElementById("welcomeUserName");

if (userDashboard) {
    const currentUserData = localStorage.getItem("currentUser");

    if (!currentUserData) {
        window.location.href = "login.html";
    } else {
        const currentUser = JSON.parse(currentUserData);

        // Allow only regular users
        if (currentUser.role !== "User") {
            window.location.href = "login.html";
        } else {

            // welcome section

            document.getElementById("welcomeUserName").textContent = 
                    currentUser.name;
            
            document.getElementById("navUserName").textContent = 
                    currentUser.name;

            // Statistics cards
            document.getElementById("userAccountStatus").textContent = 
                currentUser.status;

            document.getElementById("userAccountRole").textContent = 
                currentUser.role;
            
            document.getElementById("userAccountId").textContent = 
                currentUser.id;

            // Profile Information

            document.getElementById("profileName").textContent = 
                currentUser.name;

            document.getElementById("profileEmail").textContent = 
                currentUser.email;

            document.getElementById("profileRole").textContent =
                currentUser.role;

            document.getElementById("profileStatus").textContent =
                currentUser.status;       
        }
    }
}

// edit profile
const editProfileBtn = document.getElementById("editProfileBtn");
const editProfileModal = document.getElementById("editProfileModal");
const closeProfileModal = document.getElementById("closeProfileModal");
const cancelProfileEdit = document.getElementById("cancelProfileEdit");

const editProfileForm = document.getElementById("editProfileForm");

const editProfileName = document.getElementById("editProfileName");
const editProfileEmail = document.getElementById("editProfileEmail");

const profileEditError = document.getElementById("profileEditError");

if (
    editProfileBtn &&
    editProfileModal &&
    closeProfileModal &&
    cancelProfileEdit &&
    editProfileForm &&
    editProfileName &&
    editProfileEmail &&
    profileEditError
) {

    let profileCurrentUser = null;

    try {
        profileCurrentUser = JSON.parse(
            localStorage.getItem("currentUser")
        );
    } catch (error) {
        console.error("Error reading current user:", error);
    }

    if (profileCurrentUser) {

        // Open Edit Profile modal
        editProfileBtn.addEventListener("click", function() {

            editProfileName.value = profileCurrentUser.name;
            editProfileEmail.value = profileCurrentUser.email;

            profileEditError.textContent = "";
            profileEditError.classList.add("hidden");

            editProfileModal.classList.remove("hidden");

        });

        // Close modal function
        function closeEditProfileModal() {
            editProfileModal.classList.add("hidden");
        }

        // Close button
        closeProfileModal.addEventListener(
            "click",
            closeEditProfileModal
        );

        // Cancel button
        cancelProfileEdit.addEventListener(
            "click",
            closeEditProfileModal
        );

        editProfileForm.addEventListener("submit", function(event) {

            event.preventDefault();

            const updatedName = editProfileName.value.trim();
            const updatedEmail = editProfileEmail.value.trim().toLowerCase();

            // const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i;
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            profileEditError.textContent = "";
            profileEditError.classList.add("hidden");

            profileEditError.classList.remove("text-green-600");
            profileEditError.classList.add("text-red-500");

            if (!updatedName || !updatedEmail) {

                profileEditError.textContent = "Please fill in all fields.";
                profileEditError.classList.remove("hidden");
                return;
            }

            // Validate mail address
            if (!emailPattern.test(updatedEmail)) {

                profileEditError.textContent =
                    "Please enter a valid mail address.";

                profileEditError.classList.remove("hidden");
                return;
            }

            let profileUsers = [];

            try {
                profileUsers = JSON.parse(
                    localStorage.getItem("users")
                ) || [];

                if (!Array.isArray(profileUsers)) {
                    profileUsers = [];
                }

            } catch (error) {
                profileEditError.textContent = "Unable to retrieve user data.";
                profileEditError.classList.remove("hidden");
                return;
            }

            // Find current Admin account
            const adminIndex = profileUsers.findIndex(
                user => user.id === profileCurrentUser.id
            );

            if (adminIndex === -1) {

                profileEditError.textContent = "User account not found.";
                profileEditError.classList.remove("hidden");
                return;
            }

            // Check duplicate email
            const emailExists = profileUsers.some(user =>
                (user.email || "").toLowerCase() === updatedEmail &&
                user.id !== profileCurrentUser.id
            );

            if (emailExists) {

                profileEditError.textContent =
                    "This email is already registered.";

                profileEditError.classList.remove("hidden");
                return;
            }

            profileUsers[adminIndex].name = updatedName;
            profileUsers[adminIndex].email = updatedEmail;

            localStorage.setItem(
                "users",
                JSON.stringify(profileUsers)
            );

            profileCurrentUser.name = updatedName;
            profileCurrentUser.email = updatedEmail;

            localStorage.setItem(
                "currentUser",
                JSON.stringify(profileCurrentUser)
            );

            document.getElementById("profileDisplayName").textContent = updatedName;
            document.getElementById("profileName").textContent = updatedName;
            document.getElementById("profileEmail").textContent = updatedEmail;

            closeEditProfileModal();

            const successToast = document.getElementById("successToast");

            if (successToast) {

                successToast.classList.remove("hidden");

                setTimeout(function() {
                    successToast.classList.add("hidden");
                }, 3000);

            }

        });

        function clearProfileMessage() {

            profileEditError.textContent = "";
            profileEditError.classList.add("hidden");

        }

        editProfileName.addEventListener(
            "input",
            clearProfileMessage
        );

        editProfileEmail.addEventListener(
            "input",
            clearProfileMessage
        );

    }

}

const logoutBtn = document.getElementById("logoutBtn");

if (logoutBtn) {
    logoutBtn.addEventListener("click", function(event) {
        event.preventDefault();

        console.log("Logout button clicked");

        const confirmLogout = confirm("Are you sure you want to logout?");

        if (confirmLogout) {
            localStorage.removeItem("currentUser");
            window.location.href = "login.html";
        }
    });
}

(function () {

    const editUserProfileBtn = document.getElementById("editUserProfileBtn");
    const editUserProfileModal = document.getElementById("editUserProfileModal");

    // Run only on User Dashboard
    if (!editUserProfileBtn || !editUserProfileModal) {
        return;
    }

    const closeUserProfileModal = document.getElementById("closeUserProfileModal");
    const cancelUserProfileEdit = document.getElementById("cancelUserProfileEdit");

    const editUserProfileForm = document.getElementById("editUserProfileForm");

    const editUserName = document.getElementById("editUserName");
    const editUserEmail = document.getElementById("editUserEmail");

    const userProfileError = document.getElementById("userProfileError");
    const userProfileSuccessToast = document.getElementById("userProfileSuccessToast");

    function getCurrentUser() {

        return JSON.parse(localStorage.getItem("currentUser"));

    }

    function clearUserProfileError() {

        userProfileError.textContent = "";
        userProfileError.classList.add("hidden");

    }

    editUserProfileBtn.addEventListener("click", function () {

        const currentUser = getCurrentUser();

        if (!currentUser) {
            window.location.href = "login.html";
            return;
        }

        editUserName.value = currentUser.name || "";
        editUserEmail.value = currentUser.email || "";

        clearUserProfileError();

        editUserProfileModal.classList.remove("hidden");

    });

    function closeUserProfile() {

        editUserProfileModal.classList.add("hidden");
        clearUserProfileError();

    }

    closeUserProfileModal.addEventListener("click", closeUserProfile);

    cancelUserProfileEdit.addEventListener("click", closeUserProfile);

    editUserName.addEventListener("input", clearUserProfileError);
    editUserEmail.addEventListener("input", clearUserProfileError);

    editUserProfileForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const updatedName = editUserName.value.trim();
        const updatedEmail = editUserEmail.value.trim().toLowerCase();

        // const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i;
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        clearUserProfileError();

        const currentUser = getCurrentUser();

        if (!currentUser) {
            window.location.href = "login.html";
            return;
        }


        if (!updatedName || !updatedEmail) {

            userProfileError.textContent = "Please fill in all fields.";
            userProfileError.classList.remove("hidden");

            return;

        }

        if (!emailPattern.test(updatedEmail)) {

            userProfileError.textContent = "Please enter a valid mail address.";
            userProfileError.classList.remove("hidden");

            return;

        }

        let storedUsers = [];

        try {

            storedUsers = JSON.parse(localStorage.getItem("users")) || [];

            if (!Array.isArray(storedUsers)) {
                storedUsers = [];
            }

        } catch (error) {

            userProfileError.textContent = "Unable to load account information.";
            userProfileError.classList.remove("hidden");

            return;

        }

        const userIndex = storedUsers.findIndex(user =>
            String(user.id) === String(currentUser.id) &&
            user.role === "User"
        );

        if (userIndex === -1) {

            userProfileError.textContent = "User account not found.";
            userProfileError.classList.remove("hidden");

            return;

        }

        const emailExists = storedUsers.some(user =>
            user.email.toLowerCase() === updatedEmail &&
            String(user.id) !== String(currentUser.id)
        );

        if (emailExists) {

            userProfileError.textContent = "This email is already registered.";
            userProfileError.classList.remove("hidden");

            return;

        }

        storedUsers[userIndex].name = updatedName;
        storedUsers[userIndex].email = updatedEmail;

        localStorage.setItem("users", JSON.stringify(storedUsers));

        currentUser.name = updatedName;
        currentUser.email = updatedEmail;

        localStorage.setItem("currentUser", JSON.stringify(currentUser));

        document.getElementById("navUserName").textContent = updatedName;
        document.getElementById("welcomeUserName").textContent = updatedName;

        document.getElementById("profileDisplayName").textContent = updatedName;
        document.getElementById("profileName").textContent = updatedName;
        document.getElementById("profileEmail").textContent = updatedEmail;

        closeUserProfile();

        userProfileSuccessToast.classList.remove("hidden");

        setTimeout(function () {

            userProfileSuccessToast.classList.add("hidden");

        }, 3000);

    });

})();

(function () {

    const changePasswordNav = document.getElementById("changePasswordNav");
    const changePasswordModal = document.getElementById("changePasswordModal");

    if (!changePasswordNav || !changePasswordModal) {
        return;
    }

    const closePasswordModal = document.getElementById("closeChangePasswordModal");
    const cancelPassword = document.getElementById("cancelChangePassword");

    const changePasswordForm = document.getElementById("changePasswordForm");

    const currentPassword = document.getElementById("currentPassword");
    const newPassword = document.getElementById("newPassword");
    const confirmNewPassword = document.getElementById("confirmNewPassword");

    const changePasswordError = document.getElementById("changePasswordError");

    function clearPasswordError() {
        changePasswordError.textContent = "";
        changePasswordError.classList.add("hidden");
    }

    changePasswordNav.addEventListener("click", function (event) {

        event.preventDefault();

        changePasswordForm.reset();
        clearPasswordError();

        changePasswordModal.classList.remove("hidden");

    });

    function closePasswordChangeModal() {

        changePasswordModal.classList.add("hidden");
        changePasswordForm.reset();
        clearPasswordError();

    }


    closePasswordModal.addEventListener("click", closePasswordChangeModal);

    cancelPassword.addEventListener("click", closePasswordChangeModal);

    currentPassword.addEventListener("input", clearPasswordError);
    newPassword.addEventListener("input", clearPasswordError);
    confirmNewPassword.addEventListener("input", clearPasswordError);

    changePasswordForm.addEventListener("submit", function (event) {

        event.preventDefault();

        clearPasswordError();

        const oldPassword = currentPassword.value;
        const updatedPassword = newPassword.value;
        const confirmPassword = confirmNewPassword.value;

        const loggedInUser = JSON.parse(localStorage.getItem("currentUser"));

        if (!loggedInUser) {
            window.location.href = "login.html";
            return;
        }

        if (!oldPassword || !updatedPassword || !confirmPassword) {

            changePasswordError.textContent = "Please fill in all fields.";
            changePasswordError.classList.remove("hidden");

            return;

        }

        if (oldPassword !== loggedInUser.password) {

            changePasswordError.textContent = "Current password is incorrect.";
            changePasswordError.classList.remove("hidden");

            return;

        }

        if (updatedPassword.length < 6 || updatedPassword.length > 12) {

            changePasswordError.textContent = "New password must contain 6 to 12 characters.";
            changePasswordError.classList.remove("hidden");

            return;

        }

        if (updatedPassword !== confirmPassword) {

            changePasswordError.textContent = "New passwords do not match.";
            changePasswordError.classList.remove("hidden");

            return;

        }

        if (oldPassword === updatedPassword) {

            changePasswordError.textContent = "New password must be different from current password.";
            changePasswordError.classList.remove("hidden");

            return;

        }

        let storedUsers = JSON.parse(localStorage.getItem("users")) || [];

        const userIndex = storedUsers.findIndex(user =>
            String(user.id) === String(loggedInUser.id) &&
            user.role === "User"
        );

        if (userIndex === -1) {

            changePasswordError.textContent = "User account not found.";
            changePasswordError.classList.remove("hidden");

            return;

        }

        storedUsers[userIndex].password = updatedPassword;
        loggedInUser.password = updatedPassword;

        localStorage.setItem("users", JSON.stringify(storedUsers));
        localStorage.setItem("currentUser", JSON.stringify(loggedInUser));

changePasswordModal.classList.add("hidden");

changePasswordForm.reset();

        const passwordSuccessToast = document.getElementById("passwordSuccessToast");

        if (passwordSuccessToast) {

            passwordSuccessToast.classList.remove("hidden");
            passwordSuccessToast.classList.add("flex");

            setTimeout(function () {

                passwordSuccessToast.classList.add("hidden");
                passwordSuccessToast.classList.remove("flex");

            }, 3000);

        }

    });

})();

(function () {

    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const userSidebar = document.getElementById("userSidebar");
    const mobileSidebarOverlay = document.getElementById("mobileSidebarOverlay");

    if (!mobileMenuBtn || !userSidebar || !mobileSidebarOverlay) {
        return;
    }

    const menuIcon = mobileMenuBtn.querySelector("i");

    function openMobileMenu() {
        userSidebar.classList.remove("-translate-x-full");
        mobileSidebarOverlay.classList.remove("hidden");

        mobileMenuBtn.setAttribute("aria-expanded", "true");
        menuIcon.classList.replace("fa-bars", "fa-xmark");
    }

    function closeMobileMenu() {
        userSidebar.classList.add("-translate-x-full");
        mobileSidebarOverlay.classList.add("hidden");

        mobileMenuBtn.setAttribute("aria-expanded", "false");
        menuIcon.classList.replace("fa-xmark", "fa-bars");
    }

    mobileMenuBtn.addEventListener("click", function () {

        const isOpen = mobileMenuBtn.getAttribute("aria-expanded") === "true";

        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }

    });

    mobileSidebarOverlay.addEventListener("click", closeMobileMenu);

    userSidebar.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", closeMobileMenu);
    });

    window.addEventListener("resize", function () {
        if (window.innerWidth >= 768) {
            closeMobileMenu();
        }
    });

})();