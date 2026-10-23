<?php

class Department
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }


    public function addDepartment($name)
    {
        try {

            $sql = "INSERT INTO department
                    (department_name)
                    VALUES (?)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $name
            ]);

            return true;

        } catch (PDOException $e) {

            return false;
        }
    }


 
    public function viewDepartments()
    {
        $sql = "SELECT
                    department_id,
                    department_name
                FROM department
                ORDER BY department_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartmentById($id)
    {
        $sql = "SELECT
                    department_id,
                    department_name
                FROM department
                WHERE department_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function searchDepartment($type, $keyword)
    {

        if ($type == "id") {

            $sql = "SELECT
                        department_id,
                        department_name
                    FROM department
                    WHERE department_id = ?";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $keyword
            ]);
        }


        elseif ($type == "name") {

            $sql = "SELECT
                        department_id,
                        department_name
                    FROM department
                    WHERE department_name LIKE ?";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                "%" . $keyword . "%"
            ]);
        }


        else {

            return [];
        }


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function updateDepartment($id, $name)
    {
        try {

            $sql = "UPDATE department
                    SET department_name = ?
                    WHERE department_id = ?";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $name,
                $id
            ]);

            return true;

        } catch (PDOException $e) {

            return false;
        }
    }

    public function hasCourses($id)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM courses
                WHERE department_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            $id
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }


   

    public function deleteDepartment($id)
    {
        try {

            $sql = "DELETE FROM department
                    WHERE department_id = ?";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $id
            ]);

            return true;

        } catch (PDOException $e) {

            return false;
        }
    }
}

?>