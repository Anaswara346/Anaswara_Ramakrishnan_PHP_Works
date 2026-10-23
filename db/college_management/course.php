<?php

class Course
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function viewDepartments()
    {
        $sql = "SELECT department_id,department_name
                FROM department 
                ORDER BY department_id";

        $stmt = $this->conn->prepare($sql);  //preoare returns the PDO Statement Object which allow to work with the query

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);   //to fetch the data ASSOC - Associative array

    }

    //Add Course

    public function addCourse($name, $code, $departmentId)
    {
        try {
            $sql = "INSERT INTO courses(course_name, course_code, department_id) VALUES (?,?,?)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $name,
                $code,
                $departmentId
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // View All courses

    public function viewCourses()
    {
        $sql = "SELECT c.course_id, c.course_name,c.course_code, d.department_id,d.department_name FROM courses c LEFT JOIN department d ON c.department_id = d.department_id ORDER BY c.course_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get the course by id

    public function getCourseById($id)
    {
        $sql = "SELECT
                    c.course_id,
                    c.course_name,
                    c.course_code,
                    c.department_id,
                    d.department_name
                    FROM courses c
                    LEFT JOIN department d
                       ON c.department_id = d.department_id
                    WHERE c.course_id = ?";
                
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //search course

    public function searchCourse($type, $keyword)
    {

            if ($type == "id") {
                $sql = "SELECT
                            c.course_id,
                            c.course_name,
                            c.course_code,
                            d.department_name
                            FROM courses c
                            LEFT JOIN department d
                                ON c.department_id = d.department_id
                            WHERE c.course_id = ?";
                
                $stmt = $this->conn->prepare($sql);

                $stmt->execute([$keyword]);
            }

            elseif ($type == "name") {

                $sql = "SELECT 
                        c.course_id,
                        c.course_name,
                        c.course_code,
                        d.department_name
                        FROM courses c
                        LEFT JOIN department d
                            ON c.department_id = d.department_id
                        WHERE c.course_name LIKE ?";
            
                $stmt = $this->conn->prepare($sql);

                $stmt->execute(["%" . $keyword . "%"]);
            }

            elseif ($type == "code")  {
                $sql = "SELECT 
                            c.course_id,
                            c.course_name,
                            c.course_code,
                            d.department_name
                        FROM courses c
                        LEFT JOIN department d
                            On c.department_id = d.department_id
                        WHERE c.course_code = ?";

                $stmt = $this->conn->prepare([$sql]);

                $stmt->execute([$keyword]);
            }

            else {
                return [];
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //update the  course

    public function updateCourse($id, $name, $code, $departmentId)
    {
        try {
            $sql = "UPDATE courses 
                        SET course_name = ?,
                            course_code = ?,
                            department_id = ?
                        WHERE course_id = ?";
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $name,
                $code,
                $departmentId,
                $id
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Check Students

    public function checkStudents($id)
    {

        $sql = "SELECT COUNT(*) AS total FROM student WHERE course_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }

    // Delete the course

    public function deleteCourse($id)
    {
        try {

            $sql = "DELETE FROM courses WHERE course_id = ?";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([$id]);
            return true;
        }  catch (PDOException $e) {
            return false;
        }
    }
}

?>