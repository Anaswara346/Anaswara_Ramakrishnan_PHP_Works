let savedUsers = localStorage.getItem("users");
let users = savedUsers ? JSON.parse(savedUsers) : [];

if (!savedUsers) {
    localStorage.setItem("users", JSON.stringify(users));
}

let userName = document.getElementById("userName");
let userEmail = document.getElementById("userEmail");
let userRole = document.getElementById("userRole");
let saveBtn = document.getElementById("saveUser");
let userTable = document.getElementById("userTable");
let userForm = document.getElementById("userForm");
let userModal = document.getElementById("userModal");
let userModalTitle = document.getElementById("userModalTitle");
let deleteUserModal = document.getElementById("deleteUserModal");
let deleteUserMessage = document.getElementById("deleteUserMessage");
let confirmDeleteUserBtn = document.getElementById("confirmDeleteUser");
let editingUserId = null;
let pendingDeleteUserId = null;

function getUserRole(user) {
    let supportedRoles = ["Admin", "Teacher", "Student"];
    return supportedRoles.includes(user.role) ? user.role : "Student";
}

function updateDashboard() {
    document.getElementById("totalUsers").textContent = users.length;

    let roleCounts = {
        Admin: 0,
        Teacher: 0,
        Student: 0
    };

    users.forEach(function(user) {
        roleCounts[getUserRole(user)] += 1;
    });

    document.getElementById("adminCount").textContent = roleCounts.Admin;
    document.getElementById("teacherCount").textContent = roleCounts.Teacher;
    document.getElementById("studentCount").textContent = roleCounts.Student;

    if (users.length === 0) {
        document.getElementById("latestUser").textContent = "No users yet";
        return;
    }

    let latestUser = users.reduce(function(latest, user) {
        return Number(user.id) > Number(latest.id) ? user : latest;
    }, users[0]);

    document.getElementById("latestUser").textContent = latestUser.name;
}

function showSuccessMessage() {
    let successMessage = document.getElementById("successMessage");
    successMessage.classList.remove("d-none");

    setTimeout(function() {
        successMessage.classList.add("d-none");
    }, 3000);
}

function displayUsers() {
    userTable.innerHTML = "";
    updateDashboard();

    if (users.length === 0) {
        let emptyRow = document.createElement("tr");
        let emptyCell = document.createElement("td");
        emptyCell.colSpan = 4;
        emptyCell.className = "empty-state";
        emptyCell.textContent = "No users yet. Add a user to get started.";
        emptyRow.appendChild(emptyCell);
        userTable.appendChild(emptyRow);
        return;
    }

    users.forEach(function(user) {
        let newRow = document.createElement("tr");
         let nameCell = document.createElement("td");
        let emailCell = document.createElement("td");
        let roleCell = document.createElement("td");
        let actionCell = document.createElement("td");
        let editBtn = document.createElement("button");
        let deleteBtn = document.createElement("button");
        let roleBadge = document.createElement("span");
        let role = getUserRole(user);

        nameCell.textContent = user.name;
        emailCell.textContent = user.email;
        roleBadge.textContent = role;
        roleBadge.className = "badge " + (
            role === "Admin" ? "bg-purple-lt" :
            role === "Teacher" ? "bg-blue-lt" :
            "bg-green-lt"
        );
        roleCell.appendChild(roleBadge);
        actionCell.className = "text-end";

        editBtn.type = "button";
        editBtn.textContent = "Edit";
        editBtn.className = "btn btn-outline-primary btn-sm me-2";
        editBtn.addEventListener("click", function() {
            editingUserId = user.id;
            userName.value = user.name;
            userEmail.value = user.email;
            userRole.value = role;
            userModalTitle.textContent = "Edit User";
            saveBtn.textContent = "Save Changes";
            bootstrap.Modal.getOrCreateInstance(userModal).show();
        });

        deleteBtn.type = "button";
        deleteBtn.textContent = "Delete";
        deleteBtn.className = "btn btn-outline-danger btn-sm";
        deleteBtn.addEventListener("click", function() {
            pendingDeleteUserId = user.id;
            deleteUserMessage.textContent = "Are you sure you want to delete " + user.name + "?";
            bootstrap.Modal.getOrCreateInstance(deleteUserModal).show();
        });

        actionCell.append(editBtn, deleteBtn);
        newRow.append(nameCell, emailCell, roleCell, actionCell);
        userTable.appendChild(newRow);
    });
}

confirmDeleteUserBtn.addEventListener("click", function() {
    if (pendingDeleteUserId === null) {
        return;
    }

    users = users.filter(function(user) {
        return user.id !== pendingDeleteUserId;
    });
    pendingDeleteUserId = null;
    localStorage.setItem("users", JSON.stringify(users));
    displayUsers();
    bootstrap.Modal.getOrCreateInstance(deleteUserModal).hide();
});

deleteUserModal.addEventListener("hidden.bs.modal", function() {
    pendingDeleteUserId = null;
});

function saveUser(name, email, role, userId) {
    name = name.trim();
    email = email.trim();

    if (name === "" || email === "") {
        alert("Please fill in the name and email fields.");
        return false;
    }

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    let alreadyExists = users.some(function(user) {
        return user.id !== userId && user.email.toLowerCase() === email.toLowerCase();
    });
    if (alreadyExists) {
        alert("This email already exists.");
        return false;
    }

    if (userId === null) {
        users.push({
            id: Date.now(),
            name: name,
            email: email,
            role: role || "Student"
        });
    } else {
        let userToUpdate = users.find(function(user) {
            return user.id === userId;
        });
        if (!userToUpdate) {
            alert("Could not find this user. Please refresh the page and try again.");
            return false;
        }

        userToUpdate.name = name;
        userToUpdate.email = email;
        userToUpdate.role = role || "Student";
    }

    localStorage.setItem("users", JSON.stringify(users));
    displayUsers();
    showSuccessMessage();
    return true;
}

document.getElementById("openAddUser").addEventListener("click", function() {
    editingUserId = null;
    userName.value = "";
    userEmail.value = "";
    userRole.value = "Student";
    userModalTitle.textContent = "Add New User";
    saveBtn.textContent = "Save User";
});

saveBtn.addEventListener("click", function() {
    if (!saveUser(userName.value, userEmail.value, userRole.value, editingUserId)) {
        return;
    }

    userName.value = "";
    userEmail.value = "";
    userRole.value = "Student";
    editingUserId = null;
    bootstrap.Modal.getOrCreateInstance(userModal).hide();
});

userForm.addEventListener("submit", function(event) {
    event.preventDefault();

    let name = document.getElementById("formUserName").value;
    let email = document.getElementById("formUserEmail").value;
    let role = document.getElementById("formUserRole").value;

    if (saveUser(name, email, role, null)) {
        userForm.reset();
    }
});

displayUsers();

