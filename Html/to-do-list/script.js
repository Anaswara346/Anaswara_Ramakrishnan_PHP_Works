const taskinput = document.getElementById("taskinput");
const addBtn = document.getElementById("addBtn");
const tasklist = document.getElementById("tasklist");

addBtn.addEventListener("click", function(){
    const task = taskinput.value.trim();

    if (task === "") {
        alert("Please enter a task");
        return;
    }

    const li = document.createElement("li");

    const taskText = document.createElement("span");
    taskText.textContent = task;

    // edit button
    const editBtn = document.createElement("button");
    editBtn.textContent = "Edit";
    // editBtn.type = "button";

    // delete button
    const dltBtn = document.createElement("button");
    dltBtn.textContent = "Delete";
    // dltBtn.type = "button";

    // Adding buttons to list item
    li.appendChild(taskText);
    li.appendChild(editBtn);
    li.appendChild(dltBtn);

    // add item to the task list
    tasklist.appendChild(li);

    taskinput.value = "";
    taskinput.focus();

    dltBtn.addEventListener("click", function(){
        const confirmDelete = confirm("Do you want to delete this task?");
        if (confirmDelete) {
            li.remove();
            alert("Task deleted successfully");
        }
    });

    // editBtn.addEventListener("click", function(){
    //     const updatedTask = prompt("Edit your task: ", taskText.textContent);

    //     if (updatedTask !== null && updatedTask.trim() !== "") {
    //         taskText.textContent = updatedTask.trim();
    //     }
    // });
    editBtn.addEventListener("click", function(){
        const editInput = document.createElement("input");

        editInput.type = "text";
        editInput.value = taskText.textContent;

        const saveBtn = document.createElement("button");
        saveBtn.textContent = "Save";

        li.replaceChild(editInput, taskText);
        li.replaceChild(saveBtn, editBtn);

        saveBtn.addEventListener("click", function() {
            const updatedTask = editInput.value.trim();

            if (updatedTask !== "") {
                taskText.textContent = updatedTask;

                li.replaceChild(taskText, editInput);
                li.replaceChild(editBtn, saveBtn);
            } else {
                alert("Task cannot be empty");
            }
        });
    });
    taskinput.value = "";
});