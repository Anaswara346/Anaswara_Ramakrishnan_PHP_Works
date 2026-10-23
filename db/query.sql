-- CREATE DATABASE employee_db;

-- use employee_db;

-- CREATE TABLE department (department_id INT AUTO_INCREMENT PRIMARY KEY, department_name VARCHAR(100) NOT NULL UNIQUE);

-- SHOW TABLES;

-- CREATE TABLE employee (employee_id INT AUTO_INCREMENT PRIMARY KEY, employee_name VARCHAR(100) NOT NULL, email VARCHAR(50), phone VARCHAR(10), joining_date DATE, department_id INT, FOREIGN KEY (department_id) REFERENCES department(department_id));

-- SHOW TABLE STATUS;

-- CREATE TABLE project (project_id INT AUTO_INCREMENT PRIMARY KEY, project_name VARCHAR(100) NOT NULL UNIQUE, employee_id INT, FOREIGN KEY(employee_id) REFERENCES employee(employee_id));

