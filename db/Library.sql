use library_db;

-- CREATE TABLE user_category (category_id INT AUTO_INCREMENT PRIMARY KEY, category_name VARCHAR(50) NOT NULL UNIQUE, max_items INT not NULL, loan_days INT NOT NULL, late_free_per_day DECIMAL(10,2) NOT NULL);

-- INSERT INTO user_category (category_name, max_items, loan_days, late_free_per_day) VALUES ('Staff', 10, 30, 1.00),('Undergraduate', 3, 14, 2.00),('Graduate Student', 5, 21, 2.00),('Research Student',8, 30, 3.00);

SELECT * FROM user_category;
/*
CREATE TABLE library_user (user_id INT AUTO_INCREMENT PRIMARY KEY, user_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL UNIQUE,phone VARCHAR(15), category_id INT NOT NULL, account_status ENUM('ACTIVE','INACTIVE','SUSPENDED') NOT NULL DEFAULT 'ACTIVE', 
FOREIGN KEY (category_id) REFERENCES user_category(category_id));

CREATE TABLE author (author_id INT AUTO_INCREMENT PRIMARY KEY, author_name VARCHAR(100) NOT NULL, biography TEXT, birth_date DATE, death_date DATE);

self referencing the genre

CREATE TABLE genre (genre_id INT AUTO_INCREMENT PRIMARY KEY, genre_name VARCHAR(100) NOT NULL, parent_genre_id INT NULL, 
FOREIGN KEY (parent_genre_id) REFERENCES genre(genre_id));

CREATE TABLE item (item_id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(200) NOT NULL,publication_year YEAR, isbn VARCHAR(20) UNIQUE, physical_format VARCHAR(50) NOT NULL,genre_id INT,description TEXT,price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
FOREIGN KEY (genre_id) REFERENCES genre(genre_id));

CREATE TABLE item_author (item_id INT NOT NULL, author_id INT NOT NULL, FOREIGN KEY (item_id) REFERENCES item(item_id), FOREIGN KEY (author_id) REFERENCES author(author_id));

CREATE TABLE borrowing (borrowing_id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, item_id INT NOT NULL,borrow_date DATE NOT NULL,due_date DATE NOT NULL, return_date DATE NULL,
FOREIGN KEY (user_id) REFERENCES library_user(user_id),
FOREIGN KEY (item_id) REFERENCES item(item_id));


CREATE TABLE fee (fee_id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,borrowing_id INT NOT NULL,fee_type ENUM('LATE','LOST') NOT NULL,amount DECIMAL(10,2) NOT NULL,fee_date DATE NOT NULL,paid BOOLEAN NOT NULL DEFAULT FALSE,
FOREIGN KEY (user_id) REFERENCES library_user(user_id),
FOREIGN KEY (borrowing_id) REFERENCES borrowing(borrowing_id));

DESCRIBE user_category;
DESCRIBE library_user;
DESCRIBE author;
DESCRIBE genre;
DESCRIBE item;
DESCRIBE item_author;
DESCRIBE borrowing;
DESCRIBE fee;


INSERT INTO author
    (author_name, biography, birth_date, death_date)
VALUES
    ('Arthur Conan Doyle', 'British writer and physician.', '1859-05-22', '1930-07-07'),
    ('Agatha Christie', 'English writer known for detective fiction.', '1890-09-15', '1976-01-12'),
    ('J. K. Rowling', 'British author best known for the Harry Potter series.', '1965-07-31', NULL),
    ('George Orwell', 'English novelist and essayist.', '1903-06-25', '1950-01-21');
*/
SELECT * FROM author;
