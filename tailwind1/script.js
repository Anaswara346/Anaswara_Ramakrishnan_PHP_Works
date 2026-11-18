
let users = JSON.parse(localStorage.getItem("users")) || [];

let instructors = JSON.parse(localStorage.getItem("instructors")) || [];

const courses = [

    {
        id: 1,
        name: "Python Programming",
        description: "Learn Python programming from basics to advanced concepts.",
        duration: "3 Months",
        price: 5000,
        features: [
            "Python Fundamentals",
            "Practical Exercises",
            "Certificate"
        ]
    },

    {
        id: 2,
        name: "Web Development",
        description: "Learn to create responsive and interactive websites.",
        duration: "4 Months",
        price: 8000,
        features: [
            "HTML, CSS and JavaScript",
            "Responsive Design",
            "Certificate"
        ]
    },

    {
        id: 3,
        name: "Data Science",
        description: "Learn data analysis and machine learning concepts.",
        duration: "6 Months",
        price: 12000,
        features: [
            "Python and Statistics",
            "Machine Learning Basics",
            "Certificate"
        ]
    }

];

function saveData() {

    localStorage.setItem("users", JSON.stringify(users));

    localStorage.setItem("instructors", JSON.stringify(instructors));

}

function generateId(data) {

    if (data.length === 0) {

        return 1;

    }

    return Math.max(...data.map(item => Number(item.id) || 0)) + 1;

}

function escapeHTML(value) {

    return String(value ?? "").replace(/[&<>"']/g, function(character) {

        const entities = {

            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"

        };

        return entities[character];

    });

}

const registerForm = document.getElementById("registerForm");

if (registerForm) {

    registerForm.addEventListener("submit", function(event) {

        event.preventDefault();

        const name = document.getElementById("registerName").value.trim();
        const email = document.getElementById("registerEmail").value.trim().toLowerCase();

        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!emailPattern.test(email)) {

            message.textContent = "Please enter a valid email address.";
            message.className = "text-sm text-red-600";

            return;
        }
        const password = document.getElementById("registerPassword").value;

        const confirmPassword = document.getElementById("confirmPassword").value;
        const message = document.getElementById("registerMessage");

        if (password !== confirmPassword) {
            message.textContent = "Passwords do not match.";
            message.className = "text-sm text-red-600";
            return;

        }

        const existingUser = users.some(function(user) {

            return user.email.toLowerCase() === email;

        });

        if (existingUser) {

            message.textContent = "This email is already registered.";

            message.className = "text-sm text-red-600";

            return;

        }

        const newUser = {

            id: generateId(users),

            name: name,

            email: email,

            password: password,

            registrationDate: new Date().toLocaleDateString("en-IN")

        };

        users.push(newUser);

        saveData();


        message.textContent = "Registration successful! Redirecting to login...";

        message.className = "text-sm text-green-600";


        registerForm.reset();

        setTimeout(function() {

            window.location.href = "login.html";

        }, 1200);

    });

}



// LOGIN SYSTEM

const loginForm = document.getElementById("loginForm");

if (loginForm) {

    loginForm.addEventListener("submit", function(event) {

        event.preventDefault();

        const email = document.getElementById("loginEmail").value.trim().toLowerCase();
        const password = document.getElementById("loginPassword").value;

        const message = document.getElementById("loginMessage");

        // Admin login
        if (email === "admin@gmail.com" && password === "admin123") {

            localStorage.setItem("adminLoggedIn", "true");
            localStorage.removeItem("loggedInUser");

            message.textContent = "Admin login successful!";
            message.className = "bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm mb-5";

            setTimeout(function() {
                window.location.href = "index.html";
            }, 800);

            return;
        }

        // Registered user login
        const users = JSON.parse(localStorage.getItem("users")) || [];

        const loggedInUser = users.find(user =>
            user.email.toLowerCase() === email &&
            user.password === password
        );

        if (loggedInUser) {

            localStorage.setItem("loggedInUser", JSON.stringify(loggedInUser));
            localStorage.removeItem("adminLoggedIn");

            message.textContent = "Login successful! Welcome to LearnHub.";
            message.className = "bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm mb-5";

            setTimeout(function() {
                window.location.href = "userdashboard.html";
            }, 800);

        } else {

            message.textContent = "Invalid email or password.";
            message.className = "bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm mb-5";

        }

    });

}


