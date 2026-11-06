<?php

require_once "Database.php";

class Ticket extends Database 
{
    private $conn;

    public function __construct()
    {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    public function addTickets($userId, $category_id, $subject, $description, $priority)
    {
        $sql = "INSERT INTO tickets
                (user_id, category_id, subject, description, priority)
                VALUES
                (:user_id, :category_id, :subject, :description, :priority)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'user_id' => $user_id,
            ':category_id' => $category_id,
            ':subject' => $subject,
            ':description' => $decription,
            ':priority' => $priority
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getTickets()
    {
        $sql = "SELECT * FROM tickets ORDER BY ticket_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketsById($id)
    {
        $sql = "SELECT * FROM tickets
                WHERE ticket_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTicket($id, $subject, $description, $category_id, $priority)
    {
        $sql = "UPDATE tickets
                SET subject = :subject,
                    description = :description,
                    categort_id = :category_id,
                    priority = :priority,
                    updated_at = CURRENT_TIMESTAMP
                WHERE ticket_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':subject' => $subject,
            ':description' => $description,
            ':category_id' => $category_id,
            ':priority' => $priority,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function deleteTicket($id)
    {
        $sql = "DELETE FROM tickets
                WHERE ticket_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' =>$id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function assignTechnician($ticket_id, $technician_id)
    {
        $sql = "UPDATE tickets
                SET assigned_to = :technician_id,
                    status = 'assigned',
                    updated_at = CURRENT_TIMESTAMP
                WHERE ticket_id = :ticket_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':technician_id' => $technician_id,
            ':ticket_id' => $ticket_id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function updateStatus($ticket_id, $status)
    {
        $sql = "UPDATE tickets 
                SET status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE ticket_id = :ticket_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':status' => $status,
            ':ticket_id' => $ticket_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getTicketDetails()
    {
        $sql = "SELECT
                    t.ticket_id,
                    t.subject,
                    t.priority,
                    t.status,
                    u.name AS employee_name,
                    c.category_name,
                    tech.name AS technician_name
                FROM tickets t
                INNER JOIN users u
                    ON t.user_id = u.user_id
                INNER JOIN categories c
                    ON t.category_id = c.category_id
                LEFT JOIN users tech
                    ON t.assigned_to = tech.user_id
                ORDER BY t.ticket_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}