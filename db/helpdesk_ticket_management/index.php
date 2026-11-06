
<?php

require_once "User.php";
require_once "Category.php";
require_once "Ticket.php";

$user = new User();
$category = new Category();
$ticket = new Ticket();

function getInput($message)
{
    echo $message;
    return trim(fgets(STDIN));
}


function showData($data)
{
    if (!$data) {
        echo "No records found.\n";
        return;
    }

    foreach ($data as $row) {

        foreach ($row as $key => $value) {

            $label = ucfirst(str_replace('_', ' ', $key));

            echo $label . " : " . ($value ?? 'N/A') . "\n";
        }

        echo "--------------------------------\n";
    }
}



function userMenu($user)
{
    while (true) {

        echo "\n===== USER MANAGEMENT =====\n";
        echo "1. Display Users\n";
        echo "2. Add User\n";
        echo "3. Search User\n";
        echo "4. Update User\n";
        echo "5. Delete User\n";
        echo "6. Display Users by Role\n";
        echo "7. Back to Main Menu\n";

        $choice = getInput("Enter your choice: ");

        if ($choice == 1) {

            $users = $user->getUsers();
            showData($users);

        } elseif ($choice == 2) {

            $name = getInput("Enter name: ");
            $email = getInput("Enter email: ");
            $password = getInput("Enter password: ");
            $role = getInput("Enter role (employee/technician/admin): ");

            try {
                $result = $user->addUser($name, $email, $password, $role);

                echo $result ? "User added successfully!\n" : "Failed to add user.\n";

            } catch (PDOException $e) {
                echo "Error adding user: " . $e->getMessage() . "\n";
            }

        } elseif ($choice == 3) {

            $id = getInput("Enter User ID: ");

            $result = $user->getUserById($id);

            if ($result) {
                showData([$result]);
            } else {
                echo "User not found.\n";
            }

        } elseif ($choice == 4) {

            $id = getInput("Enter User ID to update: ");

            $existingUser = $user->getUserById($id);

            if ($existingUser) {

                echo "Enter new details:\n";

                $name = getInput("Enter name: ");
                $email = getInput("Enter email: ");
                $role = getInput("Enter role (employee/technician/admin): ");

                try {
                    $result = $user->updateUser($id, $name, $email, $role);

                    echo $result ? "User updated successfully!\n" : "No changes made or update failed.\n";

                } catch (PDOException $e) {
                    echo "Error updating user: " . $e->getMessage() . "\n";
                }

            } else {
                echo "User not found.\n";
            }

        } elseif ($choice == 5) {

            $id = getInput("Enter User ID to delete: ");

            try {
                $result = $user->deleteUser($id);

                echo $result ? "User deleted successfully!\n" : "User not found.\n";

            } catch (PDOException $e) {
                echo "Cannot delete this user because related records exist.\n";
            }

        } elseif ($choice == 6) {

            $role = getInput("Enter role (employee/technician/admin) : ");

            $users = $user->getUsersByRole($role);

            showData($users);

        } elseif ($choice == 7) {

            break;

        } else {

            echo "Invalid choice. Try again.\n";
        }
    }
}



function categoryMenu($category)
{
    while (true) {

        echo "\n===== CATEGORY MANAGEMENT =====\n";
        echo "1. Display Categories\n";
        echo "2. Add Category\n";
        echo "3. Search Category\n";
        echo "4. Update Category\n";
        echo "5. Delete Category\n";
        echo "6. Back to Main Menu\n";

        $choice = getInput("Enter your choice: ");

        if ($choice == 1) {

            $categories = $category->getCategories();
            showData($categories);

        } elseif ($choice == 2) {

            $name = getInput("Enter category name: ");
            $description = getInput("Enter description: ");

            $result = $category->addCategory($name, $description);

            echo $result . "\n";

        } elseif ($choice == 3) {

            $id = getInput("Enter Category ID: ");

            $result = $category->getCategoryById($id);

            if ($result) {
                showData([$result]);
            } else {
                echo "Category not found.\n";
            }

        } elseif ($choice == 4) {

            $id = getInput("Enter Category ID to update: ");

            $existingCategory = $category->getCategoryById($id);

            if ($existingCategory) {

                $name = getInput("Enter new category name: ");
                $description = getInput("Enter new description: ");

                try {
                    $result = $category->updateCategory($id, $name, $description);

                    echo $result ? "Category updated successfully!\n" : "No changes made or update failed.\n";

                } catch (PDOException $e) {
                    echo "Error updating category: " . $e->getMessage() . "\n";
                }

            } else {
                echo "Category not found.\n";
            }

        } elseif ($choice == 5) {

            $id = getInput("Enter Category ID to delete: ");

            try {
                $result = $category->deleteCategory($id);

                echo $result ? "Category deleted successfully!\n" : "Category not found.\n";

            } catch (PDOException $e) {
                echo "Cannot delete this category because tickets are associated with it.\n";
            }

        } elseif ($choice == 6) {

            break;

        } else {

            echo "Invalid choice. Try again.\n";
        }
    }
}