const dashboardPage = document.getElementById("userTable");

if (dashboardPage) {

    if (localStorage.getItem("adminLoggedIn") !== "true") {

        window.location.href = "login.html";

    } else {

        initializeDashboard();

    }

}

function initializeDashboard() {

    updateDashboard();

    displayUsers();

    displayCourses();
    displayInstructors();

    setupDashboardEvents();

}

function updateDashboard() {

    document.getElementById("totalUsers").textContent = users.length;
    document.getElementById("totalCourses").textContent = courses.length;
    document.getElementById("totalInstructors").textContent = instructors.length;

}


function displayUsers(search = "") {

    const filteredUsers = users.filter(user =>
        String(user.name || "").toLowerCase().includes(search.toLowerCase()) ||
        String(user.email || "").toLowerCase().includes(search.toLowerCase())
    );

    if (filteredUsers.length === 0) {
        userTable.innerHTML = `
            <tr>
                <td colspan="5" class="text-center p-5 text-gray-500">
                    No users found
                </td>
            </tr>
        `;
        return;
    }

    userTable.innerHTML = filteredUsers.map(user => `
        <tr class="border-b border-gray-200 hover:bg-gray-50">

            <td class="p-3">${escapeHTML(user.id)}</td>

            <td class="p-3">${escapeHTML(user.name)}</td>

            <td class="p-3">${escapeHTML(user.email)}</td>

            <td class="p-3">${escapeHTML(user.registrationDate || "-")}</td>

            <td class="p-3">
                <div class="flex gap-2">

                    <button
                        type="button"
                        class="edit-user-btn bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-blue-800 transition"
                        data-id="${escapeHTML(user.id)}">
                        Edit
                    </button>

                    <button
                        type="button"
                        class="delete-user-btn bg-red-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-red-800 transition"
                        data-action="delete-user"
                        data-id="${escapeHTML(user.id)}">
                        Delete
                    </button>

                </div>
            </td>

        </tr>
    `).join("");
}

function displayCourses() {

    const courseCards = document.getElementById("courseCards");

    if (!courseCards) return;

    const cardColors = [
        "from-blue-500 to-indigo-400",
        "from-purple-500 to-violet-400",
        "from-cyan-500 to-blue-400"
    ];

    courseCards.innerHTML = courses.map(function(course, index) {

        const cardColor = cardColors[index % cardColors.length];

        return `
            <div class="bg-gradient-to-br ${cardColor} rounded-xl p-6 shadow-md shadow-blue-200/50 hover:shadow-2xl hover:shadow-indigo-300/70 hover:-translate-y-2 hover:scale-[1.02] transition-all duration-300 ease-in-out cursor-pointer">

                <div class="bg-white/25 text-white w-12 h-12 flex items-center justify-center rounded-lg mb-5">

                    <i class="fa-solid fa-book-open text-xl"></i>

                </div>

                <h3 class="text-xl font-bold text-white mb-2">

                    ${escapeHTML(course.name)}

                </h3>

                <p class="text-sm text-white/90 mb-5">

                    ${escapeHTML(course.description)}

                </p>

                <div class="mb-5">

                    <p class="text-3xl font-bold text-white">

                        ₹${course.price.toLocaleString("en-IN")}

                    </p>

                    <p class="text-sm text-white/90 mt-1">

                        Duration: ${escapeHTML(course.duration)}

                    </p>

                </div>

                <div class="border-t border-white/40 pt-4">

                    <p class="font-semibold text-sm text-white mb-3">

                        Course Features

                    </p>

                    <ul class="space-y-2 text-sm text-white">

                        ${course.features.map(function(feature) {

                            return `

                                <li class="flex items-center gap-2">

                                    <i class="fa-solid fa-check text-white"></i>

                                    ${escapeHTML(feature)}

                                </li>

                            `;

                        }).join("")}

                    </ul>

                </div>

            </div>
        `;

    }).join("");

}


