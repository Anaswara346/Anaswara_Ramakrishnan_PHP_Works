// to get sidebar and buttons

const sidebar = document.getElementById("sidebar");
const openSidebar = document.getElementById("openSidebar");
const closeSidebar = document.getElementById("closeSidebar");

// open sidebar
    // -translate-x-full hides the sidebar by moving it outside the screen. 
    //  removes that class to show it.

if (sidebar && openSidebar && closeSidebar) {

    openSidebar.addEventListener("click", function () {
        sidebar.classList.remove("-translate-x-full");
    });

    closeSidebar.addEventListener("click", function () {
        sidebar.classList.add("-translate-x-full");
    });

}

// get the stored users
let storedUsers = localStorage.getItem("users");
let users = storedUsers ? JSON.parse(storedUsers) : [];

let currentPage = 1;
const usersPerPage = 5;

function displayUsers() {

    const userTableBody = document.getElementById("userTableBody");
    const userCount = document.getElementById("userCount");

    if (!userTableBody) return;

    // Get search and filter values
    const search = document.getElementById("searchUser")?.value.toLowerCase() || "";
    const role = document.getElementById("filterRole")?.value || "all";
    const status = document.getElementById("filterStatus")?.value || "all";
    const sort = document.getElementById("sortUsers")?.value || "newest";

    // Filter users
    let filteredUsers = users.filter(user => {
        if (user.role !== "User") {
            return false;
        }

        const matchesSearch =
            user.name.toLowerCase().includes(search) ||
            user.email.toLowerCase().includes(search);

        const matchesRole =
            role === "all" || user.role === role;

        const matchesStatus =
            status === "all" || user.status === status;

        return matchesSearch && matchesRole && matchesStatus;
    });

    // Sort users
    switch (sort) {

        case "newest":
            filteredUsers.sort((a, b) => b.id - a.id);
            break;

        case "oldest":
            filteredUsers.sort((a, b) => a.id - b.id);
            break;

        case "name-asc":
            filteredUsers.sort((a, b) =>
                a.name.localeCompare(b.name)
            );
            break;

        case "name-desc":
            filteredUsers.sort((a, b) =>
                b.name.localeCompare(a.name)
            );
            break;
    }

    // Calculate pagination
    const totalUsers = filteredUsers.length;
    const totalPages = Math.ceil(totalUsers / usersPerPage);

    if (totalPages === 0) {
        currentPage = 1;
    } else if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    const startIndex = (currentPage - 1) * usersPerPage;
    const endIndex = startIndex + usersPerPage;

    // Get users for current page
    const paginatedUsers = filteredUsers.slice(startIndex, endIndex);

    // Clear table
    userTableBody.innerHTML = "";

    // Display users
    if (paginatedUsers.length === 0) {

        userTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center p-8 text-gray-500">
                    No users found.
                </td>
            </tr>
        `;

    } else {

        paginatedUsers.forEach(user => {

            const row = document.createElement("tr");

            row.className = "border-t border-gray-200 hover:bg-blue-50 transition";

            const statusClass = user.status === "Active"
                ? "bg-green-100 text-green-700"
                : "bg-red-100 text-red-700";

            row.innerHTML = `
                <td class="px-6 py-4">${user.id}</td>

                <td class="px-6 py-4 font-medium text-gray-800">
                    ${escapeHTML(user.name)}
                </td>

                <td class="px-6 py-4">
                    ${escapeHTML(user.email)}
                </td>

                <td class="px-6 py-4">
                    ${escapeHTML(user.role)}
                </td>

                <td class="px-6 py-4">
                    <span class="${statusClass} px-3 py-1 rounded-full text-xs font-medium">
                        ${escapeHTML(user.status)}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-4">

                        <button class="text-blue-600 hover:text-blue-800"
                            onclick="editUser(${user.id})">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <button class="text-red-600 hover:text-red-800"
                            onclick="deleteUser(${user.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </div>
                </td>
            `;

            userTableBody.appendChild(row);

        });

    }
    if (userCount) {
        userCount.textContent = `Showing ${totalUsers} users`;
    }

    updatePagination(totalUsers, startIndex, endIndex);

}

function updatePagination(totalUsers, startIndex, endIndex) {

    const paginationInfo = document.getElementById("paginationInfo");
    const pageNumbers = document.getElementById("pageNumbers");
    const prevPage = document.getElementById("prevPage");
    const nextPage = document.getElementById("nextPage");

    if (!paginationInfo || !pageNumbers || !prevPage || !nextPage) {
        return;
    }

    const totalPages = Math.ceil(totalUsers / usersPerPage);

    // Pagination information
    if (totalUsers === 0) {
        paginationInfo.textContent = "Showing 0 to 0 of 0 users";
    } else {
        paginationInfo.textContent =
            `Showing ${startIndex + 1} to ${Math.min(endIndex, totalUsers)} of ${totalUsers} users`;
    }

    // Previous and Next buttons
    prevPage.disabled = currentPage === 1;
    nextPage.disabled = currentPage === totalPages || totalPages === 0;

    // Page number buttons
    pageNumbers.innerHTML = "";

    for (let i = 1; i <= totalPages; i++) {

        const button = document.createElement("button");

        button.textContent = i;

        button.className = i === currentPage
            ? "px-3 py-2 bg-blue-600 text-white rounded-md text-sm"
            : "px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-100";

        button.addEventListener("click", function () {
            currentPage = i;
            displayUsers();
        });

        pageNumbers.appendChild(button);
    }

}
document.getElementById("prevPage")?.addEventListener("click", function () {

    if (currentPage > 1) {
        currentPage--;
        displayUsers();
    }

});

document.getElementById("nextPage")?.addEventListener("click", function () {

        currentPage++;
        displayUsers();

});

const searchUser = document.getElementById("searchUser");
const filterRole = document.getElementById("filterRole");
const filterStatus = document.getElementById("filterStatus");
const sortUsers = document.getElementById("sortUsers");

function resetPagination() {
    currentPage = 1;
    displayUsers();
}

if (searchUser) {
    searchUser.addEventListener("input", resetPagination);
}

if (filterRole) {
    filterRole.addEventListener("change", resetPagination);
}

if (filterStatus) {
    filterStatus.addEventListener("change", resetPagination);
}

if (sortUsers) {
    sortUsers.addEventListener("change", resetPagination);
}

// Initial display
displayUsers();


// escape user provided text before inserting HTML
function escapeHTML(value) {

    return String(value).replace(/[&<>"']/g, char => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
    })[char]);

}

function editUser(id) {

    const user = users.find(user => user.id === id);

    if (!user) {
        alert("User not found.");
        return;
    }

    document.getElementById("editUserId").value = user.id;
    document.getElementById("editUserName").value = user.name;
    document.getElementById("editUserEmail").value = user.email;
    document.getElementById("editUserRole").value = user.role;
    document.getElementById("editUserStatus").value = user.status;

    document.getElementById("editUserModal").classList.remove("hidden");
}

function closeEditModal() {
    document.getElementById("editUserModal").classList.add("hidden");
}

const addUserForm = document.getElementById("addUserForm");

if (addUserForm) {

    addUserForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const name = document.getElementById("userName").value.trim();
        const email = document.getElementById("userEmail").value.trim().toLowerCase();
        const role = document.getElementById("userRole").value;
        const status = document.getElementById("userStatus").value;

        if (!name || !email || !role || !status) {
            alert("Please fill in all fields.");
            return;
        }

        // Check whether email already exists
        const existingUser = users.find(user =>
            user.email.toLowerCase() === email
        );

        if (existingUser) {
            alert("This email address is already registered.");
            return;
        }

        // Create new user
        const newUser = {
            id: Date.now(),
            name: name,
            email: email,
            role: role,
            status: status
        };

        // Add user to array
        users.push(newUser);

        // Save users
        localStorage.setItem("users", JSON.stringify(users));

        alert("User added successfully!");

        window.location.href = "users.html";

    });

}

// Update User
const editUserForm = document.getElementById("editUserForm");

if (editUserForm) {

    editUserForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const id = Number(document.getElementById("editUserId").value);
        const name = document.getElementById("editUserName").value.trim();
        const email = document.getElementById("editUserEmail").value.trim().toLowerCase();
        const role = document.getElementById("editUserRole").value;
        const status = document.getElementById("editUserStatus").value;
        const existingUser = users.find(user =>
            user.email.toLowerCase() === email && user.id !== id
        );

        if (existingUser) {
            alert("This email address is already registered.");
            return;
        }
        const userIndex = users.findIndex(user => user.id === id);

        if (userIndex === -1) {
            alert("User not found.");
            return;
        }

        users[userIndex] = {
            ...users[userIndex],
            name: name,
            email: email,
            role: role,
            status: status
        };

        localStorage.setItem("users", JSON.stringify(users));
        const searchValue = document.getElementById("searchUser")?.value || "";
        displayUsers(searchValue);

        closeEditModal();

        alert("User updated successfully!");
    });
}

// Delete User

function deleteUser(id) {
    const user = users.find(user => user.id === id);

    if(!user) {
        alert("User not found");
        return;
    }
    const confirmDelete = confirm(
        `Are you sure you want to delete ${user.name}?`
    );

    if(!confirmDelete) {
        return;
    }

    users = users.filter(user => user.id !== id);

    localStorage.setItem("users", JSON.stringify(users));

    const searchValue = document.getElementById("searchUser")?.value || "";

    displayUsers(searchValue);

    alert("User deleted successfully!");
}
// Statistics Card

function displayDashboardStats() {

    const totalUsers = document.getElementById("totalUsers");
    const activeUsers = document.getElementById("activateUsers");
    const inactivateUsers = document.getElementById("inactiveUsers");

    if (!totalUsers) return;
    const regularUsers = users.filter(user =>
        user.role === "User"
    );

    const total = regularUsers.length;

    const active = regularUsers.filter(user =>
        user.status === "Active"
    ).length;

    // Calculate inactive users
    const inactive = regularUsers.filter(user =>
        user.status === "Inactive"
    ).length;

    // Update dashboard cards
    totalUsers.textContent = total;
    activeUsers.textContent = active;
    inactivateUsers.textContent = inactive;
}

displayDashboardStats();

// Display Recent Users

function displayRecentUsers() {
    const recentUsersTableBody = document.getElementById("recentUsersTableBody");

    if(!recentUsersTableBody) return;

    const recentUsers = [...users]
        .filter(user => user.role === "User")
        .sort((a, b) => b.id - a.id)
        .slice(0, 5);
        recentUsersTableBody.innerHTML = "";

    if(recentUsers.length === 0) {
        recentUsersTableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center p-6 text-gray-500">
                    No users registered yet.
                </td>
            </tr>
        `;

        return;
    }
    recentUsers.forEach(user => {

        const row = document.createElement("tr");

        row.className = "border-t border-gray-200 hover:bg-gray-50";

        const statusClass = user.status === "Active"
            ? "bg-green-100 text-green-700"
            : "bg-red-100 text-red-700";

        row.innerHTML = `
            <td class="px-6 py-4 font-medium text-gray-800">
                ${escapeHTML(user.name)}
            </td>

            <td class="px-6 py-4 text-gray-600">
                ${escapeHTML(user.email)}
            </td>

            <td class="px-6 py-4 text-gray-600">
                ${escapeHTML(user.role)}
            </td>

            <td class="px-6 py-4">
                <span class="${statusClass} px-3 py-1 rounded-full text-xs font-medium">
                    ${escapeHTML(user.status)}
                </span>
            </td>
        `;

        recentUsersTableBody.appendChild(row);

    });
}

displayRecentUsers();

