<?php

$json = file_get_contents("to-do-list.json");

if ($json === false || trim($json) === "") {
    $todo = [];
} else {
    $todo = json_decode($json, true);

    if (!is_array($todo)) {
        echo "Invalid to-do data found. Resetting list.\n";
        $todo = [];
    }
}

while (true) {
    echo "\n====== TO-DO-LIST ======\n";
    echo "1. Add Task\n";
    echo "2. View Task\n";
    echo "3. Edit Task\n";
    echo "4. Complete Task\n";
    echo "5. Delete Task\n";
    echo "6. Exit\n";

    $choice = readline("Enter your choice: ");

    if ($choice === false || trim($choice) === "") {
        echo "Choice cannot be empty.\n";
        continue;
    }

    $choice = (int) $choice;

    if ($choice < 1 || $choice > 6) {
        echo "Invalid choice. Please try again.\n";
        continue;
    }

    switch ($choice) {
        case 1:
            while (true) {
                $taskName = readline("Enter your task name : ");

                if ($taskName === false) {
                    echo "Task cannot be empty\n";
                    continue;
                }

                $taskName = trim($taskName);

                if ($taskName === "") {
                    echo "Task cannot be empty\n";
                    continue;
                }

                if (strlen($taskName) > 200) {
                    echo "Task name is too long. Maximum 200 characters.\n";
                    continue;
                }

                $duplicate = false;
                foreach ($todo as $item) {
                    if (isset($item["task"]) && strtolower(trim($item["task"])) === strtolower($taskName)) {
                        $duplicate = true;
                        break;
                    }
                }

                if ($duplicate) {
                    echo "This task already exists.\n";
                    continue;
                }

                break;
            }

            $id = 1;
            foreach ($todo as $item) {
                if (isset($item["id"])) {
                    $id = max($id, (int) $item["id"] + 1);
                }
            }

            $task = [
                "id" => $id,
                "task" => $taskName,
                "completed" => false,
            ];

            $todo[] = $task;
            echo "Task added successfully\n";
            break;

        case 2:
            echo "\n Display Tasks \n";

            if (empty($todo)) {
                echo "No tasks found.\n";
                break;
            }

            foreach ($todo as $item) {
                echo $item["id"] . "." . $item["task"];

                if ($item["completed"] === true) {
                    echo "  - Completed\n";
                } else {
                    echo "  - Pending\n";
                }

                echo "\n";
            }
            break;

        case 3:
            $idInput = readline("Enter the task ID to edit : ");
            if ($idInput === false || trim($idInput) === "") {
                echo "Task ID cannot be empty.\n";
                break;
            }

            $id = (int) $idInput;
            if ($id <= 0) {
                echo "Invalid task ID.\n";
                break;
            }

            $newTask = readline("Enter the new task : ");
            if ($newTask === false) {
                echo "Task cannot be empty.\n";
                break;
            }

            $newTask = trim($newTask);
            if ($newTask === "") {
                echo "Task cannot be empty.\n";
                break;
            }

            if (strlen($newTask) > 200) {
                echo "Task name is too long. Maximum 200 characters.\n";
                break;
            }

            $found = false;
            foreach ($todo as $index => $item) {
                if ($item["id"] === $id) {
                    $todo[$index]["task"] = $newTask;
                    echo "Task updated successfully.\n";
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                echo "Task ID not found.\n";
            }
            break;

        case 4:
            $idInput = readline("Enter the task ID to complete : ");
            if ($idInput === false || trim($idInput) === "") {
                echo "Task ID cannot be empty.\n";
                break;
            }

            $id = (int) $idInput;
            if ($id <= 0) {
                echo "Invalid task ID.\n";
                break;
            }

            $found = false;
            foreach ($todo as $index => $item) {
                if ($item["id"] === $id) {
                    $found = true;

                    if ($item["completed"] === true) {
                        echo "Task is already completed.\n";
                    } else {
                        $todo[$index]["completed"] = true;
                        echo "Task completed successfully\n";
                    }
                    break;
                }
            }

            if (!$found) {
                echo "Task ID not found.\n";
            }
            break;

        case 5:
            $idInput = readline("Enter the task ID to delete: ");
            if ($idInput === false || trim($idInput) === "") {
                echo "Task ID cannot be empty.\n";
                break;
            }

            $id = (int) $idInput;
            if ($id <= 0) {
                echo "Invalid task ID.\n";
                break;
            }

            $found = false;
            foreach ($todo as $index => $item) {
                if ($item["id"] === $id) {
                    unset($todo[$index]);
                    $todo = array_values($todo);
                    echo "Task deleted successfully.\n";
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                echo "Task ID not found.\n";
            }
            break;

        case 6:
            echo "Goodbye!\n";
            break 2;
    }

    $json = json_encode($todo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        echo "Unable to save tasks.\n";
    } else {
        file_put_contents("to-do-list.json", $json);
    }
}