function displayInstructors() {

    const instructorTable = document.getElementById("instructorTable");

    if (!instructorTable) return;
    if (instructors.length === 0) {

        instructorTable.innerHTML = `

            <tr>

                <td colspan="5" class="text-center p-6 text-gray-500">

                    No instructors available. Add your first instructor.

                </td>
            </tr>
        `;
        return;
    }

    instructorTable.innerHTML = instructors.map(function(instructor) {

        return `

            <tr class="border-b border-gray-100 hover:bg-indigo-50/50 transition duration-200">

                <td class="p-4">

                    ${Number(instructor.id)}

                </td>


                <td class="p-4 font-medium">

                    ${escapeHTML(instructor.name)}

                </td>


                <td class="p-4">

                    ${escapeHTML(instructor.email)}

                </td>


                <td class="p-4">

                    ${escapeHTML(instructor.specialization)}

                </td>


                <td class="p-4">

                    <div class="flex gap-3">

                        <button

                            data-action="edit-instructor"

                            data-id="${Number(instructor.id)}"

                            class="editBtn bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-blue-900 hover:shadow-blue-200 transition duration-300">

                            Edit

                        </button>


                        <button

                            data-action="delete-instructor"

                            data-id="${Number(instructor.id)}"

                            class="dltBtn bg-red-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-red-800 hover:shadow-red-200 transition duration-300">

                            Delete

                        </button>

                    </div>

                </td>

            </tr>

        `;

    }).join("");

}

function openModal(modalId) {

    const modal = document.getElementById(modalId);

    modal.classList.remove("hidden");

    modal.classList.add("flex");

}

function closeModal(modalId) {

    const modal = document.getElementById(modalId);

    modal.classList.add("hidden");

    modal.classList.remove("flex");

}



