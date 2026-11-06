CREATE TABLE users (user_id SERIAL PRIMARY KEY,
	name VARCHAR(100) NOT NULL,email VARCHAR(120) UNIQUE NOT NULL,
	password VARCHAR(50) NOT NULL, role VARCHAR(100) NOT NULL
	CHECK(role IN ('employee', 'technician', 'admin')), 
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP); 

INSERT INTO users (name,email,password,role)
VALUES
('Rahul','rahul@esample.com','pass1word','employee'),
('Anu','anu@example.com','pass2word','technician'), 
('Admin','admin@gmail.com','pass3word','admin');  

CREATE TABLE categories (category_id SERIAL PRIMARY KEY,
	category_name VARCHAR(100) UNIQUE NOT NULL,
	description TEXT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

INSERT INTO categories (category_name, description)
VALUES
('Hardware', 'Computer and physical equipment problems'),
('Software', 'Software installation and application errors'),
('Network', 'Internet and network connectivity problems'),
('Account', 'Login and password-related issues'),
('Other', 'Other technical issues');


SELECT * FROM categories;


CREATE TABLE tickets (
	ticket_id SERIAL PRIMARY KEY,
	user_id INTEGER NOT NULL,
	category_id INTEGER NOT NULL,
	assigned_to INTEGER,
	subject VARCHAR(150) NOT NULL,
	description TEXT NOT NULL,
	priority VARCHAR(10) NOT NULL
        CHECK (priority IN ('low', 'medium', 'high')),

    status VARCHAR(20) NOT NULL DEFAULT 'open'
        CHECK (status IN ('open', 'assigned', 'in_progress', 'resolved', 'closed')),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ticket_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id),

    CONSTRAINT fk_ticket_category
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id),

    CONSTRAINT fk_ticket_technician
        FOREIGN KEY (assigned_to)
        REFERENCES users(user_id)
);



SELECT * FROM tickets;

INSERT INTO tickets
(user_id, category_id, subject, description, priority)
VALUES
(1,3,'Wi-Fi not connecting',
	'Unable to connect to the office Wi-Fi network.',
	'high'),
(1,1, 'Keyboard not working',
	'Some keyboard keys are not responding',
	'medium'),
(2,2, 'Software installation issue',
	'Unable to install the required software.',
	'low');



SELECT ticket_id,subject,priority,status, created_at FROM tickets;


CREATE TABLE ticket_comments (
	comment_id SERIAL PRIMARY KEY,
	ticket_id INTEGER NOT NULL,
	user_id INTEGER NOT NULL,
	comment TEXT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY(ticket_id)
		REFERENCES users(user_id)
);

CREATE TABLE ticket_history(
	history_id SERIAL PRIMARY KEY,
	ticket_id INTEGER NOT NULL,
	changed_by INTEGER NOT NULL,
	old_status VARCHAR(20),
	new_status VARCHAR(20) NOT NULL,
	changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (ticket_id)
		REFERENCES tickets(ticket_id),
	FOREIGN KEY (changed_by)
		REFERENCES users(user_id)
); 

INSERT INTO ticket_comments (ticket_id, user_id, comment)
VALUES
(1, 2, 'Checking the network connection.'),
(1, 2, 'Router Restarted.Please check again.'),
(2, 1, 'The keyboard issue is still present.'),
(3, 2, 'Software installation completed successfully.');

SELECT * FROM ticket_comments;

INSERT INTO ticket_history
(ticket_id, changed_by, old_status, new_status)
VALUES
(1, 1, NULL, 'open'),
(1, 2, 'open', 'in_progress'),
(1, 2, 'in_progress', 'resolved'),
(2, 1, NULL, 'open'),
(3, 1, NULL, 'open'),
(3, 2, 'open', 'resolved');


SELECT * FROM ticket_history;

SELECT tickets.ticket_id,
		tickets.subject,
		users.name AS employee_name,
		categories.category_name
FROM tickets
INNER JOIN users
	ON tickets.user_id = users.user_id
INNER JOIN categories
	ON tickets.category_id = categories.category_id;


SELECT users.user_id,
	users.name AS employee_name,
	tickets.ticket_id,
	tickets.subject,
	tickets.status
FROM users
LEFT JOIN tickets
	ON users.user_id = tickets.user_id;


SELECT tickets.ticket_id,
		tickets.subject,
		tickets.assigned_to,
		users.name AS technician
