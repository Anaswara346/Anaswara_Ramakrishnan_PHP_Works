
<?php

class Student
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get available courses
    public function viewCourses()
    {
        $sql = "SELECT c.course_id, c.course_name, c.course_code,
                       d.department_name
                FROM courses c
                LEFT JOIN department d
                    ON c.department_id = d.department_id
                ORDER BY c.course_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find one student by ID
    public function getStudentById($id)
    {
        $sql = "SELECT * FROM student WHERE student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a student
    public function addStudent(
        $name,
        $email,
        $phone,
        $dob,
        $admissionDate,
        $courseId
    ) {
        $sql = "INSERT INTO student
                (student_name, email, phone, date_of_birth,
                 admission_date, course_id)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $email,
            $phone,
            $dob,
            $admissionDate,
            $courseId
        ]);
    }

    // View all students with course and department names
    public function viewStudents()
    {
        $sql = "SELECT s.student_id, s.student_name, s.email,
                       s.phone, s.date_of_birth, s.admission_date,
                       c.course_name, d.department_name
                FROM student s
                LEFT JOIN courses c
                    ON s.course_id = c.course_id
                LEFT JOIN department d
                    ON c.department_id = d.department_id
                ORDER BY s.student_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search by ID, name, email, or phone
    
public function searchStudent($keyword)
{
    $keyword = trim($keyword);

    // If the input contains only digits, search by exact student ID
    if (ctype_digit($keyword)) {

        $sql = "SELECT s.student_id, s.student_name, s.email,
                       s.phone, s.date_of_birth, s.admission_date,
                       c.course_name, d.department_name
                FROM student s
                LEFT JOIN courses c
                    ON s.course_id = c.course_id
                LEFT JOIN department d
                    ON c.department_id = d.department_id
                WHERE s.student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$keyword]);

    } else {

        // If the input is text, search by name, email, or phone
        $sql = "SELECT s.student_id, s.student_name, s.email,
                       s.phone, s.date_of_birth, s.admission_date,
                       c.course_name, d.department_name
                FROM student s
                LEFT JOIN courses c
                    ON s.course_id = c.course_id
                LEFT JOIN department d
                    ON c.department_id = d.department_id
                WHERE s.student_name LIKE ?
                   OR s.email LIKE ?
                   OR s.phone LIKE ?
                ORDER BY s.student_id";

        $search = "%" . $keyword . "%";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$search, $search, $search]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Update a student's details
    public function updateStudent(
        $id,
        $name,
        $email,
        $phone,
        $dob,
        $admissionDate,
        $courseId
    ) {
        $sql = "UPDATE student
                SET student_name = ?,
                    email = ?,
                    phone = ?,
                    date_of_birth = ?,
                    admission_date = ?,
                    course_id = ?
                WHERE student_id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $email,
            $phone,
            $dob,
            $admissionDate,
            $courseId,
            $id
        ]);
    }

    // Delete a student
    public function deleteStudent($id)
    {
        $sql = "DELETE FROM student WHERE student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }
}