function setupDashboardEvents() {

    document.getElementById("logoutBtn").addEventListener("click", function() {

        localStorage.removeItem("adminLoggedIn");

        window.location.href = "login.html";

    });

    document.getElementById("addInstructorBtn").addEventListener("click", function() {

        document.getElementById("instructorForm").reset();

        document.getElementById("instructorId").value = "";

        document.getElementById("instructorModalTitle").textContent = "Add Instructor";

        openModal("instructorModal");

    });

    document.getElementById("closeInstructorModal").addEventListener("click", function() {

        closeModal("instructorModal");

    });


    document.getElementById("cancelInstructorModal").addEventListener("click", function() {

        closeModal("instructorModal");

    });

    document.getElementById("instructorForm").addEventListener("submit", function(event) {

        event.preventDefault();

        const id = document.getElementById("instructorId").value;

        const instructorData = {

            name: document.getElementById("instructorName").value.trim(),

            email: document.getElementById("instructorEmail").value.trim().toLowerCase(),

            specialization: document.getElementById("instructorSpecialization").value.trim()

        };
        if (id) {

            const index = instructors.findIndex(function(item) {

                return Number(item.id) === Number(id);

            });

            if (index !== -1) {

                instructors[index] = {

                    ...instructors[index],

                    ...instructorData

                };

            }

        } else {

            instructorData.id = generateId(instructors);

            instructors.push(instructorData);

        }
        saveData();

        displayInstructors();

        updateDashboard();

        closeModal("instructorModal");

        this.reset();

    });

    document.getElementById("userTable").addEventListener("click", function(event) {

        const editButton = event.target.closest(".edit-user-btn");

        if (editButton) {

            const id = editButton.dataset.id;

            const user = users.find(function(item) {

                return String(item.id) === id;

            });

            if (!user) return;

            document.getElementById("userId").value = user.id;

            document.getElementById("editUserName").value = user.name;

            document.getElementById("editUserEmail").value = user.email;

            openModal("userModal");

        }


        const deleteButton = event.target.closest("button[data-action='delete-user']");

        if (deleteButton) {

            const id = Number(deleteButton.dataset.id);

            if (confirm("Are you sure you want to delete this registered user?")) {

                users = users.filter(function(user) {

                    return Number(user.id) !== id;

                });

                saveData();

                displayUsers();

                updateDashboard();

            }

        }

    });

    document.getElementById("userForm").addEventListener("submit", function(event) {

        event.preventDefault();

        const id = document.getElementById("userId").value;

        const name = document.getElementById("editUserName").value.trim();

        const email = document.getElementById("editUserEmail").value.trim().toLowerCase();


        const existingUser = users.find(function(user) {

            return String(user.id) !== id &&

                String(user.email || "").toLowerCase() === email;
        });

        if (existingUser) {

            alert("This email is already registered.");

            return;

        }


        const user = users.find(function(item) {

            return String(item.id) === id;

        });


        if (!user) return;


        user.name = name;

        user.email = email;

        saveData();

        displayUsers();

        updateDashboard();

        closeModal("userModal");

        this.reset();

        alert("User updated successfully!");

    });

    document.getElementById("closeUserModal").addEventListener("click", function() {

        closeModal("userModal");

    });


    document.getElementById("cancelUserModal").addEventListener("click", function() {

        closeModal("userModal");

    });

    document.getElementById("instructorTable").addEventListener("click", function(event) {

        const button = event.target.closest("button[data-action]");

        if (!button) return;

        const id = Number(button.dataset.id);

        const action = button.dataset.action;

        const instructor = instructors.find(function(item) {

            return Number(item.id) === id;

        });

        if (!instructor) return;


        // Edit Instructor

        if (action === "edit-instructor") {

            document.getElementById("instructorId").value = instructor.id;

            document.getElementById("instructorName").value = instructor.name;

            document.getElementById("instructorEmail").value = instructor.email;

            document.getElementById("instructorSpecialization").value = instructor.specialization;

            document.getElementById("instructorModalTitle").textContent = "Edit Instructor";

            openModal("instructorModal");

        }


        if (action === "delete-instructor") {

            if (confirm("Are you sure you want to delete this instructor?")) {

                instructors = instructors.filter(function(item) {

                    return Number(item.id) !== id;

                });

                saveData();

                displayInstructors();

                updateDashboard();

            }
        }

    });

}


const userNameElement = document.getElementById("userName");

if (userNameElement) {

    const loggedInUser = JSON.parse(
        localStorage.getItem("loggedInUser")
    );

    // Check whether a user is logged in
    if (!loggedInUser) {

        window.location.href = "login.html";

    } else {

        document.getElementById("userName").textContent =
            loggedInUser.name;

        document.getElementById("profileName").textContent =
            loggedInUser.name;

        document.getElementById("profileEmail").textContent =
            loggedInUser.email;

    }

    // Logout
    document.getElementById("userLogoutBtn").addEventListener("click", function () {

        localStorage.removeItem("loggedInUser");

        window.location.href = "login.html";

    });

}

const mobileMenuBtn = document.getElementById("mobileMenuBtn");
const mobileMenu = document.getElementById("mobileMenu");

if (mobileMenuBtn && mobileMenu) {

    mobileMenuBtn.addEventListener("click", function() {

        mobileMenu.classList.toggle("hidden");

        const isOpen = !mobileMenu.classList.contains("hidden");

        mobileMenuBtn.innerHTML = isOpen
            ? '<i class="fa-solid fa-xmark"></i>'
            : '<i class="fa-solid fa-bars"></i>';

        mobileMenuBtn.setAttribute("aria-expanded", isOpen);

    });

    mobileMenu.querySelectorAll("a").forEach(function(link) {

        link.addEventListener("click", function() {

            mobileMenu.classList.add("hidden");

            mobileMenuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';

            mobileMenuBtn.setAttribute("aria-expanded", "false");

        });

    });

}