FROM users
RIGHT JOIN tickets
ON users.user_id = tickets.assigned_to;

SELECT users.user_id,
		users.name AS employee_name,
		tickets.ticket_id,
		tickets.subject
FROM users
FULL OUTER JOIN tickets
	ON users.user_id = tickets.user_id;

UPDATE tickets 
SET assigned_to = 2,
	status = 'assigned',
	updated_at = CURRENT_TIMESTAMP
	WHERE ticket_id = 1;

SELECT * FROM tickets;


SELECT t.ticket_id,
		t.subject,
		t.status,
		u.name AS technician_name
FROM tickets t
INNER JOIN users u 
	ON t.assigned_to = u.user_id;


UPDATE tickets 
SET status = 'in_progress',
	updated_at = CURRENT_TIMESTAMP
WHERE ticket_id = 1;


UPDATE tickets
SET status = 'resolved',
	updated_at = CURRENT_TIMESTAMP
WHERE ticket_id = 1; 

INSERT INTO ticket_history
	(ticket_id, changed_by, old_status, new_status)
VALUES
	(1, 2, 'assigned', 'in_progress'),
	(1, 2, 'in_progress', 'resolved');

SELECT * FROM ticket_history;

SELECT * FROM tickets;

SELECT * FROM tickets
WHERE ticket_id = 1;

SELECT * FROM tickets 
WHERE subject ILIKE '%s%';

SELECT * FROM tickets;

SELECT * FROM tickets 
WHERE priority = 'medium';

SELECT * FROM tickets WHERE status = 'open';

SELECT * FROM tickets WHERE status IN ('open','in_progress');

SELECT *
FROM tickets
WHERE status != 'closed';

SELECT * FROM tickets ORDER BY created_at DESC;

SELECT * FROM tickets ORDER BY 
	CASE priority
		WHEN 'high' THEN 1
		WHEN 'medium' THEN 2
		WHEN 'low' THEN 3
	END;

SELECT COUNT(*) AS total_tickets
FROM tickets;

SELECT status,
		COUNT(*) AS total
FROM tickets
GROUP BY status;

SELECT
    priority,
    COUNT(*) AS total
FROM tickets
GROUP BY priority;

SELECT c.category_name,
		COUNT(t.ticket_id) AS total_tickets
FROM categories c
INNER JOIN tickets t
	ON c.category_id = t.category_id
GROUP BY c.category_id, c.category_name
HAVING COUNT(t.ticket_id) > 1;


SELECT * FROM tickets
WHERE user_id IN (
	SELECT user_id
	FROM tickets
	GROUP BY user_id
	HAVING COUNT(*) >= 2
);

SELECT ticket_id,
		subject,
		priority,
		CASE
			WHEN priority = 'high' THEN 'Urgent Attention'
			WHEN priority = 'medium' THEN 'Normal Attention'
			WHEN priority = 'low' THEN 'Low Attention'
		END AS priority_description
FROM tickets;

SELECT * FROM tickets;

SELECT COUNT(*) AS total_tickets
FROM tickets;

SELECT MIN(created_at) AS first_ticket
FROM tickets;

SELECT MAX(created_at) AS latest_ticket FROM tickets;

SELECT AVG(ticket_count) AS average_tickets
FROM (
	SELECT COUNT(*) AS ticket_count
	FROM tickets
	GROUP BY user_id
) AS employee_tickets;

CREATE VIEW ticket_details AS 
SELECT 
	t.ticket_id,
	t.subject,
	t.description,
	t.priority,
	t.status,
	u.name AS employee_name,
	c.category_name
FROM tickets t
INNER JOIN users u
	ON t.user_id = u.user_id
INNER JOIN categories c
	ON t.category_id = c.category_id; 

SELECT * FROM ticket_details;

CREATE INDEX idx_tickets_status
ON tickets(status);

CREATE INDEX idx_tickets_priority
ON tickets(priority);  

SELECT indexname, indexdef FROM pg_indexes 
WHERE tablename = 'tickets';  

BEGIN;

UPDATE tickets 
SET status = 'closed',
	updated_at = CURRENT_TIMESTAMP
WHERE ticket_id = 1;

INSERT INTO ticket_history 
(ticket_id, changed_by, old_status, new_status)
VALUES
(1, 2, 'resolved','closed'); 

COMMIT;

