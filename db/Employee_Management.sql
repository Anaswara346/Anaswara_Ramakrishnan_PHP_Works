CREATE DATABASE employee_db;

use employee_db;

CREATE TABLE department (department_id INT AUTO_INCREMENT PRIMARY KEY, department_name VARCHAR(100) NOT NULL UNIQUE);

SHOW TABLES;

CREATE TABLE employee (employee_id INT AUTO_INCREMENT PRIMARY KEY, employee_name VARCHAR(100) NOT NULL, email VARCHAR(50), phone VARCHAR(10), joining_date DATE, department_id INT, FOREIGN KEY (department_id) REFERENCES department(department_id));

SHOW TABLE STATUS;

CREATE TABLE project (project_id INT AUTO_INCREMENT PRIMARY KEY, project_name VARCHAR(100) NOT NULL UNIQUE, employee_id INT, FOREIGN KEY(employee_id) REFERENCES employee(employee_id));

INSERT INTO department (department_name) VALUES('HR'),('IT'), ('Marketing'),('Sales'),('Finance');

SELECT * FROM department;

INSERT INTO employee (employee_name,email,phone,joining_date,department_id) VALUES ('Anu', 'anu@gmail.com','8391830193','2025-02-13',1);

SELECT * FROM employee;

DELETE FROM employee WHERE employee_id =2;

INSERT INTO employee (employee_name,email,phone,joining_date,department_id) VALUES ('Nayana', 'nayana@gmail.com', '8723456987','2025-05,15',2),('Ichigo Kurosaki', 'ichigo@gmail.com','7830294958','2024-02-08',3),('Itadori Yuji','yuji@gmail.com','7820384920','2025-08-13',4),('Roronoa Zoro','zoro@gmail.com','9873456792','2026-07-19',5);

INSERT INTO project(project_name,employee_id) VALUES ('Website Development',1),('Mobile Application',3),('Finance Dashboard',4),('Recruitment System', 5),('Full Stack Development',6);

SELECT * FROM project;

DESCRIBE employee;

DESCRIBE department;

DESCRIBE project;

SELECT employee_name,email,employee_id FROM employee WHERE employee_id IN (5,6);

SELECT * FROM employee WHERE employee_name LIKE "%a";

SELECT * FROM employee WHERE employee_name LIKE "a%";

SELECT COUNT(*) FROM employee;

SELECT employee_name, department_name FROM employee INNER JOIN department ON employee.department_id = department.department_id;

SELECT project_name, employee_name FROM project LEFT JOIN employee ON project.employee_id = employee.employee_id;


ALTER TABLE employee ADD COLUMN salary FLOAT;

UPDATE employee SET salary =30000 WHERE employee_id = 1;

SELECT * FROM employee WHERE salary > 36000;

CREATE TABLE attendance (attendance_id INT AUTO_INCREMENT PRIMARY KEY, employee_id INT NOT NULL, attendance_date DATE NOT NULL,check_in DATETIME,check_out DATETIME,
FOREIGN KEY (employee_id) REFERENCES employee(employee_id), UNIQUE(employee_id, attendance_date));

-- Migration for databases where attendance was created with date-only columns.
ALTER TABLE attendance
    MODIFY COLUMN check_in DATETIME NULL,
    MODIFY COLUMN check_out DATETIME NULL;

SELECT * FROM attendance;