function ticketMenu($ticket)
{
    while (true) {

        echo "\n===== TICKET MANAGEMENT =====\n";
        echo "1. Create Ticket\n";
        echo "2. Display Tickets\n";
        echo "3. Search Ticket\n";
        echo "4. Update Ticket\n";
        echo "5. Delete Ticket\n";
        echo "6. Back to Main Menu\n";

        $choice = getInput("Enter your choice: ");

        if ($choice == 1) {

            $user_id = getInput("Enter Employee/User ID: ");
            $category_id = getInput("Enter Category ID: ");
            $subject = getInput("Enter ticket subject: ");
            $description = getInput("Enter description: ");
            $priority = getInput("Enter priority (low/medium/high): ");

            try {
                $result = $ticket->addTicket(
                    $user_id,
                    $category_id,
                    $subject,
                    $description,
                    $priority
                );

                echo $result ? "Ticket created successfully!\n" : "Failed to create ticket.\n";

            } catch (PDOException $e) {
                echo "Error creating ticket: " . $e->getMessage() . "\n";
            }

        } elseif ($choice == 2) {

            $tickets = $ticket->getTickets();
            showData($tickets);

        } elseif ($choice == 3) {

            $id = getInput("Enter Ticket ID: ");

            $result = $ticket->getTicketById($id);

            if ($result) {
                showData([$result]);
            } else {
                echo "Ticket not found.\n";
            }

        } elseif ($choice == 4) {

            $id = getInput("Enter Ticket ID to update: ");

            $existingTicket = $ticket->getTicketById($id);

            if ($existingTicket) {

                $subject = getInput("Enter new subject: ");
                $description = getInput("Enter new description: ");
                $category_id = getInput("Enter new Category ID: ");
                $priority = getInput("Enter new priority (low/medium/high): ");

                try {
                    $result = $ticket->updateTicket(
                        $id,
                        $subject,
                        $description,
                        $category_id,
                        $priority
                    );

                    echo $result ? "Ticket updated successfully!\n" : "No changes made or update failed.\n";

                } catch (PDOException $e) {
                    echo "Error updating ticket: " . $e->getMessage() . "\n";
                }

            } else {
                echo "Ticket not found.\n";
            }

        } elseif ($choice == 5) {

            $id = getInput("Enter Ticket ID to delete: ");

            try {
                $result = $ticket->deleteTicket($id);

                echo $result ? "Ticket deleted successfully!\n" : "Ticket not found.\n";

            } catch (PDOException $e) {
                echo "Cannot delete this ticket because related records exist.\n";
            }

        } elseif ($choice == 6) {

            break;

        } else {

            echo "Invalid choice. Try again.\n";
        }
    }
}

function assignTechnicianMenu($ticket)
{
    $ticket_id = getInput("Enter Ticket ID: ");
    $technician_id = getInput("Enter Technician User ID: ");

    try {
        $result = $ticket->assignTechnician($ticket_id, $technician_id);

        echo $result ? "Technician assigned successfully!\n" : "Assignment failed.\n";

    } catch (PDOException $e) {
        echo "Error assigning technician: " . $e->getMessage() . "\n";
    }
}

function updateTicketStatusMenu($ticket)
{
    $ticket_id = getInput("Enter Ticket ID: ");

    echo "\nAvailable Statuses:\n";
    echo "1. assigned\n";
    echo "2. in_progress\n";
    echo "3. resolved\n";
    echo "4. closed\n";

    $choice = getInput("Choose status: ");

    if ($choice == 1) {
        $status = "assigned";
    } elseif ($choice == 2) {
        $status = "in_progress";
    } elseif ($choice == 3) {
        $status = "resolved";
    } elseif ($choice == 4) {
        $status = "closed";
    } else {
        echo "Invalid status choice.\n";
        return;
    }

    try {
        $result = $ticket->updateStatus($ticket_id, $status);

        echo $result ? "Ticket status updated successfully!\n" : "Ticket not found or status unchanged.\n";

    } catch (PDOException $e) {
        echo "Error updating ticket status: " . $e->getMessage() . "\n";
    }
}


while (true) {

    echo "\n====================================\n";
    echo "   HELPDESK TICKET MANAGEMENT\n";
    echo "====================================\n";

    echo "1. User Management\n";
    echo "2. Category Management\n";
    echo "3. Ticket Management\n";
    echo "4. Display Ticket Details\n";
    echo "5. Assign Technician\n";
    echo "6. Update Ticket Status\n";
    echo "7. Exit\n";

    $choice = getInput("Enter your choice: ");

    if ($choice == 1) {

        userMenu($user);

    } elseif ($choice == 2) {

        categoryMenu($category);

    } elseif ($choice == 3) {

        ticketMenu($ticket);

    } elseif ($choice == 4) {

        $details = $ticket->getTicketDetails();
        showData($details);

    } elseif ($choice == 5) {

        assignTechnicianMenu($ticket);

    } elseif ($choice == 6) {

        updateTicketStatusMenu($ticket);

    } elseif ($choice ==7) {

        echo "Thank you for using Helpdesk Ticket Management System!\n";
        break;

    } else {

        echo "Invalid choice. Please try again.\n";
    }
}

?>
